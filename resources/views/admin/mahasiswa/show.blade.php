@extends('layouts.main')

@section('title', 'Detail Mahasiswa')

@section('content')
<div class="p-6">


    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Detail Mahasiswa</h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.mahasiswa.edit', $mahasiswa->id) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('admin.mahasiswa.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pribadi</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="font-medium text-gray-700">NIM:</span>
                            <span class="text-gray-900">{{ $mahasiswa->nim }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="font-medium text-gray-700">Nama Lengkap:</span>
                            <span class="text-gray-900">{{ $mahasiswa->nama }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="font-medium text-gray-700">Email:</span>
                            <span class="text-gray-900">{{ $mahasiswa->user->email }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="font-medium text-gray-700">Tahun Masuk:</span>
                            <span class="text-gray-900">{{ $mahasiswa->tahunMasuk }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="font-medium text-gray-700">Status:</span>
                            <span>
                                @if($mahasiswa->user->isAktif)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Tidak Aktif</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="font-medium text-gray-700">Role:</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst($mahasiswa->user->role) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="font-medium text-gray-700">Tanggal Dibuat:</span>
                            <span class="text-gray-900">{{ $mahasiswa->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="font-medium text-gray-700">Terakhir Diupdate:</span>
                            <span class="text-gray-900">{{ $mahasiswa->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Akun</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">User ID:</span>
                                <span class="text-gray-900">{{ $mahasiswa->user->id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Status Akun:</span>
                                <span class="text-gray-900">{{ ucfirst($mahasiswa->user->status) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Email Verified:</span>
                                <span>
                                    @if($mahasiswa->user->email_verified_at)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Terverifikasi</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Belum Terverifikasi</span>
                                    @endif
                                </span>
                            </div>
                            @if($mahasiswa->user->email_verified_at)
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Tanggal Verifikasi:</span>
                                    <span class="text-gray-900">{{ $mahasiswa->user->email_verified_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 