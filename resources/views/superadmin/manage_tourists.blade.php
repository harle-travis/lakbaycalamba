@extends('layouts.superadmin')

@section('title', 'Manage Tourists - Super Admin')

@section('breadcrumb')
<li>
    <div class="flex items-center">
        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Tourist Management</span>
    </div>
</li>
@endsection

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Tourists</h1>
        <p class="text-gray-600">View and manage tourist accounts and their stamp collections</p>
    </div>

    @if($tourists->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tourist
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lakbay ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Stamps
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tourists as $tourist)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                <span class="text-white font-medium text-sm">
                                                    {{ strtoupper(substr($tourist->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $tourist->name }}</div>
                                            <div class="text-sm text-gray-500">Member since {{ $tourist->created_at->format('M Y') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $tourist->lakbay_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $tourist->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($tourist->stamps_count > 0)
                                        <button onclick="viewStamps({{ $tourist->id }}, '{{ $tourist->name }}')" 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 cursor-pointer transition">
                                            {{ $tourist->stamps_count }} stamps
                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                            0 stamps
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($tourist->stamps_count > 0)
                                        <button onclick="deleteAllStamps({{ $tourist->id }}, '{{ $tourist->name }}')" 
                                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md text-xs font-medium transition">
                                            Delete All Stamps
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-xs">No stamps to delete</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                    <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No Tourists Found</h3>
            <p class="text-gray-500">No tourist accounts have been created yet.</p>
        </div>
    @endif
</div>

<!-- Stamps Detail Modal -->
<div id="stampsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="stampsModalTitle">Tourist Stamps</h3>
                <button onclick="closeStampsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="stampsContent" class="max-h-96 overflow-y-auto">
                <!-- Stamps will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2" id="modalTitle">Confirm Action</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="modalMessage">Are you sure you want to perform this action?</p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmButton" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Delete
                </button>
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentAction = null;

function viewStamps(touristId, touristName) {
    // Fetch tourist stamps
    fetch(`/superadmin/tourists/${touristId}/stamps`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('stampsModalTitle').textContent = `${touristName}'s Stamps (${data.stamps.length})`;
                
                let stampsHtml = '';
                if (data.stamps.length > 0) {
                    stampsHtml = '<div class="space-y-3">';
                    data.stamps.forEach(stamp => {
                        stampsHtml += `
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">${stamp.establishment.establishment_name}</h4>
                                    <p class="text-sm text-gray-500">${stamp.establishment.location}</p>
                                    <p class="text-xs text-gray-400">Visited: ${new Date(stamp.visit_date).toLocaleDateString()}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">${stamp.establishment.category}</span>
                                    <button onclick="deleteStamp(${stamp.id}, '${stamp.establishment.establishment_name}')" 
                                            class="text-red-600 hover:text-red-800 p-1 hover:bg-red-50 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    stampsHtml += '</div>';
                } else {
                    stampsHtml = '<p class="text-gray-500 text-center py-8">No stamps collected yet.</p>';
                }
                
                document.getElementById('stampsContent').innerHTML = stampsHtml;
                document.getElementById('stampsModal').classList.remove('hidden');
            } else {
                showNotification('Failed to load stamps: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('An error occurred while loading stamps.', 'error');
        });
}

function closeStampsModal() {
    document.getElementById('stampsModal').classList.add('hidden');
}

function deleteStamp(stampId, establishmentName) {
    currentAction = () => {
        fetch(`/superadmin/tourists/stamps/${stampId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                location.reload();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('An error occurred while deleting the stamp.', 'error');
        });
    };
    
    document.getElementById('modalTitle').textContent = 'Delete Stamp';
    document.getElementById('modalMessage').textContent = `Are you sure you want to delete the stamp from "${establishmentName}"?`;
    document.getElementById('confirmModal').classList.remove('hidden');
}

function deleteAllStamps(touristId, touristName) {
    currentAction = () => {
        fetch(`/superadmin/tourists/${touristId}/stamps`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                location.reload();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('An error occurred while deleting the stamps.', 'error');
        });
    };
    
    document.getElementById('modalTitle').textContent = 'Delete All Stamps';
    document.getElementById('modalMessage').textContent = `Are you sure you want to delete ALL stamps for "${touristName}"? This action cannot be undone.`;
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    currentAction = null;
}

document.getElementById('confirmButton').addEventListener('click', function() {
    if (currentAction) {
        currentAction();
        closeModal();
    }
});

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection
