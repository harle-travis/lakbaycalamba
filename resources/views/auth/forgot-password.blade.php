<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - Lakbay Calamba</title>
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
                        <img src="{{ asset('images/mainlogo.png') }}" alt="Lakbay Calamba" class="w-72 md:w-96 h-auto">
                    </a>
                </div>
                <p class="text-gray-600 leading-relaxed">Forgot your password? No worries! Enter your email address and we'll send you a link to reset it.</p>
            </div>
        </div>

        <!-- Right form card -->
        <div class="flex items-center justify-center py-10 sm:py-12 px-4 sm:px-6">
            <div class="w-full max-w-lg">
                <!-- Mobile Logo -->
                <div class="lg:hidden flex justify-center mb-6">
                    <a href="{{ route('home') }}" class="cursor-pointer hover:opacity-80 transition-opacity">
                        <img src="{{ asset('images/mainlogo.png') }}" alt="Lakbay Calamba" class="w-40 sm:w-56 h-auto">
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Forgot Password?</h2>
                    
                    @if (session('status'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                required 
                                autofocus 
                                class="w-full rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-3"
                                placeholder="Enter your email address"
                            >
                        </div>

                        <button 
                            type="submit" 
                            id="forgotPasswordBtn"
                            class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold rounded-md py-3 transition duration-200 flex items-center justify-center space-x-2"
                        >
                            <span class="btn-text">Send Reset Link</span>
                            <div class="loading-spinner"></div>
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline text-sm">
                            ← Back to Sign In
                        </a>
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

        // Forgot password form submission handler
        document.addEventListener('DOMContentLoaded', function() {
            const forgotPasswordForm = document.querySelector('form[action="{{ route('password.email') }}"]');
            if (forgotPasswordForm) {
                forgotPasswordForm.addEventListener('submit', function() {
                    // Show loading state
                    showButtonLoading('forgotPasswordBtn');
                    showFullPageLoader('Sending reset link...');
                });
            }
        });
    </script>
</body>
</html>
