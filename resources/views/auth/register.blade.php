<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Portal TPB</title>
    
    <!-- Favicon -->
    <link href="/assets/images/unand.png" rel="shortcut icon" type="image/vnd.microsoft.icon">
    
    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="bg-gradient-to-br from-amber-50 to-yellow-100 min-h-screen">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto min-h-screen">
        <!-- Logo & Brand -->
        <div class="flex items-center mb-8 text-3xl font-bold text-amber-700">
            <span>Portal TPB</span>
        </div>
        
        <!-- Register Card -->
        <div class="w-full bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 md:mt-0 sm:max-w-md xl:p-0">
            <div class="p-8 space-y-6">
                <!-- Header -->
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Daftar Akun Baru
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Buat akun untuk mengakses sistem
                    </p>
                </div>

                <!-- Register Form -->
                <form class="space-y-6" method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block mb-2 text-sm font-semibold text-gray-700">Nama Lengkap</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bx bx-user text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name') }}" 
                                   class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 block w-full pl-10 p-3 transition-all duration-200" 
                                   placeholder="Masukkan nama lengkap" 
                                   required 
                                   autofocus 
                                   autocomplete="name">
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block mb-2 text-sm font-semibold text-gray-700">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bx bx-envelope text-gray-400"></i>
                            </div>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email') }}" 
                                   class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 block w-full pl-10 p-3 transition-all duration-200" 
                                   placeholder="Masukkan email Anda" 
                                   required 
                                   autocomplete="username">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block mb-2 text-sm font-semibold text-gray-700">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bx bx-lock-alt text-gray-400"></i>
                            </div>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   placeholder="Masukkan password Anda" 
                                   class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 block w-full pl-10 pr-10 p-3 transition-all duration-200" 
                                   required 
                                   autocomplete="new-password">
                            <button type="button" 
                                    id="togglePassword" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors duration-200">
                                <i class="bx bx-hide text-xl" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <label for="password_confirmation" class="block mb-2 text-sm font-semibold text-gray-700">Konfirmasi Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bx bx-lock-alt text-gray-400"></i>
                            </div>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   placeholder="Konfirmasi password Anda" 
                                   class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 block w-full pl-10 pr-10 p-3 transition-all duration-200" 
                                   required 
                                   autocomplete="new-password">
                            <button type="button" 
                                    id="toggleConfirmPassword" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors duration-200">
                                <i class="bx bx-hide text-xl" id="eyeConfirmIcon"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Register Button -->
                    <button type="submit" class="w-full text-white bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 focus:ring-4 focus:ring-amber-300 font-semibold rounded-xl text-sm px-5 py-3 text-center transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                        <i class="bx bx-user-plus mr-2"></i>
                        Daftar
                    </button>

                    <!-- Login Link -->
                    @if (Route::has('login'))
                        <p class="text-sm text-gray-600 text-center">
                            Sudah punya akun? 
                            <a href="{{ route('login') }}" class="font-semibold text-amber-600 hover:text-amber-700 transition-colors duration-200">
                                Masuk di sini
                            </a>
                        </p>
                    @endif
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                © {{ date('Y') }} Portal TPB by Neo Telemetri. All Rights Reserved.
            </p>
        </div>
    </div>
    
    <!-- Password Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                if (type === 'text') {
                    eyeIcon.classList.remove('bx-hide');
                    eyeIcon.classList.add('bx-show');
                } else {
                    eyeIcon.classList.remove('bx-show');
                    eyeIcon.classList.add('bx-hide');
                }
            });

            // Confirm password toggle
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const confirmPassword = document.getElementById('password_confirmation');
            const eyeConfirmIcon = document.getElementById('eyeConfirmIcon');
            
            toggleConfirmPassword.addEventListener('click', function() {
                const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPassword.setAttribute('type', type);
                
                if (type === 'text') {
                    eyeConfirmIcon.classList.remove('bx-hide');
                    eyeConfirmIcon.classList.add('bx-show');
                } else {
                    eyeConfirmIcon.classList.remove('bx-show');
                    eyeConfirmIcon.classList.add('bx-hide');
                }
            });
        });
    </script>
</body>
</html>
