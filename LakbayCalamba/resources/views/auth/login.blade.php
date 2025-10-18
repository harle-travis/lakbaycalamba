<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lakbay Calamba - Sign In</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    
    <!-- Loading Styles -->
    <style>
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .btn-loading {
            opacity: 0.7;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        .btn-loading .btn-text {
            opacity: 0;
        }
        
        .btn-loading .loading-spinner {
            display: inline-block;
        }
        
        .loading-spinner {
            display: none;
        }
        
        /* Full page loader overlay */
        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .loader-content {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .loader-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e5e7eb;
            border-top: 4px solid #1e40af;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }
        
        .loader-text {
            color: #374151;
            font-weight: 500;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
        <!-- Left branding -->
        <div class="hidden lg:flex items-center justify-center bg-gray-100">
            <div class="max-w-md px-8 text-center">
                <div class="flex justify-center mb-8">
                    <a href="{{ route('home') }}" class="cursor-pointer hover:opacity-80 transition-opacity">
                        <img src="{{ url('images/mainlogo.png') }}" alt="Lakbay Calamba" class="w-72 md:w-96 h-auto">
                    </a>
                </div>
                <p class="text-gray-600 leading-relaxed">Experience culture, relax in nature and taste local flavors</p>
            </div>
        </div>

        <!-- Right auth card -->
        <div class="flex items-center justify-center py-10 sm:py-12 px-4 sm:px-6">
            <div class="w-full max-w-lg">
                <!-- Mobile Logo -->
                <div class="lg:hidden flex justify-center mb-6">
                    <a href="{{ route('home') }}" class="cursor-pointer hover:opacity-80 transition-opacity">
                        <img src="{{ url('images/mainlogo.png') }}" alt="Lakbay Calamba" class="w-40 sm:w-56 h-auto">
                    </a>
                </div>
                <!-- Tabs -->
                <div class="flex w-full rounded-md overflow-hidden bg-gray-100 mb-5 sm:mb-6">
                    <button id="tab-signin" class="flex-1 py-3 text-sm font-semibold transition {{ (isset($showTab) && $showTab === 'signup') ? 'bg-gray-100 text-gray-600' : 'bg-blue-900 text-white' }}">Sign In</button>
                    <button id="tab-signup" class="flex-1 py-3 text-sm font-semibold transition {{ (isset($showTab) && $showTab === 'signup') ? 'bg-blue-900 text-white' : 'bg-gray-100 text-gray-600' }}">Sign Up</button>
                </div>

                @if ($errors->any())
                    <div class="mb-4 text-sm text-red-600">{{ $errors->first() }}</div>
                @endif

                <!-- Sign In Form -->
                <div id="panel-signin" class="{{ (isset($showTab) && $showTab === 'signup') ? 'hidden' : '' }}">
                    <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
                        @csrf
                        @if(isset($redirect))
                            <input type="hidden" name="redirect" value="{{ $redirect }}">
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" required autofocus class="w-full rounded-md border border-blue-300 focus:ring-2 focus:ring-blue-500 focus:outline-none px-4 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" name="password" required class="w-full rounded-md border border-blue-300 focus:ring-2 focus:ring-blue-500 focus:outline-none px-4 py-2">
                        </div>
                        <button type="submit" id="loginBtn" class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold rounded-md py-3 transition flex items-center justify-center space-x-2">
                            <span class="btn-text">Log In</span>
                            <div class="loading-spinner"></div>
                        </button>
                    </form>
                    <div class="mt-3 text-center">
                        <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline text-sm">Forgot Password?</a>
                    </div>
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600">Don't have an account yet? <a href="#" id="link-to-signup" class="text-blue-600 hover:underline font-medium">Sign up here</a></p>
                    </div>
                </div>

                <!-- Sign Up Form -->
                <div id="panel-signup" class="{{ (isset($showTab) && $showTab === 'signup') ? '' : 'hidden' }}">
                    <form id="signup-form" method="POST" action="{{ route('signup.submit') }}" class="space-y-4">
                        @csrf
                        @if(isset($redirect))
                            <input type="hidden" name="redirect" value="{{ $redirect }}">
                        @endif
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input id="first_name" type="text" class="w-full rounded-md border border-blue-300 focus:ring-2 focus:ring-blue-500 focus:outline-none px-4 py-2" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input id="last_name" type="text" class="w-full rounded-md border border-blue-300 focus:ring-2 focus:ring-blue-500 focus:outline-none px-4 py-2" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" class="w-full rounded-md border border-blue-300 focus:ring-2 focus:ring-blue-500 focus:outline-none px-4 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Create Password</label>
                            <input type="password" name="password" class="w-full rounded-md border border-blue-300 focus:ring-2 focus:ring-blue-500 focus:outline-none px-4 py-2" required>
                        </div>
                        <input type="hidden" name="name" id="full_name">
                        <input type="hidden" name="password_confirmation" id="password_confirmation_hidden">
                        <button type="submit" id="signupBtn" class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold rounded-md py-3 transition flex items-center justify-center space-x-2">
                            <span class="btn-text">Join</span>
                            <div class="loading-spinner"></div>
                        </button>
                    </form>
                    <div class="mt-6 flex items-center text-gray-400">
                        <div class="flex-1 h-px bg-gray-300"></div>
                        <div class="px-4 text-sm">Already a Member?</div>
                        <div class="flex-1 h-px bg-gray-300"></div>
                    </div>
                    <div class="text-center mt-3">
                        <a id="link-to-signin" href="{{ route('login') }}" class="text-blue-600 hover:underline text-sm">Sign In</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Loading utility functions
        function showButtonLoading(buttonId) {
            const button = document.getElementById(buttonId);
            if (button) {
                button.classList.add('btn-loading');
                button.disabled = true;
            }
        }

        function hideButtonLoading(buttonId) {
            const button = document.getElementById(buttonId);
            if (button) {
                button.classList.remove('btn-loading');
                button.disabled = false;
            }
        }

        function showFullPageLoader(message = 'Processing...') {
            // Remove existing loader if any
            const existingLoader = document.querySelector('.loader-overlay');
            if (existingLoader) {
                existingLoader.remove();
            }
            
            const loader = document.createElement('div');
            loader.className = 'loader-overlay';
            loader.innerHTML = `
                <div class="loader-content">
                    <div class="loader-spinner"></div>
                    <div class="loader-text">${message}</div>
                </div>
            `;
            document.body.appendChild(loader);
        }

        function hideFullPageLoader() {
            const loader = document.querySelector('.loader-overlay');
            if (loader) {
                loader.remove();
            }
        }

        (function() {
            const tabSignin = document.getElementById('tab-signin');
            const tabSignup = document.getElementById('tab-signup');
            const panelSignin = document.getElementById('panel-signin');
            const panelSignup = document.getElementById('panel-signup');

            function activate(tab) {
                const isSignup = tab === 'signup';
                panelSignin.classList.toggle('hidden', isSignup);
                panelSignup.classList.toggle('hidden', !isSignup);

                // Toggle tab styles
                if (isSignup) {
                    tabSignup.classList.remove('bg-gray-100','text-gray-600');
                    tabSignup.classList.add('bg-blue-900','text-white');
                    tabSignin.classList.remove('bg-blue-900','text-white');
                    tabSignin.classList.add('bg-gray-100','text-gray-600');
                } else {
                    tabSignin.classList.remove('bg-gray-100','text-gray-600');
                    tabSignin.classList.add('bg-blue-900','text-white');
                    tabSignup.classList.remove('bg-blue-900','text-white');
                    tabSignup.classList.add('bg-gray-100','text-gray-600');
                }
            }

            if (tabSignin && tabSignup) {
                tabSignin.addEventListener('click', function(e){ e.preventDefault(); activate('signin'); });
                tabSignup.addEventListener('click', function(e){ e.preventDefault(); activate('signup'); });
            }

            // Handle signup link in signin panel
            const linkToSignup = document.getElementById('link-to-signup');
            if (linkToSignup) {
                linkToSignup.addEventListener('click', function(e) {
                    e.preventDefault();
                    activate('signup');
                });
            }

            // Build hidden signup fields on submit
            const signupForm = document.getElementById('signup-form');
            if (signupForm) {
                signupForm.addEventListener('submit', function() {
                    // Show loading state
                    showButtonLoading('signupBtn');
                    showFullPageLoader('Creating your account...');
                    
                    const first = document.getElementById('first_name').value.trim();
                    const last = document.getElementById('last_name').value.trim();
                    const full = (first + ' ' + last).trim();
                    document.getElementById('full_name').value = full;
                    // Mirror password into confirmation hidden field
                    const pwInput = signupForm.querySelector('input[name="password"]');
                    document.getElementById('password_confirmation_hidden').value = pwInput ? pwInput.value : '';
                });
            }

            // Login form submission handler
            const loginForm = document.querySelector('form[action="{{ route('login.submit') }}"]');
            if (loginForm) {
                loginForm.addEventListener('submit', function() {
                    // Show loading state
                    showButtonLoading('loginBtn');
                    showFullPageLoader('Signing you in...');
                });
            }
        })();
    </script>
</body>
</html>