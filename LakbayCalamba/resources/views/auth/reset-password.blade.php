<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Lakbay Calamba</title>
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
                <p class="text-gray-600 leading-relaxed">Create a new password for your account. Make sure it's secure and easy for you to remember.</p>
            </div>
        </div>

        <!-- Right form card -->
        <div class="flex items-center justify-center py-10 sm:py-12 px-4 sm:px-6">
            <div class="w-full max-w-lg">
                <!-- Mobile Logo -->
                <div class="lg:hidden flex justify-center mb-6">
                    <a href="{{ route('home') }}" class="cursor-pointer hover:opacity-80 transition-opacity">
                        <img src="{{ url('images/mainlogo.png') }}" alt="Lakbay Calamba" class="w-40 sm:w-56 h-auto">
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Reset Password</h2>
                    
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input 
                                type="email" 
                                id="email" 
                                value="{{ $email }}"
                                disabled
                                class="w-full rounded-md border border-gray-300 bg-gray-100 px-4 py-3 text-gray-500"
                            >
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required 
                                autofocus 
                                class="w-full rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-3"
                                placeholder="Enter your new password"
                            >
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                required 
                                class="w-full rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-3"
                                placeholder="Confirm your new password"
                            >
                        </div>

                        <button 
                            type="submit" 
                            id="resetPasswordBtn"
                            class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold rounded-md py-3 transition duration-200 flex items-center justify-center space-x-2"
                        >
                            <span class="btn-text">Reset Password</span>
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

        // Reset password form submission handler
        document.addEventListener('DOMContentLoaded', function() {
            const resetPasswordForm = document.querySelector('form[action="{{ route('password.update') }}"]');
            if (resetPasswordForm) {
                resetPasswordForm.addEventListener('submit', function() {
                    // Show loading state
                    showButtonLoading('resetPasswordBtn');
                    showFullPageLoader('Resetting your password...');
                });
            }
        });
    </script>
</body>
</html>
