<nav class="fixed top-0 z-40 w-full bg-white border-b border-gray-200">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <!-- Bagian kiri (sidebar toggle & logo) tetap sama -->
            <div class="flex items-center justify-start">
                <button id="toggleSidebar" aria-expanded="true" aria-controls="sidebar"
                    class="p-2 text-gray-600 rounded cursor-pointer lg:hidden hover:text-gray-900 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
                <button id="toggleSidebarMobile" aria-expanded="true" aria-controls="sidebar"
                    class="hidden p-2 text-gray-600 rounded cursor-pointer lg:block hover:text-gray-900 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h7"></path>
                    </svg>
                </button>
                <a href="#" class="flex ml-2 md:mr-24">
                    <span
                        class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap text-amber-700">Portal TPB</span>
                </a>
            </div>

            <!-- Bagian kanan (notifications & profile) -->
            <div class="flex items-center space-x-3">
                <!-- Notification button -->
                <button type="button"
                    class="notification-button p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 transition-colors">

                </button>

                <!-- Profile dropdown -->
                <div class="relative">
                    <button type="button" class="flex items-center space-x-3 focus:outline-none" id="user-menu-button"
                        data-dropdown-toggle="user-dropdown">
                        <img class="w-8 h-8 rounded-full"
                            src=""
                            alt="user photo">
                        <div class="hidden md:block text-left">
                            <span class="block text-sm font-medium text-gray-900">{{ auth()->user()->name }}</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Dropdown menu -->
                    <div class="absolute right-0 z-50 hidden w-48 mt-2 origin-top-right bg-white rounded-lg shadow-lg"
                        id="user-dropdown">
                        <div class="p-4 border-b border-gray-100">
                            <span class="block text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</span>
                            <span class="block text-sm text-gray-500">{{ auth()->user()->role }}</span>
                        </div>
                        <ul class="py-2" role="none">
                            <li>
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Profil
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Ubah Password
                                </a>
                            </li>
                            <li class="border-t border-gray-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>