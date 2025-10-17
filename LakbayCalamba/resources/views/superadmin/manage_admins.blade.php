@extends('layouts.superadmin')

@section('title', 'Manage Admins')
@section('breadcrumb')
<li>
    <div class="flex items-center">
        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Manage Admins</span>
    </div>
</li>
@endsection

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manage Admins</h2>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <div class="flex items-center space-x-4">
            <div class="flex-1">
                <input type="text" placeholder="Search admins..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
    </div>

    <!-- Admins List -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lakbay ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($admins as $admin)
                    <tr data-admin-id="{{ $admin->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $admin->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $admin->role }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                {{ $admin->lakbay_id }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="editAdmin({{ $admin->id }})">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900" onclick="deleteAdmin({{ $admin->id }})">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            No admins found. Admins are created when adding establishments.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Admin Modal -->
<div id="editAdminModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Edit Admin</h3>
                <button id="closeEditModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <!-- Form -->
            <form id="editAdminForm" class="space-y-6">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" id="edit_admin_id" name="admin_id">
                
                <!-- Name -->
                <div>
                    <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" id="edit_name" name="name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter admin name">
                </div>

                <!-- Email -->
                <div>
                    <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <input type="email" id="edit_email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter email address">
                </div>

                <!-- Password (Optional for edit) -->
                <div>
                    <label for="edit_password" class="block text-sm font-medium text-gray-700 mb-2">New Password (leave blank to keep current)</label>
                    <input type="password" id="edit_password" name="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter new password">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="edit_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" id="edit_password_confirmation" name="password_confirmation"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Confirm new password">
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" id="cancelEditBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Update Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editAdminModal');
    const closeEditBtn = document.getElementById('closeEditModal');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const editForm = document.getElementById('editAdminForm');

    // Close modal functions
    function closeEditModal() {
        editModal.classList.add('hidden');
        editForm.reset();
    }

    // Close button handlers
    closeEditBtn.addEventListener('click', closeEditModal);
    cancelEditBtn.addEventListener('click', closeEditModal);

    // Close modal when clicking outside
    editModal.addEventListener('click', function(e) {
        if (e.target === editModal) {
            closeEditModal();
        }
    });

    // Edit admin form submission
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const password = document.getElementById('edit_password').value;
        const passwordConfirmation = document.getElementById('edit_password_confirmation').value;
        
        if (password && password !== passwordConfirmation) {
            alert('Passwords do not match!');
            return;
        }

        const formData = new FormData(editForm);
        const adminId = document.getElementById('edit_admin_id').value;
        
        // Add the _method field for PUT request
        formData.append('_method', 'PUT');
        
        fetch(`/superadmin/admins/${adminId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Admin updated successfully!');
                closeEditModal();
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the admin.');
        });
    });

    // Edit admin function
    function editAdmin(id) {
        // Fetch admin data
        fetch(`/superadmin/admins/${id}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Populate form fields
            document.getElementById('edit_admin_id').value = data.id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_email').value = data.email;
            
            // Show modal
            editModal.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading admin data.');
        });
    }

    // Delete admin function
    function deleteAdmin(id) {
        if (confirm('Are you sure you want to delete this admin?')) {
            fetch(`/superadmin/admins/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the admin.');
            });
        }
    }

    // Make functions globally available
    window.editAdmin = editAdmin;
    window.deleteAdmin = deleteAdmin;
});
</script>
@endsection
