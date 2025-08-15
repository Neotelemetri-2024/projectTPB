<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Portal TPB</title>
    
    <!-- Favicon -->
    <link href="/assets/images/unand.png" rel="shortcut icon" type="image/vnd.microsoft.icon">
    
    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Flowbite CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-50">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
        <!-- Logo & Brand -->
        <div class="flex items-center mb-6 text-2xl font-semibold text-gray-900">
            {{-- <img class="w-10 h-10 mr-3" src="/assets/images/unand.png" alt="Logo Unand"> --}}
            <span class="text-amber-700">Portal TPB</span>
        </div>
        
        <!-- Login Card -->
        <div class="w-full bg-white rounded-lg shadow-lg border border-gray-200 md:mt-0 sm:max-w-md xl:p-0">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <!-- Header -->
                <div class="text-center">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl">
                        Masuk ke Akun Anda
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Sistem Informasi Akademik TPB
                    </p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="p-4 mb-4 text-sm text-amber-800 rounded-lg bg-amber-50 border border-amber-200" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Login Form -->
                <form class="space-y-4 md:space-y-6" method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}" 
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" 
                               placeholder="name@example.com" 
                               required 
                               autofocus 
                               autocomplete="username">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                        <div class="relative">
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   placeholder="••••••••" 
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 pr-10" 
                                   required 
                                   autocomplete="current-password">
                            <button type="button" 
                                    id="togglePassword" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                                <i class="bx bx-hide text-lg" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="remember" 
                                       name="remember" 
                                       type="checkbox" 
                                       class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-amber-300 text-amber-600">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="remember" class="text-gray-500">{{ __('Remember me') }}</label>
                            </div>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-amber-600 hover:underline">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="w-full text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors duration-200 border border-yellow-600">
                        {{ __('Log in') }}
                    </button>

                    <!-- Register Link -->
                    @if (Route::has('register'))
                        <p class="text-sm font-light text-gray-500 text-center">
                            {{ __("Don't have an account yet?") }} 
                            <a href="{{ route('register') }}" class="font-medium text-amber-600 hover:underline">
                                {{ __('Sign up') }}
                            </a>
                        </p>
                    @endif
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500">
                © {{ date('Y') }} Portal TPB by Neo Telemetri. All Rights Reserved.
            </p>
        </div>
    </div>

    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
    
    <!-- Password Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            togglePassword.addEventListener('click', function() {
                // Toggle password visibility
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                // Toggle eye icon
                if (type === 'text') {
                    eyeIcon.classList.remove('bx-hide');
                    eyeIcon.classList.add('bx-show');
                } else {
                    eyeIcon.classList.remove('bx-show');
                    eyeIcon.classList.add('bx-hide');
                }
            });
        });
    </script>
</body>
</html>
