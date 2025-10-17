@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container mx-auto max-w-4xl py-8">
    {{-- Back Button --}}
    <a href="{{ url()->previous() }}" class="btn btn-link mb-6">← Back</a>

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Settings</h1>
        <p class="text-gray-600">Manage your account settings and preferences</p>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Profile Information Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <div class="p-3 bg-blue-100 rounded-lg mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Profile Information</h2>
                    <p class="text-gray-600">Update your personal details</p>
                </div>
            </div>

            <form action="{{ route('settings.profile') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $user->name) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="lakbay_id" class="block text-sm font-medium text-gray-700 mb-2">Lakbay ID</label>
                    <input type="text" 
                           id="lakbay_id" 
                           value="{{ $user->lakbay_id }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-500"
                           readonly>
                    <p class="mt-1 text-sm text-gray-500">Your unique Lakbay ID cannot be changed</p>
                </div>

                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Update Profile
                </button>
            </form>
        </div>

        {{-- Password Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <div class="p-3 bg-green-100 rounded-lg mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Change Password</h2>
                    <p class="text-gray-600">Update your account password</p>
                </div>
            </div>

            <form action="{{ route('settings.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input type="password" 
                           id="current_password" 
                           name="current_password" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('current_password') border-red-500 @enderror"
                           required>
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                           required>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>

                <button type="submit" 
                        class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-green-700 transition-colors focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Update Password
                </button>
            </form>
        </div>
    </div>

    {{-- Account Information Section --}}
    <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center mb-6">
            <div class="p-3 bg-gray-100 rounded-lg mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Account Information</h2>
                <p class="text-gray-600">View your account details and statistics</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $user->stamps->count() }}</div>
                <div class="text-sm text-gray-600">E-Stamps Earned</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $user->favorites->count() }}</div>
                <div class="text-sm text-gray-600">Favorite Places</div>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ $user->reviews->count() }}</div>
                <div class="text-sm text-gray-600">Reviews Written</div>
            </div>
        </div>
    </div>
</div>

<style>
.btn {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-link {
    color: #6b7280;
    background-color: transparent;
    border: 1px solid #d1d5db;
}

.btn-link:hover {
    color: #374151;
    background-color: #f9fafb;
    border-color: #9ca3af;
}
</style>
@endsection
