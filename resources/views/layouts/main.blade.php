<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        @vite(['resources/css/app.css','resources/js/app.js'])
        <title>Portal TPB</title>
        <link href="/assets/images/unand.png" rel="shortcut icon" type="image/vnd.microsoft.icon">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
        <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css' rel='stylesheet'>

        <!-- Di bagian head layout -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @push('styles')
        <style>
        .fc-event {
            cursor: pointer;
        }
        .fc-toolbar-title {
            font-size: 1.2em !important;
        }
        .fc-header-toolbar {
            margin-bottom: 1em !important;
            font-size: 0.8em !important;
        }
        .btn-primary {
            background-color: #d97706;
            border-color: #d97706;
        }
        .btn-primary:hover {
            background-color: #b45309;
            border-color: #b45309;
        }
        .btn-primary:focus {
            box-shadow: 0 0 0 0.2rem rgba(217, 119, 6, 0.25);
        }
        .text-brand {
            color: #d97706;
        }
        .border-brand {
            border-color: #d97706;
        }
        .focus-brand:focus {
            border-color: #d97706;
            box-shadow: 0 0 0 0.2rem rgba(217, 119, 6, 0.25);
        }
    </style>
        </style>
        @endpush
        @yield('styles')
    </head>
    <body class="bg-gray-50">
        <!-- Navbar -->
        @include('partials.navbar')

            <!-- Sidebar -->
        <!-- Enhanced Sidebar -->
        @include('partials.sidebar')
        <div class="p-4 sm:ml-64 pt-20">
            @yield('content')
        </div>
      
        <div class="pt-20">
        @include('partials.footer')
        </div>
        
  
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleSidebarMobile = document.getElementById('toggleSidebarMobile');
            const toggleSidebar = document.getElementById('toggleSidebar');
            const pageContent = document.querySelector('.p-4.sm\\:ml-64');
            const dropdownButtons = document.querySelectorAll('[data-collapse-toggle]');
            const footer = document.querySelector('footer'); // Tambahkan ini
            
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
                // Prevent dropdown toggle when sidebar is collapsed
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
                    // Expand sidebar
                    sidebar.classList.remove('w-16');
                    sidebar.classList.add('w-64');
                    if (footer) {
            footer.classList.remove('sm:ml-16');
            footer.classList.add('sm:ml-64');
            footer.style.width = 'calc(100% - 16rem)';
        }

                    // Show text with fade effect
                    document.querySelectorAll('#sidebar span').forEach(el => {
                        el.classList.remove('opacity-0');
                        el.classList.remove('hidden');
                    });

                    // Adjust main content
                    pageContent.classList.remove('sm:ml-16');
                    pageContent.classList.add('sm:ml-64');

                    // Re-enable dropdown buttons
                    dropdownButtons.forEach(button => {
                        button.style.pointerEvents = 'auto';
                    });
                } else {
                    // Collapse sidebar
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-16');

                    // Hide text with fade effect
                    document.querySelectorAll('#sidebar span').forEach(el => {
                        el.classList.add('opacity-0');
                        setTimeout(() => el.classList.add('hidden'), 200);
                    });

                    // Close all dropdowns
                    closeAllDropdowns();
                    if (footer) {
            footer.classList.remove('sm:ml-64');
            footer.classList.add('sm:ml-16');
            footer.style.width = 'calc(100% - 4rem)';
        }

                    // Adjust main content
                    pageContent.classList.remove('sm:ml-64');
                    pageContent.classList.add('sm:ml-16');

                    // Disable dropdown buttons
                    dropdownButtons.forEach(button => {
                        button.style.pointerEvents = 'none';
                    });
                }
            }

            // Add click event listeners to all dropdown buttons
            dropdownButtons.forEach(button => {
                button.addEventListener('click', handleDropdownClick);
            });

            // Toggle for desktop
            toggleSidebarMobile.addEventListener('click', function(e) {
                e.preventDefault();
                toggleSidebarView();
            });

            // Toggle for mobile
            toggleSidebar.addEventListener('click', function(e) {
                e.preventDefault();
                sidebar.classList.toggle('-translate-x-full');
            });

            // Enhanced window resize handler
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 640) {
                    sidebar.classList.remove('-translate-x-full');
                }
            });
        });
        function logout() {
    // Panggil fungsi cleanup notifikasi
    cleanupNotifications().then(() => {
        // Lanjutkan dengan proses logout normal (misalnya redirect ke halaman logout)
        window.location.href = '/logout';
    }).catch(error => {
        console.error("Error during cleanup:", error);
        // Tetap lanjutkan logout meskipun ada error
        window.location.href = '/logout';
    });
}


        </script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <!-- Toastify CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <!-- Toastify JS -->
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        
        <script>
        // Toastify notification function
        function showNotification(message, type = 'success') {
            // Custom icon SVG
            let icon = '';
            if (type === 'success') {
                icon = `<svg class="w-6 h-6 mr-2" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>`;
            } else if (type === 'error') {
                icon = `<svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>`;
            } else if (type === 'info') {
                icon = `<svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01" /></svg>`;
            }

            // Solid color
            let background = "#fbbf24"; // default orange
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
                onClick: function(){}
            }).showToast();
        }

        // Check for session messages and show notifications
        @if(session('success'))
            showNotification("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showNotification("{{ session('error') }}", 'error');
        @endif

        // Show validation errors
        @if($errors->any())
            @foreach($errors->all() as $error)
                showNotification("{{ $error }}", 'error');
            @endforeach
        @endif
        </script>
        
        @stack('scripts')
        @yield('scripts')
    </body>
</html>