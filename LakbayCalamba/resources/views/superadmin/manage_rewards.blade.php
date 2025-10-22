@extends('layouts.superadmin')

@section('title', 'Manage Rewards')
@section('breadcrumb')
<li>
    <div class="flex items-center">
        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Manage Rewards</span>
    </div>
</li>
@endsection

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Reward Management</h1>
            <p class="text-gray-600 mt-1">Manage users eligible for rewards (9+ stamps)</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500">
                Total Eligible: {{ $rewardEligibleUsers->count() }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    <!-- Reward Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-3 mr-4">
                    <i data-lucide="award" class="w-6 h-6 text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Eligible</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $rewardEligibleUsers->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3 mr-4">
                    <i data-lucide="star" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Max Stamps</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $rewardEligibleUsers->max('stamps_count') ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-full p-3 mr-4">
                    <i data-lucide="trending-up" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Average Stamps</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($rewardEligibleUsers->avg('stamps_count'), 1) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    @if($rewardEligibleUsers->count() > 0)
    <form id="bulkNotificationForm" method="POST" action="{{ route('superadmin.send-reward-notifications') }}">
        @csrf
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Bulk Actions</h3>
            <div class="flex items-center space-x-4 mb-4">
                <button type="button" id="selectAllBtn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition-colors">
                    Select All
                </button>
                <button type="button" id="deselectAllBtn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition-colors">
                    Deselect All
                </button>
                <button type="button" id="editEmailBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center space-x-2">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                    <span>Edit Email</span>
                </button>
                <button type="button" id="previewEmailBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center space-x-2">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                    <span>Preview Email</span>
                </button>
                <button type="submit" id="sendNotificationsBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm transition-colors flex items-center space-x-2">
                    <i data-lucide="mail" class="w-4 h-4"></i>
                    <span>Send Notifications</span>
                </button>
            </div>
            
            <!-- Email Editor (Hidden by default) -->
            <div id="emailEditor" class="hidden bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Email Settings -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">Email Settings</h4>
                        
                        <div class="mb-4">
                            <label for="emailSubject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <input type="text" 
                                   id="emailSubject" 
                                   name="email_subject" 
                                   value="🎉 Reward Eligibility - Tourism Monitoring System"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" id="useCustomContent" name="use_custom_content" class="rounded border-gray-300 mr-2">
                                <span class="text-sm text-gray-700">Use custom email content</span>
                            </label>
                        </div>
                        
                        <div id="customContentEditor" class="hidden">
                            <label for="emailContent" class="block text-sm font-medium text-gray-700 mb-2">Custom Message</label>
                            <textarea id="emailContent" 
                                      name="email_content" 
                                      rows="8"
                                      placeholder="Dear {user_name},

Congratulations! You have collected {stamps_count} stamps and are now eligible for a special reward.

Your Lakbay ID: {lakbay_id}

Please visit the Calamba Tourism Office to claim your reward.

Best regards,
Calamba Tourism Office"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            <div class="mt-2">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-xs text-gray-500">Available placeholders:</p>
                                    <button type="button" id="loadSampleTemplate" class="text-xs text-blue-600 hover:text-blue-800 underline">
                                        Load Sample Template
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{user_name}</span>
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{user_email}</span>
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{lakbay_id}</span>
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{stamps_count}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preview Area -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-800">Preview</h4>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-500">Select one user to preview</span>
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            </div>
                        </div>
                        <div id="emailPreview" class="bg-white border border-gray-300 rounded-lg p-4 min-h-64 max-h-96 overflow-y-auto">
                            <div class="text-center py-8">
                                <i data-lucide="mail" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                            <p class="text-gray-500 text-sm">Click "Preview Email" to see how the email will look.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-4">
                    <button type="button" id="closeEmailEditor" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition-colors">
                        Close Editor
                    </button>
                    <button type="button" id="resetEmailSettings" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-4 py-2 rounded-lg text-sm transition-colors">
                        Reset to Default
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Eligible Users</h3>
            
            @if($rewardEligibleUsers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                                </th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">User</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Lakbay ID</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Stamps</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Establishments Visited</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Last Visit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($rewardEligibleUsers as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox rounded border-gray-300">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="bg-gray-200 rounded-full p-2 mr-3">
                                            <i data-lucide="user" class="w-4 h-4 text-gray-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $user->lakbay_id }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $user->stamps_count }} stamps
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    <div class="max-w-xs">
                                        @foreach($user->stamps->take(3) as $stamp)
                                            <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                                                {{ $stamp->establishment->establishment_name }}
                                            </span>
                                        @endforeach
                                        @if($user->stamps->count() > 3)
                                            <span class="text-xs text-gray-500">+{{ $user->stamps->count() - 3 }} more</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $user->stamps->max('visit_date') ? $user->stamps->max('visit_date')->format('M d, Y') : 'N/A' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i data-lucide="award" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No eligible users yet</h3>
                    <p class="text-gray-500">Users need at least 9 stamps to be eligible for rewards.</p>
                </div>
            @endif
        </div>
    </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const deselectAllBtn = document.getElementById('deselectAllBtn');
    const sendNotificationsBtn = document.getElementById('sendNotificationsBtn');

    // Select All functionality
    selectAllCheckbox.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Individual checkbox change
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === userCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < userCheckboxes.length;
        });
    });

    // Select All button
    selectAllBtn.addEventListener('click', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
    });

    // Deselect All button
    deselectAllBtn.addEventListener('click', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    });

    // Send notifications button
    sendNotificationsBtn.addEventListener('click', function(e) {
        console.log('Send notifications button clicked');
        
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        const checkedUsers = document.querySelectorAll('.user-checkbox:checked');
        
        console.log('Checked users count:', checkedCount);
        console.log('Checked users:', Array.from(checkedUsers).map(cb => cb.value));
        
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Please select at least one user to send notifications.');
            return;
        }

        if (!confirm(`Send reward notifications to ${checkedCount} selected user(s)?`)) {
            e.preventDefault();
            return;
        }

        // Show loading state
        const originalText = this.innerHTML;
        this.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>Sending...';
        this.disabled = true;

        // Log form data before submission
        const form = document.getElementById('bulkNotificationForm');
        const formData = new FormData(form);
        console.log('Form data being submitted:');
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }

        // Show immediate feedback
        showNotification(`Sending notifications to ${checkedCount} user(s)...`, 'info');

        // Add form submission event listener for debugging
        form.addEventListener('submit', function(e) {
            console.log('Form is being submitted!');
            console.log('Form action:', this.action);
            console.log('Form method:', this.method);
        });

        // Re-enable button after form submission (in case of validation errors)
        setTimeout(() => {
            this.innerHTML = originalText;
            this.disabled = false;
        }, 5000);
    });

    // Email Editor functionality
    const editEmailBtn = document.getElementById('editEmailBtn');
    const previewEmailBtn = document.getElementById('previewEmailBtn');
    const emailEditor = document.getElementById('emailEditor');
    const closeEmailEditor = document.getElementById('closeEmailEditor');
    const useCustomContent = document.getElementById('useCustomContent');
    const customContentEditor = document.getElementById('customContentEditor');
    const emailContent = document.getElementById('emailContent');
    const emailSubject = document.getElementById('emailSubject');
    const emailPreview = document.getElementById('emailPreview');
    const resetEmailSettings = document.getElementById('resetEmailSettings');
    const loadSampleTemplate = document.getElementById('loadSampleTemplate');

    /

    // Edit email button functionality
    editEmailBtn.addEventListener('click', function() {
       
        if (emailEditor.classList.contains('hidden')) {
            emailEditor.classList.remove('hidden');
            editEmailBtn.innerHTML = '<i data-lucide="eye-off" class="w-4 h-4"></i><span>Hide Editor</span>';
            editEmailBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            editEmailBtn.classList.add('bg-gray-600', 'hover:bg-gray-700');
             // Scroll to editor
             emailEditor.scrollIntoView({ behavior: 'smooth' });
        } else {
            emailEditor.classList.add('hidden');
            editEmailBtn.innerHTML = '<i data-lucide="edit" class="w-4 h-4"></i><span>Edit Email</span>';
            editEmailBtn.classList.remove('bg-gray-600', 'hover:bg-gray-700');
            editEmailBtn.classList.add('bg-green-600', 'hover:bg-green-700');
        }
    });

     // Close email editor
     closeEmailEditor.addEventListener('click', function() {
        emailEditor.classList.add('hidden');
        editEmailBtn.innerHTML = '<i data-lucide="edit" class="w-4 h-4"></i><span>Edit Email</span>';
        editEmailBtn.classList.remove('bg-gray-600', 'hover:bg-gray-700');
        editEmailBtn.classList.add('bg-green-600', 'hover:bg-green-700');
    });

    // Toggle custom content editor
    useCustomContent.addEventListener('change', function() {
        if (this.checked) {
            customContentEditor.classList.remove('hidden');
        } else {
            customContentEditor.classList.add('hidden');
        }
        // Clear preview when toggling
        emailPreview.innerHTML = '<div class="text-center py-8"><i data-lucide="mail" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i><p class="text-gray-500 text-sm">Click "Preview Email" to see how the email will look.</p></div>';
    });

    // Preview email functionality
    previewEmailBtn.addEventListener('click', function() {
        const checkedUsers = document.querySelectorAll('.user-checkbox:checked');
        
        
        const subject = emailSubject.value;
        const content = emailContent.value;
        const useCustom = useCustomContent.checked;

        // Show loading state
        emailPreview.innerHTML = '<div class="flex items-center justify-center p-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2 text-gray-600">Loading preview...</span></div>';

        // Use first available user for preview, or create a sample user
        let userId = null;
        if (checkedUsers.length > 0) {
            userId = checkedUsers[0].value;
        } else {
            // Use the first user from the list for preview
            const firstUserCheckbox = document.querySelector('.user-checkbox');
            if (firstUserCheckbox) {
                userId = firstUserCheckbox.value;
            }
        }

        // Make AJAX request to preview email
        fetch('{{ route("superadmin.preview-email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                user_id: userId,
                email_subject: subject,
                email_content: content,
                use_custom_content: useCustom
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                emailPreview.innerHTML = `
                    <div class="border border-gray-300 rounded-lg p-4 bg-white">
                        <div class="mb-3 pb-2 border-b border-gray-200">
                            <h4 class="font-semibold text-gray-800">Subject: ${data.subject}</h4>
                        </div>
                        <div class="email-content">
                            ${data.content}
                        </div>
                    </div>
                `;
            } else {
                emailPreview.innerHTML = `<div class="text-red-600 p-4">Error: ${data.error || 'Failed to preview email'}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            emailPreview.innerHTML = '<div class="text-red-600 p-4">Error loading email preview. Please try again.</div>';
        });
    });

    // Reset email settings
    resetEmailSettings.addEventListener('click', function() {
        if (confirm('Are you sure you want to reset all email settings to default?')) {
            emailSubject.value = '🎉 Reward Eligibility - Tourism Monitoring System';
            emailContent.value = '';
            useCustomContent.checked = false;
            customContentEditor.classList.add('hidden');
            emailPreview.innerHTML = '<div class="text-center py-8"><i data-lucide="mail" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i><p class="text-gray-500 text-sm">Click "Preview Email" to see how the email will look.</p></div>';
        }
    });

    // Real-time preview update when content changes
    let previewTimeout;
    function updatePreview() {
        clearTimeout(previewTimeout);
        previewTimeout = setTimeout(() => {
            const checkedUsers = document.querySelectorAll('.user-checkbox:checked');
            if (checkedUsers.length === 1) {
                previewEmailBtn.click();
            }
        }, 1000); // Debounce for 1 second
    }

    // Load sample template
    loadSampleTemplate.addEventListener('click', function() {
        const sampleTemplate = `Dear {user_name},

🎉 Congratulations! You have collected {stamps_count} stamps and are now eligible for a special reward!

Your Lakbay ID: {lakbay_id}

How to Claim Your Reward:
1. Visit the Calamba Tourism Office
2. Present your Lakbay ID
3. Show this email as proof of eligibility
4. Claim your reward!

Tourism Office Location:
Calamba City Hall
Calamba City, Laguna

Office Hours:
Monday to Friday: 8:00 AM - 5:00 PM
Saturday: 8:00 AM - 12:00 PM

Thank you for exploring our beautiful tourist destinations in Calamba!

Best regards,
Calamba Tourism Office`;
        
        emailContent.value = sampleTemplate;
        updatePreview();
    });

    // Add event listeners for real-time preview
    emailSubject.addEventListener('input', updatePreview);
    emailContent.addEventListener('input', updatePreview);
    useCustomContent.addEventListener('change', updatePreview);

    // Show success notification if there's a success message
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif

    // Debug: Check form structure
    console.log('=== Form Debug Info ===');
    const form = document.getElementById('bulkNotificationForm');
    if (form) {
        console.log('Form found:', form);
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
        console.log('Form checkboxes:', document.querySelectorAll('.user-checkbox').length);
        
        // Add form submission handler for debugging
        form.addEventListener('submit', function(e) {
            const checkedUsers = document.querySelectorAll('.user-checkbox:checked');
            console.log('Form submitting with', checkedUsers.length, 'users selected');
            
            if (checkedUsers.length === 0) {
                e.preventDefault();
                alert('Please select at least one user to send notifications to.');
                return false;
            }
        });
    } else {
        console.error('Form not found!');
    }
});

// Notification system
function showNotification(message, type = 'success') {
    // Remove any existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    let borderColor, icon, iconColor, textColor;
    
    switch(type) {
        case 'success':
            borderColor = 'border-green-500';
            icon = 'check-circle';
            iconColor = 'text-green-600';
            textColor = 'text-green-800';
            break;
        case 'error':
            borderColor = 'border-red-500';
            icon = 'alert-circle';
            iconColor = 'text-red-600';
            textColor = 'text-red-800';
            break;
        case 'info':
        default:
            borderColor = 'border-blue-500';
            icon = 'info';
            iconColor = 'text-blue-600';
            textColor = 'text-blue-800';
            break;
    }
    
    notification.className = `notification-toast fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 ${borderColor} transform transition-all duration-300 ease-in-out translate-x-full`;
    
    notification.innerHTML = `
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i data-lucide="${icon}" class="w-6 h-6 ${iconColor}"></i>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-sm font-medium ${textColor}">
                        ${message}
                    </p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button class="bg-white rounded-md inline-flex ${iconColor} hover:opacity-75 focus:outline-none" onclick="this.closest('.notification-toast').remove()">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Trigger re-render of Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}
</script>
@endsection
