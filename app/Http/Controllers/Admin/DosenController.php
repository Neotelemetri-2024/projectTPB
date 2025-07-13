<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dosen::with('user');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('email', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Filter by status aktif
        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->whereHas('user', function($userQuery) {
                    $userQuery->where('isAktif', true);
                });
            } elseif ($request->status === 'nonaktif') {
                $query->whereHas('user', function($userQuery) {
                    $userQuery->where('isAktif', false);
                });
            }
        }
        
        // Pagination
        $dosen = $query->orderBy('nama')->paginate(10)->withQueryString();
        
        return view('admin.dosen.index', compact('dosen'));
    }

    public function create()
    {
        return view('admin.dosen.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|unique:dosen,nip|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'nama.required' => 'Nama dosen wajib diisi',
            'nip.required' => 'NIP wajib diisi',
            'nip.unique' => 'NIP sudah terdaftar',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create user account
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'dosen',
                'isAktif' => true,
            ]);

            // Create dosen record
            $dosen = Dosen::create([
                'userId' => $user->id,
                'nama' => $request->nama,
                'nip' => $request->nip,
            ]);

            return redirect()->route('admin.dosen.index')
                ->with('success', 'Data dosen berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Dosen $dosen)
    {
        $dosen->load('user');
        return view('admin.dosen.show', compact('dosen'));
    }

    public function edit(Dosen $dosen)
    {
        $dosen->load('user');
        return view('admin.dosen.edit', compact('dosen'));
    }

    public function update(Request $request, Dosen $dosen)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:20|unique:dosen,nip,' . $dosen->id,
            'email' => 'required|email|unique:users,email,' . $dosen->userId,
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'nama.required' => 'Nama dosen wajib diisi',
            'nip.required' => 'NIP wajib diisi',
            'nip.unique' => 'NIP sudah terdaftar',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update user account
            $userData = [
                'name' => $request->nama,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $dosen->user->update($userData);

            // Update dosen record
            $dosen->update([
                'nama' => $request->nama,
                'nip' => $request->nip,
            ]);

            return redirect()->route('admin.dosen.index')
                ->with('success', 'Data dosen berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Dosen $dosen)
    {
        try {
            // Delete user account
            $dosen->user->delete();
            
            // Dosen record will be deleted automatically due to foreign key cascade
            
            return redirect()->route('admin.dosen.index')
                ->with('success', 'Data dosen berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
