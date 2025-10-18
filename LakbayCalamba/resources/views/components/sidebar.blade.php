<aside class="w-64 bg-white shadow-lg h-screen fixed left-0 top-0 z-50">
    <!-- Logo Section -->
    <div class="px-6 py-4 flex items-center justify-center border-b border-gray-200">
        <a href="{{ route('home') }}" class="cursor-pointer hover:opacity-80 transition-opacity">
            <img src="/images/header_logo.png" alt="Lakbay Calamba Logo" class="h-12 w-auto">
        </a>
    </div>

    <!-- Navigation Menu -->
    <nav class="mt-6 px-4">
        <ul class="space-y-2">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('superadmin.dashboard') }}" 
                   class="group flex items-center px-4 py-3 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105
                          {{ request()->routeIs('superadmin.dashboard') 
                                ? 'bg-blue-600 text-gray-100 shadow-md' 
                                : 'text-gray-700 hover:bg-blue-600 hover:text-gray-100' }}">
                    <i data-lucide="layout-dashboard" 
                       class="w-5 h-5 transition-colors duration-300
                              {{ request()->routeIs('superadmin.dashboard') ? 'text-gray-100' : 'text-blue-600 group-hover:text-gray-100' }}"></i>
                    <span class="ml-3 font-medium">Dashboard</span>
                </a>
            </li>

            <!-- Rewards & Stamps -->
            <li>
                <a href="{{ route('superadmin.manage-rewards') }}" 
                   class="group flex items-center px-4 py-3 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105
                          {{ request()->routeIs('superadmin.manage-rewards') 
                                ? 'bg-blue-600 text-gray-100 shadow-md' 
                                : 'text-gray-700 hover:bg-blue-600 hover:text-gray-100' }}">
                    <i data-lucide="trophy" 
                       class="w-5 h-5 transition-colors duration-300
                              {{ request()->routeIs('superadmin.manage-rewards') ? 'text-gray-100' : 'text-blue-600 group-hover:text-gray-100' }}"></i>
                    <span class="ml-3 font-medium">Rewards & Stamps</span>
                </a>
            </li>

            <!-- Establishments -->
            <li>
                <a href="{{ route('superadmin.manage-establishments') }}" 
                   class="group flex items-center px-4 py-3 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105
                          {{ request()->routeIs('superadmin.manage-establishments') 
                                ? 'bg-blue-600 text-gray-100 shadow-md' 
                                : 'text-gray-700 hover:bg-blue-600 hover:text-gray-100' }}">
                    <i data-lucide="building" 
                       class="w-5 h-5 transition-colors duration-300
                              {{ request()->routeIs('superadmin.manage-establishments') ? 'text-gray-100' : 'text-blue-600 group-hover:text-gray-100' }}"></i>
                    <span class="ml-3 font-medium">Establishments</span>
                </a>
            </li>

            <!-- Admin Management -->
            <li>
                <a href="{{ route('superadmin.manage-admins') }}" 
                   class="group flex items-center px-4 py-3 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105
                          {{ request()->routeIs('superadmin.manage-admins') 
                                ? 'bg-blue-600 text-gray-100 shadow-md' 
                                : 'text-gray-700 hover:bg-blue-600 hover:text-gray-100' }}">
                    <i data-lucide="users" 
                       class="w-5 h-5 transition-colors duration-300
                              {{ request()->routeIs('superadmin.manage-admins') ? 'text-gray-100' : 'text-blue-600 group-hover:text-gray-100' }}"></i>
                    <span class="ml-3 font-medium">Admin Management</span>
                </a>
            </li>

            <!-- Tourist Management -->
            <li>
                <a href="{{ route('superadmin.manage-tourists') }}" 
                   class="group flex items-center px-4 py-3 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105
                          {{ request()->routeIs('superadmin.manage-tourists') 
                                ? 'bg-blue-600 text-gray-100 shadow-md' 
                                : 'text-gray-700 hover:bg-blue-600 hover:text-gray-100' }}">
                    <i data-lucide="user-check" 
                       class="w-5 h-5 transition-colors duration-300
                              {{ request()->routeIs('superadmin.manage-tourists') ? 'text-gray-100' : 'text-blue-600 group-hover:text-gray-100' }}"></i>
                    <span class="ml-3 font-medium">Tourist Management</span>
                </a>
            </li>

            <!-- Settings -->
            <li>
                <a href="{{ route('superadmin.settings') }}" 
                   class="group flex items-center px-4 py-3 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105
                          {{ request()->routeIs('superadmin.settings') 
                                ? 'bg-blue-600 text-gray-100 shadow-md' 
                                : 'text-gray-700 hover:bg-blue-600 hover:text-gray-100' }}">
                    <i data-lucide="settings" 
                       class="w-5 h-5 transition-colors duration-300
                              {{ request()->routeIs('superadmin.settings') ? 'text-gray-100' : 'text-blue-600 group-hover:text-gray-100' }}"></i>
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
                <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name ?? 'Super Admin' }}</p>
                <p class="text-xs text-gray-500">{{ auth()->user()->email ?? 'admin@system.com' }}</p>
            </div>
            <form method="POST" action="{{ route('superadmin.logout') }}" class="inline">
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
