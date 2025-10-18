<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 flex">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg h-screen fixed left-0 top-0 z-50">
        <!-- Logo Section -->
        <div class="px-6 py-4 flex items-center justify-center border-b border-gray-200">
            <a href="{{ route('home') }}" class="cursor-pointer hover:opacity-80 transition-opacity">
                <img src="{{ url('images/header_logo.png') }}" alt="Lakbay Calamba Logo" class="h-12 w-auto">
            </a>
        </div>

        <!-- Navigation Menu -->
        <nav class="mt-6 px-4">
            <ul class="space-y-2">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('admin.dash') }}" 
                       class="group flex items-center px-4 py-3 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105
                              {{ request()->routeIs('admin.dash') 
                                    ? 'bg-blue-600 text-gray-100 shadow-md' 
                                    : 'text-gray-700 hover:bg-blue-600 hover:text-gray-100' }}">
                        <i data-lucide="layout-dashboard" 
                           class="w-5 h-5 transition-colors duration-300
                                  {{ request()->routeIs('admin.dash') ? 'text-gray-100' : 'text-blue-600 group-hover:text-gray-100' }}"></i>
                        <span class="ml-3 font-medium">Dashboard</span>
                    </a>
                </li>

                <!-- Reports -->
                <li>
                    <a href="{{ route('admin.reports') }}" 
                       class="group flex items-center px-4 py-3 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105
                              {{ request()->routeIs('admin.reports') 
                                    ? 'bg-blue-600 text-gray-100 shadow-md' 
                                    : 'text-gray-700 hover:bg-blue-600 hover:text-gray-100' }}">
                        <i data-lucide="file-text" 
                           class="w-5 h-5 transition-colors duration-300
                                  {{ request()->routeIs('admin.reports') ? 'text-gray-100' : 'text-blue-600 group-hover:text-gray-100' }}"></i>
                        <span class="ml-3 font-medium">Reports</span>
                    </a>
                </li>

                <!-- Manage Info -->
                <li>
                    <a href="{{ route('admin.manage') }}" 
                       class="group flex items-center px-4 py-3 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105
                              {{ request()->routeIs('admin.manage') 
                                    ? 'bg-blue-600 text-gray-100 shadow-md' 
                                    : 'text-gray-700 hover:bg-blue-600 hover:text-gray-100' }}">
                        <i data-lucide="folder-cog" 
                           class="w-5 h-5 transition-colors duration-300
                                  {{ request()->routeIs('admin.manage') ? 'text-gray-100' : 'text-blue-600 group-hover:text-gray-100' }}"></i>
                        <span class="ml-3 font-medium">Manage Info</span>
                    </a>
                </li>

                <!-- Settings -->
                <li>
                    <a href="{{ route('admin.settings') }}" 
                       class="group flex items-center px-4 py-3 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105
                              {{ request()->routeIs('admin.settings') 
                                    ? 'bg-blue-600 text-gray-100 shadow-md' 
                                    : 'text-gray-700 hover:bg-blue-600 hover:text-gray-100' }}">
                        <i data-lucide="settings" 
                           class="w-5 h-5 transition-colors duration-300
                                  {{ request()->routeIs('admin.settings') ? 'text-gray-100' : 'text-blue-600 group-hover:text-gray-100' }}"></i>
                        <span class="ml-3 font-medium">Settings</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Bottom Section -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                    <i data-lucide="user" class="w-4 h-4 text-white"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->email ?? 'admin@system.com' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="p-2 text-gray-500 hover:text-red-500 transition-colors duration-300 hover:bg-red-50 rounded-lg"
                            onclick="return confirm('Are you sure you want to logout?')">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Custom CSS for enhanced hover effects -->
    <style>
        /* Additional hover effects */
        .group:hover {
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }
        
        /* Smooth transition for all interactive elements */
        .group {
            position: relative;
            overflow: hidden;
        }
        
        /* Subtle glow effect on hover */
        .group:hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05));
            border-radius: 0.5rem;
            z-index: -1;
        }
    </style>

<!-- Main Content -->
<main class="flex-1 ml-64">
    <!-- Top Bar (full width, dumikit sa sidebar) -->
    <div class="flex justify-between items-center bg-white px-6 py-6 shadow-sm border-b border-gray-200">
        <div></div>
        <div class="flex items-center space-x-6">
            <!-- Email Icon -->
            <i data-lucide="mail" class="w-6 h-6 text-blue-600 cursor-pointer"></i>

            <!-- Notification Icon -->
            <i data-lucide="bell" class="w-6 h-6 text-blue-600 cursor-pointer"></i>
        </div>
    </div>

    <!-- Content Area with padding -->
    <div class="p-6">
        <!-- Date & Time (below top bar) -->
        <div class="flex justify-end mb-6">
            <span id="datetime" class="text-gray-600"></span>
        </div>

        @yield('content')
    </div>
</main>

    <!-- JS for Lucide & Live Date/Time -->
    <script>
  // Load Lucide icons
  lucide.createIcons();

  // Live date & time (Asia/Manila)
  function updateDateTime() {
    const now = new Date();
    const opts = {
      year: 'numeric', month: 'long', day: 'numeric',
      hour: '2-digit', minute: '2-digit',
      hour12: true, timeZone: 'Asia/Manila'
    };
    document.getElementById('datetime').textContent =
      now.toLocaleString('en-PH', opts);
  }
  updateDateTime();
  setInterval(updateDateTime, 1000);
</script>

</body>
</html>
