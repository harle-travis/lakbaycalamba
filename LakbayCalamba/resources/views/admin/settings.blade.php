@extends('layouts.admin')

@section('title', 'Profile Settings')

@section('content')
<div class="p-6 space-y-8">

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Profile Information --}}
    <div>
        <h2 class="text-lg font-semibold mb-4">Profile Information</h2>
        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            <form action="{{ route('settings.profile') }}" method="POST">
                @csrf
                @method('PUT')
                
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium mb-1">Name</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', auth()->user()->name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', auth()->user()->email) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200 @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role --}}
                <div class="text-gray-600">
                    <strong>Role:</strong> {{ ucfirst(auth()->user()->role) }}
                </div>

                {{-- Update Button --}}
                <div class="flex justify-center">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-500">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Password Change --}}
    <div>
        <h2 class="text-lg font-semibold mb-4">Change Password</h2>
        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('settings.password') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    {{-- Current Password --}}
                    <div>
                        <label for="current_password" class="block text-sm font-medium mb-1">Current Password</label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200 @error('current_password') border-red-500 @enderror"
                               required>
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium mb-1">New Password</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200 @error('password') border-red-500 @enderror"
                               required>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirm New Password</label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                               required>
                    </div>

                    {{-- Update Button --}}
                    <div class="flex justify-center">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-500">
                            Change Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Account Security --}}
    <div>
        <h2 class="text-lg font-semibold mb-4">Account Security</h2>
        <div class="bg-white shadow rounded-lg p-5 flex flex-col md:flex-row md:items-center md:justify-between">
            
            <div class="space-y-2">
                <div class="font-medium">Security Information</div>
                <div class="text-sm text-gray-600">
                    <p>• Email and password changes require email confirmation</p>
                    <p>• Check your email after making changes</p>
                    <p>• Links expire in 24 hours</p>
                </div>
            </div>

            <div class="flex flex-col gap-2 mt-4 md:mt-0">
                <a href="#" class="text-blue-600 hover:underline">
                    Contact City Hall Support
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
