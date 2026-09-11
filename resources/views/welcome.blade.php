<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal TPB</title>

    <!-- Favicon -->
    <link href="/images/logo-unand.png" rel="shortcut icon" type="image/png">

    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
        <!-- Logo & Brand -->
        <div class="flex items-center mb-6 text-2xl font-semibold text-gray-900">
            <img class="w-10 h-10 mr-3" src="/assets/images/logo-unand.png" alt="Logo Unand">
            <span class="text-amber-700">Portal TPB</span>
        </div>

        <!-- Welcome Card -->
        <div class="w-full bg-white rounded-lg shadow-lg border border-gray-200 md:mt-0 sm:max-w-md xl:p-0">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <!-- Header -->
                <div class="text-center">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl">
                        Selamat Datang
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Sistem Informasi Akademik TPB
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-4">
                    @auth
                    <!-- User is logged in - redirect to appropriate dashboard -->
                    <script>
                        window.location.href = '{{ route("dashboard") }}';
                    </script>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Mengalihkan ke dashboard...</p>
                        <a href="{{ route('dashboard') }}" class="inline-block mt-4 text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors duration-200 border border-yellow-600">
                            Lanjutkan ke Dashboard
                        </a>
                    </div>
                    @else
                    <!-- User is not logged in -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('login') }}" class="w-full sm:w-auto text-gray-900 bg-white hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors duration-200">
                            Login
                        </a>
                        <p class="text-sm text-gray-600 text-center sm:text-left">
                            Hubungi administrator untuk membuat akun.
                        </p>
                    </div>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500">
                © {{ date('Y') }} Portal TPB by Neo Telemetri. All Rights Reserved.
            </p>
        </div>
    </div>
</body>

</html>