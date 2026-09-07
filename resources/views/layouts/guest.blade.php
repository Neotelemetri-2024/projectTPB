<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Portal TPB</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link href="/images/favicon.png" rel="shortcut icon" type="image/png">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header Section -->
            <div class="text-center">
                <div class="flex justify-center">
                    <div class="flex items-center space-x-3">
                        <img class="h-12 w-12" src="/assets/images/unand.png" alt="Logo Unand">
                        <div>
                            <h2 class="text-2xl font-bold text-green-700">Portal TPB</h2>
                            <p class="text-sm text-gray-600">Sistem Informasi Akademik</p>
                        </div>
                    </div>
                </div>
                <h3 class="mt-6 text-xl font-semibold text-gray-900">Masuk ke Akun Anda</h3>
                <p class="mt-2 text-sm text-gray-600">Silakan masukkan kredensial Anda untuk mengakses sistem</p>
            </div>

            <!-- Login Form Container -->
            <div class="bg-white py-8 px-8 shadow-lg rounded-lg border border-gray-200">
                {{ $slot }}
            </div>

            <!-- Footer Text -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    © {{ date('Y') }} Portal TPB by Neo Telemetri. All Rights Reserved.
                </p>
            </div>
        </div>
    </div>
</body>

</html>
