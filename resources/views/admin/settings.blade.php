@extends('layouts.admin')

@section('title', 'Profile Settings')

@section('content')
<div class="p-6 space-y-8">

    {{-- Profile Information --}}
    <div>
        <h2 class="text-lg font-semibold mb-4">Profile Information</h2>
        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            
            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" value="Admin Name"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" value="admin@example.com"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
            </div>

            {{-- Role --}}
            <div class="text-gray-600">
                Establishment Admin
            </div>

            {{-- Update Button --}}
            <div class="flex justify-center">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-500">
                    Update
                </button>
            </div>

        </div>
    </div>

    {{-- Account Security --}}
    <div>
        <h2 class="text-lg font-semibold mb-4">Account Security</h2>
        <div class="bg-white shadow rounded-lg p-5 flex flex-col md:flex-row md:items-center md:justify-between">
            
            <div class="space-y-2">
                <div class="font-medium">Manage Account Security</div>
                <div class="font-medium">Support & Help</div>
            </div>

            <div class="flex flex-col gap-2 mt-4 md:mt-0">
                <button class="border border-gray-300 px-4 py-2 rounded hover:bg-gray-50">
                    Change Password
                </button>
                <a href="#" class="text-blue-600 hover:underline">
                    Contact City Hall Support
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
