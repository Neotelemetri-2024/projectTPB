<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::with('user');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nim', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('email', 'like', '%' . $search . '%');
                  });
            });
        }

        // Default filter: Semua Tahun Masuk (tidak menerapkan filter jika tidak dipilih)
        // Jika parameter tahun_masuk ada di request namun kosong => berarti pilih "Semua Tahun Masuk"
        $selectedTahunMasuk = $request->has('tahun_masuk') ? $request->tahun_masuk : null;

        // Terapkan filter hanya jika nilai tidak kosong/null
        if ($selectedTahunMasuk !== null && $selectedTahunMasuk !== '') {
            $query->where('tahunMasuk', $selectedTahunMasuk);
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

        // Get unique tahun masuk for filter dropdown
        $tahunMasukList = Mahasiswa::distinct()->pluck('tahunMasuk')->sort()->values();

        // Pagination
        $mahasiswa = $query->orderBy('nama')->paginate($this->perPage($request))->withQueryString();

        return view('admin.mahasiswa.index', compact('mahasiswa', 'tahunMasukList', 'selectedTahunMasuk'));
    }

    public function create()
    {
        return view('admin.mahasiswa.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|unique:mahasiswa,nim|max:20',
            'tahunMasuk' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'email' => 'required|email|unique:users,email',
        ], [
            'nama.required' => 'Nama mahasiswa wajib diisi',
            'nim.required' => 'NIM wajib diisi',
            'nim.unique' => 'NIM sudah terdaftar',
            'tahunMasuk.required' => 'Tahun masuk wajib diisi',
            'tahunMasuk.integer' => 'Tahun masuk harus berupa angka',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create user account with password same as NIM
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->nim), // Password otomatis sama dengan NIM
                'role' => 'mahasiswa',
                'isAktif' => true,
            ]);

            // Create mahasiswa record
            $mahasiswa = Mahasiswa::create([
                'userId' => $user->id,
                'nama' => $request->nama,
                'nim' => $request->nim,
                'tahunMasuk' => $request->tahunMasuk,
            ]);

            return redirect()->route('admin.mahasiswa.index')
                ->with('success', 'Data mahasiswa berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load('user');
        return view('admin.mahasiswa.show', compact('mahasiswa'));
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load('user');
        return view('admin.mahasiswa.edit', compact('mahasiswa'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|max:20|unique:mahasiswa,nim,' . $mahasiswa->id,
            'tahunMasuk' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'email' => 'required|email|unique:users,email,' . $mahasiswa->userId,
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'nama.required' => 'Nama mahasiswa wajib diisi',
            'nim.required' => 'NIM wajib diisi',
            'nim.unique' => 'NIM sudah terdaftar',
            'tahunMasuk.required' => 'Tahun masuk wajib diisi',
            'tahunMasuk.integer' => 'Tahun masuk harus berupa angka',
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

            $mahasiswa->user->update($userData);

            // Update mahasiswa record
            $mahasiswa->update([
                'nama' => $request->nama,
                'nim' => $request->nim,
                'tahunMasuk' => $request->tahunMasuk,
            ]);

            return redirect()->route('admin.mahasiswa.index')
                ->with('success', 'Data mahasiswa berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        try {
            // Delete user account
            $mahasiswa->user->delete();

            // Mahasiswa record will be deleted automatically due to foreign key cascade

            return redirect()->route('admin.mahasiswa.index')
                ->with('success', 'Data mahasiswa berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:mahasiswa,id'
        ]);

        try {
            $mahasiswas = Mahasiswa::with('user')->whereIn('id', $request->ids)->get();
            
            foreach ($mahasiswas as $mhs) {
                if ($mhs->user) {
                    $mhs->user->delete();
                } else {
                    $mhs->delete();
                }
            }

            return redirect()->route('admin.mahasiswa.index')
                ->with('success', count($request->ids) . ' Data mahasiswa berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
