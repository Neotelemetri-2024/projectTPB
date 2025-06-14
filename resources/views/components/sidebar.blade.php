@php
    $nav = [
        ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home'],
        ['name' => 'Users', 'route' => 'users.index', 'icon' => 'users'],
        ['name' => 'Settings', 'route' => 'settings', 'icon' => 'cog'],
    ];
@endphp

<nav class="h-full">
    <div class="p-4 font-bold text-gray-800 text-center text-xl">
        LOGO
    </div>
    <ul class="space-y-2 px-2">
        @foreach ($nav as $item)
            <li>
                <a href="{{ route($item['route']) }}"
                   class="flex items-center space-x-3 px-3 py-2 rounded-md
                          text-sm font-medium
                          {{ request()->routeIs($item['route']) ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-100' }}">
                    <x-icon :name="$item['icon']" class="w-5 h-5"/>
                    <span x-show="open" class="whitespace-nowrap">{{ $item['name'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>
