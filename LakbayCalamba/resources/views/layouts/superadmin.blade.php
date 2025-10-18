<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50">
    
    <!-- SuperAdmin Sidebar -->
    @include('components.sidebar')

    <!-- Main Content Area -->
    <main class="ml-64 min-h-screen">
        <!-- Top Header Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-6">
                    <!-- Breadcrumb -->
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="{{ route('superadmin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                    <i data-lucide="home" class="w-4 h-4"></i>
                                    
                                </a>
                            </li>
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                </div>
                
                <div class="flex items-center space-x-4 h-12">
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-6">
            <!-- Main Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Add any additional JavaScript for superadmin functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Handle sidebar navigation active states
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('nav a');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('bg-blue-600', 'text-gray-100');
                    link.classList.remove('text-gray-700', 'hover:bg-blue-600', 'hover:text-gray-100');
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
