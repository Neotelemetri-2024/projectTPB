<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <title>Portal TPB</title>
    <link href="/images/favicon.png" rel="shortcut icon" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('head')
    @stack('styles')
    @yield('styles')
</head>

<body class="bg-gray-50 font-sans antialiased">
    <!-- Navbar -->
    @include('partials.navbar')

    <!-- Sidebar -->
    @include('partials.sidebar')
    <div class="p-4 sm:ml-64 pt-20 min-h-screen flex flex-col">
        <div class="flex-1">
            @yield('content')
        </div>
        @include('partials.footer')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleSidebarMobile = document.getElementById('toggleSidebarMobile');
            const toggleSidebar = document.getElementById('toggleSidebar');
            const pageContent = document.querySelector('.p-4.sm\\:ml-64');
            const dropdownButtons = document.querySelectorAll('[data-collapse-toggle]');

            function closeAllDropdowns() {
                document.querySelectorAll('[data-collapse-toggle]').forEach(button => {
                    const targetId = button.getAttribute('data-collapse-toggle');
                    const dropdownContent = document.getElementById(targetId);
                    if (dropdownContent) {
                        dropdownContent.classList.add('hidden');
                        const arrow = button.querySelector('svg:last-child');
                        if (arrow) {
                            arrow.style.transform = 'rotate(0deg)';
                        }
                    }
                });
            }

            function handleDropdownClick(e) {
                if (sidebar.classList.contains('w-16')) {
                    e.preventDefault();
                    return;
                }

                const targetId = this.getAttribute('data-collapse-toggle');
                const dropdownContent = document.getElementById(targetId);
                const arrow = this.querySelector('svg:last-child');

                if (dropdownContent) {
                    if (dropdownContent.classList.contains('hidden')) {
                        dropdownContent.classList.remove('hidden');
                        if (arrow) arrow.style.transform = 'rotate(180deg)';
                    } else {
                        dropdownContent.classList.add('hidden');
                        if (arrow) arrow.style.transform = 'rotate(0deg)';
                    }
                }
            }

            function toggleSidebarView() {
                const isCollapsed = sidebar.classList.contains('w-16');

                if (isCollapsed) {
                    sidebar.classList.remove('w-16');
                    sidebar.classList.add('w-64');

                    document.querySelectorAll('#sidebar span').forEach(el => {
                        el.classList.remove('opacity-0');
                        el.classList.remove('hidden');
                    });

                    pageContent.classList.remove('sm:ml-16');
                    pageContent.classList.add('sm:ml-64');

                    dropdownButtons.forEach(button => {
                        button.style.pointerEvents = 'auto';
                    });
                } else {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-16');

                    document.querySelectorAll('#sidebar span').forEach(el => {
                        el.classList.add('opacity-0');
                        setTimeout(() => el.classList.add('hidden'), 200);
                    });

                    closeAllDropdowns();

                    pageContent.classList.remove('sm:ml-64');
                    pageContent.classList.add('sm:ml-16');

                    dropdownButtons.forEach(button => {
                        button.style.pointerEvents = 'none';
                    });
                }
            }

            dropdownButtons.forEach(button => {
                button.addEventListener('click', handleDropdownClick);
            });

            if (toggleSidebarMobile) {
                toggleSidebarMobile.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleSidebarView();
                });
            }

            if (toggleSidebar) {
                toggleSidebar.addEventListener('click', function(e) {
                    e.preventDefault();
                    sidebar.classList.toggle('-translate-x-full');
                });
            }

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 640) {
                    sidebar.classList.remove('-translate-x-full');
                }
            });
        });

        function logout() {
            cleanupNotifications().then(() => {
                window.location.href = '/logout';
            }).catch(error => {
                console.error("Error during cleanup:", error);
                window.location.href = '/logout';
            });
        }
    </script>

    <script>
        function showNotification(message, type = 'success') {
            if (typeof Toastify === 'undefined') {
                return;
            }

            let icon = '';
            if (type === 'success') {
                icon = `<svg class="w-6 h-6 mr-2" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>`;
            } else if (type === 'error') {
                icon = `<svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>`;
            } else if (type === 'info') {
                icon = `<svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01" /></svg>`;
            }

            let background = "#fbbf24";
            if (type === 'success') background = "#22c55e";
            if (type === 'error') background = "#ef4444";
            if (type === 'info') background = "#3b82f6";

            Toastify({
                text: `<div style='display:flex;align-items:center;'><span>${icon}</span><span style='font-weight:500;font-size:0.98em;'>${message}</span></div>`,
                duration: 4000,
                gravity: "top",
                position: "right",
                backgroundColor: background,
                stopOnFocus: true,
                escapeMarkup: false,
                style: {
                    borderRadius: "12px",
                    boxShadow: "0 4px 24px 0 rgba(0,0,0,0.12)",
                    padding: "1em 1.5em",
                    color: "#fff",
                    minWidth: "220px",
                    maxWidth: "400px",
                },
                onClick: function() {}
            }).showToast();
        }

        @if(session('success'))
        showNotification(@json(session('success')), 'success');
        @endif

        @if(session('error'))
        showNotification(@json(session('error')), 'error');
        @endif

        @if($errors->any())
        @foreach($errors->all() as $error)
        showNotification(@json($error), 'error');
        @endforeach
        @endif
    </script>

    @stack('scripts')
    @yield('scripts')
</body>

</html>
