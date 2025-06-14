@props(['title' => ''])

<div x-data="{ open: true }" class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <div :class="open ? 'w-64' : 'w-16'" class="transition-all duration-300 bg-white border-r shadow-sm">
        <x-sidebar />
    </div>

    <!-- Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Topbar -->
        <header class="flex items-center justify-between px-4 py-2 bg-white border-b shadow-sm">
            <button @click="open = !open" class="text-gray-500 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="text-lg font-semibold text-gray-700">{{ $title }}</h1>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-4 overflow-auto bg-gray-100">
            {{ $slot }}
        </main>
    </div>
</div>
