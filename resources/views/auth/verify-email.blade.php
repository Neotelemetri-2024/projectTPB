<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Email - Portal TPB</title>

    <!-- Favicon -->
    <link href="/images/favicon.png" rel="shortcut icon" type="image/png">

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

        <!-- Verify Email Card -->
        <div class="w-full bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 md:mt-0 sm:max-w-md xl:p-0">
            <div class="p-8 space-y-6">
                <!-- Header -->
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mb-4">
                        <i class="bx bx-envelope-open text-2xl text-amber-600"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Verifikasi Email
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Sebelum melanjutkan, silakan verifikasi email Anda
                    </p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                <div class="p-4 text-sm text-green-800 rounded-xl bg-green-50 border border-green-200" role="alert">
                    <div class="flex items-center">
                        <i class="bx bx-check-circle mr-2 text-green-600"></i>
                        {{ session('status') }}
                    </div>
                </div>
                @endif

                <!-- Verify Email Form -->
                <form class="space-y-6" method="POST" action="{{ route('verification.send') }}">
                    @csrf

                    <!-- Submit Button -->
                    <button type="submit" class="w-full text-white bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 focus:ring-4 focus:ring-amber-300 font-semibold rounded-xl text-sm px-5 py-3 text-center transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                        <i class="bx bx-refresh mr-2"></i>
                        Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <!-- Logout Form -->
                <form method="POST" action="{{ route('logout') }}" class="text-center">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-amber-600 hover:text-amber-700 transition-colors duration-200">
                        <i class="bx bx-log-out mr-1"></i>
                        Logout
                    </button>
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
</body>

</html>
