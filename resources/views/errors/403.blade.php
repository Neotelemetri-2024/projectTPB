<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Dilarang | Portal TPB</title>
    <link href="/images/logo-unand.png" rel="shortcut icon" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-md p-8 max-w-md w-full text-center">
        <!-- Icon -->
        <div class="mb-6">
            <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                <i class='bx bx-lock-alt text-3xl text-red-600'></i>
            </div>
        </div>

        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-900 mb-2">403</h1>
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Akses Dilarang</h2>

        <!-- Description -->
        <p class="text-gray-600 mb-6">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
        </p>

        <!-- Action Buttons -->
        <div class="space-y-3">
            <a href="{{ route('login') }}"
                class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                <i class='bx bx-log-in mr-2'></i>
                Login
            </a>

            <a href="{{ route('dashboard') }}"
                class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                <i class='bx bx-home-alt mr-2'></i>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>

</html>