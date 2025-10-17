@extends('layouts.superadmin')

@section('title', 'Settings')
@section('breadcrumb')
<li>
    <div class="flex items-center">
        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Settings</span>
    </div>
</li>
@endsection

@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">System Settings</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Change Superadmin Email -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Change Email</h3>
            <form id="changeEmailForm" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">New Email Address</label>
                    <input type="email" id="email" name="email" required
                           value="{{ auth()->user()->email }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Update Email
                    </button>
                </div>
            </form>
            <div id="emailMessage" class="mt-4 hidden"></div>
        </div>
    </div>

    <!-- Password Change Section -->
    <div class="mt-6">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h3>
            <form id="changePasswordForm" class="space-y-4">
                @csrf
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" id="new_password" name="new_password" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <button type="submit" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Change Password
                    </button>
                </div>
            </form>
            <div id="passwordMessage" class="mt-4 hidden"></div>
        </div>
    </div>
</div>

<script>
document.getElementById('changeEmailForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const messageDiv = document.getElementById('emailMessage');

    fetch('{{ route("superadmin.update-email") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(async response => {
        const contentType = response.headers.get('content-type') || '';
        const data = contentType.includes('application/json') ? await response.json() : { success: false, message: 'Unexpected response' };
        if (!response.ok) {
            throw data;
        }
        return data;
    })
    .then(data => {
        messageDiv.classList.remove('hidden');
        messageDiv.className = 'mt-4 p-4 rounded-lg bg-green-100 text-green-700 border border-green-200';
        messageDiv.textContent = data.message || 'Email updated successfully.';
    })
    .catch(err => {
        const message = (err && err.errors && err.errors.email && err.errors.email[0]) || err.message || 'Failed to update email.';
        messageDiv.classList.remove('hidden');
        messageDiv.className = 'mt-4 p-4 rounded-lg bg-red-100 text-red-700 border border-red-200';
        messageDiv.textContent = message;
    });
});

document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const messageDiv = document.getElementById('passwordMessage');
    
    fetch('{{ route("superadmin.change-password") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json())
    .then(data => {
        messageDiv.classList.remove('hidden');
        messageDiv.className = data.success ? 
            'mt-4 p-4 rounded-lg bg-green-100 text-green-700 border border-green-200' : 
            'mt-4 p-4 rounded-lg bg-red-100 text-red-700 border border-red-200';
        messageDiv.textContent = data.message;
        
        if (data.success) {
            document.getElementById('changePasswordForm').reset();
        }
    })
    .catch(error => {
        messageDiv.classList.remove('hidden');
        messageDiv.className = 'mt-4 p-4 rounded-lg bg-red-100 text-red-700 border border-red-200';
        messageDiv.textContent = 'An error occurred. Please try again.';
    });
});
</script>
@endsection
