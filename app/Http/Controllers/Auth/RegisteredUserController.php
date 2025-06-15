<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Cek apakah user dengan email sudah ada
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Kalau belum ada, tolak karena mahasiswa seharusnya diimport oleh admin
            throw ValidationException::withMessages([
                'email' => 'Email ini tidak terdaftar dalam sistem. Silakan hubungi admin.',
            ]);
        }

        // Cek apakah sudah pernah registrasi (sudah punya password)
        if ($user->password !== null) {
            throw ValidationException::withMessages([
                'email' => 'Email ini sudah pernah digunakan untuk registrasi.',
            ]);
        }

        // Validasi: apakah NIM di email cocok dengan tabel students
        $emailNim = explode('_', $request->email)[0];

        $student = Student::where('user_id', $user->id)->where('nim', $emailNim)->first();

        if (!$student) {
            throw ValidationException::withMessages([
                'email' => 'Format email tidak cocok dengan NIM yang terdaftar.',
            ]);
        }

        // Update user
        $user->update([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil. Silakan login.');
    }
}
