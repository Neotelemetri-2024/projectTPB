<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // 1. Mahasiswa
    public function indexStudents()
    {
        // $all = Student::with('user')->paginate(5);
        $students = Student::with('user') // get data relasi user
            ->whereHas('user', function ($q) {
                $q->where('role', 'mahasiswa');
            })
            ->paginate(5);

        return view('user.students.index', compact('students'));
    }

    public function storeStudent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nim' => 'required|string|unique:students,nim',
            'tahun_masuk' => 'required|numeric',
            'role' => 'required|string|in:admin,dosen,pimpinan,mahasiswa',
        ]);
        // 1. Simpan ke tabel users
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('password'), // default password, nanti bisa reset
            'role' => $validated['role'],
        ]);

        // 2. Simpan ke tabel students (jika role = mahasiswa)
        if ($validated['role'] === 'mahasiswa') {
            Student::create([
                'user_id' => $user->id,
                'nim' => $validated['nim'],
                'tahun_masuk' => $validated['tahun_masuk'],
            ]);
        }

        return redirect()->back()->with('success', 'Data user berhasil ditambahkan.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        Excel::import(new StudentsImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data berhasil diimport!');
    }

    public function editStudent($id)
    {
        $student = Student::with('user')->where('id', $id)->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        return response()->json($student);
    }


    public function updateStudent(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);

        // Update tabel user (relasi)
        $student->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        // Update tabel student 

        $student->update([
            'nim' => $request->nim,
            'tahun_masuk' => $request->tahun_masuk,
        ]);

        return redirect()->route('admin.users.students')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }
    public function destroyStudent($id)
    {
        $student = Student::findOrFail($id);
        $user = $student->user;

        $student->delete();
        if ($user) {
            $user->delete();
        }

        return response()->json(['message' => 'Berhasil dihapus']);
    }
}
