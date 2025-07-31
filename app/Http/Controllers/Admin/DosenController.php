<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Exports\DosenTemplateExport;
use App\Imports\DosenImport;
use Maatwebsite\Excel\Facades\Excel;

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
        ], [
            'nama.required' => 'Nama dosen wajib diisi',
            'nip.required' => 'NIP wajib diisi',
            'nip.unique' => 'NIP sudah terdaftar',
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
            // Create user account with password same as NIP
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->nip), // Password otomatis sama dengan NIP
                'role' => 'dosen',
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

    /**
     * Export template Excel untuk import dosen
     */
    public function exportTemplate()
    {
        $fileName = 'Template_Import_Dosen.xlsx';
        return Excel::download(new DosenTemplateExport(), $fileName);
    }

    /**
     * Import dosen dari Excel
     */
    public function importDosen(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:2048'
        ], [
            'excel_file.required' => 'File Excel harus dipilih.',
            'excel_file.mimes' => 'File harus berformat Excel (.xlsx atau .xls).',
            'excel_file.max' => 'Ukuran file maksimal 2MB.'
        ]);

        try {
            \Log::info('Starting dosen import...');
            $import = new DosenImport();
            Excel::import($import, $request->file('excel_file'));

            // Get import results
            $results = $import->getImportResults();
            \Log::info('Import dosen results:', $results);

            $message = "Import berhasil! ";
            $message .= "Berhasil memproses " . ($results['success'] ?? 0) . " dosen. ";
            
            if (($results['created'] ?? 0) > 0) {
                $message .= "Dibuat " . $results['created'] . " akun dosen baru. ";
            }
            
            if (($results['updated'] ?? 0) > 0) {
                $message .= "Diperbarui " . $results['updated'] . " data dosen. ";
            }

            if (!empty($results['errors'])) {
                $message .= "Terdapat " . count($results['errors']) . " error.";
                
                // Store errors in session for detailed display
                session()->flash('import_errors', $results['errors']);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}
