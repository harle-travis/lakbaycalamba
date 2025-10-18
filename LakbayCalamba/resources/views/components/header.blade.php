<!-- resources/views/components/header.blade.php -->
<header class="fixed top-0 left-0 w-full z-50 flex items-center justify-between px-4 sm:px-6 lg:px-8 py-3 sm:py-4 bg-white shadow-sm">
    <!-- Logo / Brand -->
    <div class="flex items-center space-x-2">
        <a href="{{ route('home') }}" class="cursor-pointer hover:opacity-80 transition-opacity">
            <img src="/images/header_logo.png" alt="Lakbay Calamba Logo" class="h-8 sm:h-10 md:h-12 w-auto">
        </a>
    </div>
    
    <div class="flex items-center gap-3 sm:gap-6 flex-wrap justify-end">            
        <!-- Mobile menu toggle -->
        <button id="mobile-menu-button" class="sm:hidden p-2 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-50" aria-label="Open menu" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4A1 1 0 013 5zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm1 4a1 1 0 100 2h12a1 1 0 100-2H4z" clip-rule="evenodd" />
            </svg>
        </button>
        <!-- Navigation Links -->
        <nav class="hidden sm:flex space-x-4 md:space-x-6">
            <a href="{{ route('aboutus') }}" class="text-gray-800 font-medium hover:text-blue-600 transition">About</a>
            <a href="{{ route('weather') }}" class="text-gray-800 font-medium hover:text-blue-600 transition">Weather</a>
            <a href="{{ route('leaderboard') }}" class="text-gray-800 font-medium hover:text-blue-600 transition">Leaderboard</a>
            
            @auth
                @if(auth()->user()->role === 'superadmin')
                    <a href="{{ route('superadmin.dashboard') }}" class="text-white font-medium bg-blue-600 px-3 py-1 rounded-md hover:bg-blue-700 transition">Superadmin Dashboard</a>
                @elseif(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dash') }}" class="text-white font-medium bg-blue-600 px-3 py-1 rounded-md hover:bg-blue-700 transition">Admin Dashboard</a>
                @endif
            @endauth
        </nav>
        
        <!-- Action Buttons -->
        <div class="hidden sm:flex gap-2 sm:gap-3 flex-wrap justify-end">
            @guest
                <a href="{{ route('login') }}" class="hidden sm:inline-flex px-3 py-2 sm:px-4 rounded-lg bg-blue-100 text-blue-800 text-sm sm:text-base font-medium hover:bg-blue-200 transition">My E-Stamps</a>
                <a href="{{ route('login') }}" class="hidden sm:inline-flex px-3 py-2 sm:px-4 rounded-lg bg-blue-600 text-white text-sm sm:text-base font-medium hover:bg-blue-700 transition">Sign In</a>
            @endguest
            @auth
                @if(auth()->user()->role !== 'admin' && auth()->user()->role !== 'superadmin')
                    <a href="{{ route('e-stamps') }}" class="hidden sm:inline-flex px-3 py-2 sm:px-4 rounded-lg bg-blue-100 text-blue-800 text-sm sm:text-base font-medium hover:bg-blue-200 transition">My E-Stamps</a>
                @endif
                
                @if(auth()->user()->role !== 'admin' && auth()->user()->role !== 'superadmin')
                    <div class="relative hidden sm:block">
                        <button id="user-menu-button" type="button" class="px-3 py-2 sm:px-4 rounded-lg bg-gray-100 text-gray-800 text-sm sm:text-base font-medium hover:bg-gray-200 transition flex items-center gap-2">
                            <span>{{ auth()->user()->name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.29a.75.75 0 01-.02-1.08z" clip-rule="evenodd"/></svg>
                        </button>
                        <div id="user-menu" class="hidden absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg py-1 z-50">
                            <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                            <a href="#" onclick="event.preventDefault(); if(confirm('Are you sure you want to sign out?')) document.getElementById('logout-form').submit();" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Sign Out</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Simple logout button for admin/superadmin -->
                    <div class="hidden sm:block">
                        <a href="#" onclick="event.preventDefault(); if(confirm('Are you sure you want to sign out?')) document.getElementById('logout-form-admin').submit();" class="px-3 py-2 sm:px-4 rounded-lg bg-gray-100 text-gray-800 text-sm sm:text-base font-medium hover:bg-gray-200 transition">
                            {{ auth()->user()->name }} - Sign Out
                        </a>
                        <form id="logout-form-admin" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    </div>
    <!-- Mobile dropdown menu -->
    <div id="mobile-menu" class="sm:hidden hidden absolute left-0 top-full w-full bg-white border-t border-gray-200 shadow-lg rounded-b-xl overflow-hidden">
        <div class="px-4 py-3 space-y-3">
            <div class="flex flex-col space-y-1">
                <a href="{{ route('aboutus') }}" class="px-3 py-2 rounded-md hover:bg-gray-50 text-gray-800 font-medium">About</a>
                <a href="{{ route('weather') }}" class="px-3 py-2 rounded-md hover:bg-gray-50 text-gray-800 font-medium">Weather</a>
                <a href="{{ route('leaderboard') }}" class="px-3 py-2 rounded-md hover:bg-gray-50 text-gray-800 font-medium">Leaderboard</a>
                
                @auth
                    @if(auth()->user()->role === 'superadmin')
                        <a href="{{ route('superadmin.dashboard') }}" class="px-3 py-2 rounded-md hover:bg-blue-50 text-white font-medium bg-blue-600 hover:bg-blue-700 transition">Superadmin Dashboard</a>
                    @elseif(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dash') }}" class="px-3 py-2 rounded-md hover:bg-blue-50 text-white font-medium bg-blue-600 hover:bg-blue-700 transition">Admin Dashboard</a>
                    @endif
                @endauth
            </div>
            <div class="h-px bg-gray-200"></div>
            <div class="flex flex-wrap gap-2">
                @guest
                    <a href="{{ route('login') }}" class="w-full text-center px-3 py-2 rounded-lg bg-blue-100 text-blue-800 text-sm font-medium hover:bg-blue-200 transition">My E-Stamps</a>
                    <a href="{{ route('login') }}" class="w-full text-center px-3 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">Sign In</a>
                @endguest
                @auth
                    @if(auth()->user()->role !== 'admin' && auth()->user()->role !== 'superadmin')
                        <a href="{{ route('e-stamps') }}" class="w-full text-center px-3 py-2 rounded-lg bg-blue-100 text-blue-800 text-sm font-medium hover:bg-blue-200 transition">My E-Stamps</a>
                        <a href="{{ route('settings') }}" class="w-full text-center px-3 py-2 rounded-lg bg-gray-100 text-gray-800 text-sm font-medium hover:bg-gray-200 transition">Settings</a>
                    @endif
                    <a href="#" onclick="event.preventDefault(); if(confirm('Are you sure you want to sign out?')) document.getElementById('logout-form-mobile').submit();" class="w-full text-center px-3 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition">Sign Out</a>
                    <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @endauth
            </div>
        </div>
    </div>
</header>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('user-menu-button');
    var menu = document.getElementById('user-menu');
    if (!btn || !menu) return;
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        menu.classList.toggle('hidden');
    });
    document.addEventListener('click', function(e) {
        if (!menu.classList.contains('hidden')) {
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.add('hidden');
            }
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var mobileBtn = document.getElementById('mobile-menu-button');
    var mobileMenu = document.getElementById('mobile-menu');
    if (mobileBtn && mobileMenu) {
        mobileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var isHidden = mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            mobileBtn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        });
        document.addEventListener('click', function(e) {
            if (!mobileMenu.classList.contains('hidden')) {
                if (!mobileMenu.contains(e.target) && !mobileBtn.contains(e.target)) {
                    mobileMenu.classList.add('hidden');
                    mobileBtn.setAttribute('aria-expanded', 'false');
                }
            }
        });
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 640 && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                mobileBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }
});
</script>