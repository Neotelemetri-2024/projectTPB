@extends('layouts.main')

@section('title', 'Profile')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Profil Saya</h1>
            <p class="text-gray-600 mt-1">Kelola informasi profil dan keamanan akun Anda</p>
        </div>

        <!-- Success Messages -->
        @if(session('status') === 'profile-updated')
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">Profil berhasil diperbarui!</span>
            </div>
        @endif

        @if(session('status') === 'password-updated')
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">Password berhasil diperbarui!</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Information -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Informasi Profil</h2>
                        <p class="text-sm text-gray-600 mt-1">Perbarui informasi profil dan alamat email akun Anda</p>
                    </div>
                    
                    <div class="p-6">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PATCH')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name', $user->name) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('name') border-red-500 @enderror" 
                                           required>
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           value="{{ old('email', $user->email) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('email') border-red-500 @enderror" 
                                           required>
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                        Role
                                    </label>
                                    <input type="text" 
                                           id="role" 
                                           value="{{ ucfirst($user->role) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed" 
                                           disabled>
                                </div>
                                
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        Status
                                    </label>
                                    <span class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium {{ $user->isAktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <div class="w-2 h-2 rounded-full mr-2 {{ $user->isAktif ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                        {{ $user->isAktif ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex justify-end mt-6">
                                <button type="submit" 
                                        class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 text-center">
                        <div class="w-24 h-24 bg-amber-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-12 h-12 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-600">{{ ucfirst($user->role) }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="text-sm text-gray-600">
                                <p>Bergabung sejak</p>
                                <p class="font-medium text-gray-900">{{ $user->created_at->format('d F Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="mt-6">
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Ubah Password</h2>
                    <p class="text-sm text-gray-600 mt-1">Pastikan akun Anda menggunakan password yang kuat untuk keamanan</p>
                </div>
                
                <div class="p-6">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password Saat Ini <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       name="current_password" 
                                       id="current_password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('current_password', 'updatePassword') border-red-500 @enderror" 
                                       required>
                                @error('current_password', 'updatePassword')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password Baru <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('password', 'updatePassword') border-red-500 @enderror" 
                                       required>
                                @error('password', 'updatePassword')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Konfirmasi Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('password_confirmation', 'updatePassword') border-red-500 @enderror" 
                                       required>
                                @error('password_confirmation', 'updatePassword')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Danger Zone -->
        <div class="mt-6">
            <div class="bg-white rounded-lg shadow-md border border-red-200">
                <div class="p-6 border-b border-red-200">
                    <h2 class="text-lg font-semibold text-red-900">Zona Berbahaya</h2>
                    <p class="text-sm text-red-600 mt-1">Tindakan berikut tidak dapat dibatalkan</p>
                </div>
                
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Hapus Akun</h3>
                            <p class="text-sm text-gray-600">Hapus akun Anda secara permanen dan semua data yang terkait</p>
                        </div>
                        <button type="button" 
                                data-modal-target="deleteAccountModal" 
                                data-modal-toggle="deleteAccountModal"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                            Hapus Akun
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteAccountModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="deleteAccountModal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="p-4 md:p-5 text-center">
                <svg class="mx-auto mb-4 text-red-400 w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Apakah Anda yakin ingin menghapus akun ini?</h3>
                <p class="mb-5 text-sm text-gray-400">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data Anda secara permanen.</p>
                
                <form method="POST" action="{{ route('profile.destroy') }}" class="inline">
                    @csrf
                    @method('DELETE')
                    
                    <div class="mb-4">
                        <input type="password" 
                               name="password" 
                               placeholder="Masukkan password untuk konfirmasi"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                               required>
                    </div>
                    
                    <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center me-2">
                        Ya, Hapus Akun
                    </button>
                    <button type="button" data-modal-hide="deleteAccountModal" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        Batal
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 