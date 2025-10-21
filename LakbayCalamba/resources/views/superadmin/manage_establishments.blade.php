@extends('layouts.superadmin')

@section('title', 'Manage Establishments')
@section('breadcrumb')
<li>
    <div class="flex items-center">
        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Manage Establishments</span>
    </div>
</li>
@endsection

@section('content')
<!-- Google Maps API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAqTc2s31QM_gQanGqCxE1hw5QI0OxoUbg&libraries=places&callback=initMaps"></script>

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
        border-top: 4px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 1rem;
    }
    
    .loader-text {
        color: #374151;
        font-weight: 500;
    }
    
    /* QR Code table improvements */
    .qr-code-container {
        min-width: 80px;
        min-height: 80px;
    }
    
    .qr-actions-container {
        min-width: 120px;
    }
    
    .qr-actions-container button {
        min-width: 100px;
        white-space: nowrap;
    }
</style>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manage Establishments</h2>
        <button id="addEstablishmentBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add New Establishment</span>
        </button>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <div class="flex items-center space-x-4">
            <div class="flex-1">
                <input type="text" placeholder="Search establishments..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option>All Categories</option>
                <option>Restaurant</option>
                <option>Hotel</option>
                <option>Attraction</option>
                <option>Park</option>
                <option>Museum</option>
                <option>Resort</option>
                <option>Mall</option>
                <option>Church</option>
                <option>Cafe</option>
                <option>Bar</option>
                <option>Historical Site</option>
                <option>Other</option>
            </select>
            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option>All Status</option>
                <option>Active</option>
                <option>Inactive</option>
                <option>Pending</option>
            </select>
        </div>
    </div>

    <!-- Establishments List -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full min-w-max">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Establishment Name</th>
                                                 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photos</th>
                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Actions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                                         @forelse($establishments as $establishment)
                     <tr data-establishment-id="{{ $establishment->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    @if($establishment->pictures->count() > 0)
                                        <img src="{{ Storage::url($establishment->pictures->first()->image_path) }}" 
                                             alt="{{ $establishment->establishment_name }}" 
                                             class="w-10 h-10 rounded-lg object-cover">
                                    @else
                                        <i data-lucide="building" class="w-5 h-5 text-blue-600"></i>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $establishment->establishment_name }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($establishment->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($establishment->category)
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $establishment->category }}
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-medium">
                                    Not Set
                                </span>
                            @endif
                        </td>
                                                 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $establishment->location }}</td>
                         <td class="px-6 py-4 whitespace-nowrap">
                             @if($establishment->pictures->count() > 0)
                                 <button onclick="viewPhotos({{ $establishment->id }})" class="text-blue-600 hover:text-blue-900 flex items-center space-x-1">
                                     <i data-lucide="image" class="w-4 h-4"></i>
                                     <span class="text-sm">{{ $establishment->pictures->count() }} photo(s)</span>
                                 </button>
                             @else
                                 <span class="text-gray-400 text-sm">No photos</span>
                             @endif
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap qr-code-container">
                             @if($establishment->qr_code)
                                 <div class="flex justify-center">
                                     <div class="w-12 h-12 bg-white border border-gray-200 rounded-lg flex items-center justify-center p-2">
                                         {!! $establishment->qr_code !!}
                                     </div>
                                 </div>
                             @else
                                 <div class="text-center">
                                     <div class="w-20 h-20 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center">
                                         <i data-lucide="qr-code" class="w-8 h-8 text-gray-400"></i>
                                     </div>
                                 </div>
                             @endif
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap qr-actions-container">
                             @if($establishment->qr_code)
                                 <div class="flex flex-col space-y-2">
                                     <button onclick="viewQRCode({{ $establishment->id }})" class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition-colors flex items-center justify-center space-x-1">
                                         <i data-lucide="eye" class="w-3 h-3"></i>
                                         <span>View & Test</span>
                                     </button>
                                     <button onclick="regenerateQRCode({{ $establishment->id }})" class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 transition-colors flex items-center justify-center space-x-1">
                                         <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                                         <span>Regenerate</span>
                                     </button>
                                 </div>
                             @else
                                 <div class="flex justify-center">
                                     <button onclick="generateQRCode({{ $establishment->id }})" class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition-colors flex items-center justify-center space-x-1">
                                         <i data-lucide="plus" class="w-3 h-3"></i>
                                         <span>Generate</span>
                                     </button>
                                 </div>
                             @endif
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="editEstablishment({{ $establishment->id }})">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900" onclick="deleteEstablishment({{ $establishment->id }})">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No establishments found. Add your first establishment!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Establishment Modal -->
<div id="addEstablishmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Add New Establishment</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <!-- Form -->
            <form id="establishmentForm" class="space-y-6" enctype="multipart/form-data">
                @csrf
                <!-- Establishment Name -->
                <div>
                    <label for="establishment_name" class="block text-sm font-medium text-gray-700 mb-2">Establishment Name *</label>
                    <input type="text" id="establishment_name" name="establishment_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter establishment name">
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                    <input type="text" id="location" name="location" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter location">
                </div>

                <!-- Location Picker -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location on Map</label>
                    <div class="border border-gray-300 rounded-lg overflow-hidden">
                        <div id="map" style="height: 300px; width: 100%;"></div>
                    </div>
                    <div class="mt-2 flex space-x-2">
                        <button type="button" id="getCurrentLocation" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            Use Current Location
                        </button>
                        <button type="button" id="searchLocation" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                            Search Location
                        </button>
                    </div>
                    <input type="hidden" id="maps_data" name="maps_data">
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">
                </div>

                <!-- Short Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Short Description *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Enter a brief description of the establishment"></textarea>
                </div>

                <!-- Schedule -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Schedule *</label>
                    
                    <!-- 24 Hours Option -->
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="open_24_hours" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" onchange="toggle24Hours()">
                            <span class="ml-2 text-sm font-medium text-gray-700">Open 24 Hours</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Check this to set all days as 24 hours open</p>
                    </div>

                    <!-- Set Same Time for All Days -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <label class="flex items-center mb-2">
                            <input type="checkbox" id="set_same_time" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" onchange="toggleSameTime()">
                            <span class="ml-2 text-sm font-medium text-gray-700">Set Same Time for All Days</span>
                        </label>
                        <div id="same_time_controls" class="hidden space-y-2">
                            <div class="flex items-center space-x-2">
                                <input type="time" id="same_open_time" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                <span class="text-sm text-gray-500">to</span>
                                <input type="time" id="same_close_time" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                <button type="button" id="apply_same_time" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                    Apply to All Days
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="schedule_editor" class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Monday -->
                            <div class="border rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm font-medium text-gray-700">Monday</label>
                                    <input type="checkbox" id="monday_closed" class="text-blue-600 rounded" onchange="toggleDaySchedule('monday')">
                                    <label for="monday_closed" class="text-sm text-gray-600">Closed</label>
                                </div>
                                <div id="monday_schedule" class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <input type="time" id="monday_open" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                        <span class="text-sm text-gray-500">to</span>
                                        <input type="time" id="monday_close" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Tuesday -->
                            <div class="border rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm font-medium text-gray-700">Tuesday</label>
                                    <input type="checkbox" id="tuesday_closed" class="text-blue-600 rounded" onchange="toggleDaySchedule('tuesday')">
                                    <label for="tuesday_closed" class="text-sm text-gray-600">Closed</label>
                                </div>
                                <div id="tuesday_schedule" class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <input type="time" id="tuesday_open" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                        <span class="text-sm text-gray-500">to</span>
                                        <input type="time" id="tuesday_close" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Wednesday -->
                            <div class="border rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm font-medium text-gray-700">Wednesday</label>
                                    <input type="checkbox" id="wednesday_closed" class="text-blue-600 rounded" onchange="toggleDaySchedule('wednesday')">
                                    <label for="wednesday_closed" class="text-sm text-gray-600">Closed</label>
                                </div>
                                <div id="wednesday_schedule" class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <input type="time" id="wednesday_open" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                        <span class="text-sm text-gray-500">to</span>
                                        <input type="time" id="wednesday_close" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Thursday -->
                            <div class="border rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm font-medium text-gray-700">Thursday</label>
                                    <input type="checkbox" id="thursday_closed" class="text-blue-600 rounded" onchange="toggleDaySchedule('thursday')">
                                    <label for="thursday_closed" class="text-sm text-gray-600">Closed</label>
                                </div>
                                <div id="thursday_schedule" class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <input type="time" id="thursday_open" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                        <span class="text-sm text-gray-500">to</span>
                                        <input type="time" id="thursday_close" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Friday -->
                            <div class="border rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm font-medium text-gray-700">Friday</label>
                                    <input type="checkbox" id="friday_closed" class="text-blue-600 rounded" onchange="toggleDaySchedule('friday')">
                                    <label for="friday_closed" class="text-sm text-gray-600">Closed</label>
                                </div>
                                <div id="friday_schedule" class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <input type="time" id="friday_open" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                        <span class="text-sm text-gray-500">to</span>
                                        <input type="time" id="friday_close" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Saturday -->
                            <div class="border rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm font-medium text-gray-700">Saturday</label>
                                    <input type="checkbox" id="saturday_closed" class="text-blue-600 rounded" onchange="toggleDaySchedule('saturday')">
                                    <label for="saturday_closed" class="text-sm text-gray-600">Closed</label>
                                </div>
                                <div id="saturday_schedule" class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <input type="time" id="saturday_open" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                        <span class="text-sm text-gray-500">to</span>
                                        <input type="time" id="saturday_close" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Sunday -->
                            <div class="border rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-sm font-medium text-gray-700">Sunday</label>
                                    <input type="checkbox" id="sunday_closed" class="text-blue-600 rounded" onchange="toggleDaySchedule('sunday')">
                                    <label for="sunday_closed" class="text-sm text-gray-600">Closed</label>
                                </div>
                                <div id="sunday_schedule" class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <input type="time" id="sunday_open" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                        <span class="text-sm text-gray-500">to</span>
                                        <input type="time" id="sunday_close" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="schedule" name="schedule" required>
                </div>
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select id="category" name="category" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select a category</option>
                        <option value="Restaurant">Restaurant</option>
                        <option value="Hotel">Hotel</option>
                        <option value="Attraction">Attraction</option>
                        <option value="Park">Park</option>
                        <option value="Museum">Museum</option>
                        <option value="Resort">Resort</option>
                        <option value="Mall">Mall</option>
                        <option value="Church">Church</option>
                        <option value="Cafe">Cafe</option>
                        <option value="Bar">Bar</option>
                        <option value="Spa">Spa</option>
                        <option value="Gym">Gym</option>
                        <option value="Hospital">Hospital</option>
                        <option value="School">School</option>
                        <option value="Bank">Bank</option>
                        <option value="Gas Station">Gas Station</option>
                        <option value="Market">Market</option>
                        <option value="Beach">Beach</option>
                        <option value="Mountain">Mountain</option>
                        <option value="Historical Site">Historical Site</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Pictures Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Establishment Pictures</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <div class="flex flex-col items-center">
                            <i data-lucide="upload" class="w-8 h-8 text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-600 mb-2">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-500">PNG, JPG, JPEG, WebP up to 10MB each</p>
                            <input type="file" id="pictures" name="pictures[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden">
                            <button type="button" id="uploadBtn" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Choose Files
                            </button>
                        </div>
                    </div>
                    <div id="fileList" class="mt-2 space-y-2"></div>
                </div>

                                 <!-- Form Actions -->
                 <div class="flex justify-end space-x-3 pt-4 border-t">
                     <button type="button" id="cancelBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                         Cancel
                     </button>
                     <button type="button" id="nextBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                         Next
                     </button>
                 </div>
                         </form>
         </div>
     </div>
 </div>

 <!-- Add User Modal (Second Step) -->
 <div id="addUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
     <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
         <div class="mt-3">
             <!-- Header -->
             <div class="flex justify-between items-center mb-6">
                 <h3 class="text-lg font-semibold text-gray-900">Create Establishment Admin Account</h3>
                 <button id="closeUserModal" class="text-gray-400 hover:text-gray-600">
                     <i data-lucide="x" class="w-6 h-6"></i>
                 </button>
             </div>

             <!-- Progress Indicator -->
             <div class="mb-6">
                 <div class="flex items-center justify-center space-x-4">
                     <div class="flex items-center">
                         <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                             <i data-lucide="check" class="w-4 h-4 text-white"></i>
                         </div>
                         <span class="ml-2 text-sm font-medium text-gray-900">Establishment Details</span>
                     </div>
                     <div class="w-8 h-1 bg-gray-300"></div>
                     <div class="flex items-center">
                         <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                             <span class="text-white text-sm font-bold">2</span>
                         </div>
                         <span class="ml-2 text-sm font-medium text-blue-600">Admin Account</span>
                     </div>
                 </div>
             </div>

             <!-- User Form -->
             <form id="userForm" class="space-y-6">
                 @csrf
                 <!-- Establishment Name (Read-only) -->
                 <div>
                     <label class="block text-sm font-medium text-gray-700 mb-2">Establishment Name</label>
                     <input type="text" id="user_establishment_name" readonly
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700"
                            placeholder="Establishment name will appear here">
                 </div>

                 <!-- Email -->
                 <div>
                     <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                     <input type="email" id="email" name="email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter email address for admin account">
                 </div>

                 <!-- Password -->
                 <div>
                     <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                     <input type="password" id="password" name="password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter password for admin account">
                 </div>

                 <!-- Confirm Password -->
                 <div>
                     <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                     <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Confirm password">
                 </div>

                 <!-- Form Actions -->
                 <div class="flex justify-end space-x-3 pt-4 border-t">
                     <button type="button" id="backBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                         Back
                     </button>
                     <button type="submit" id="createAccountBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2">
                         <span class="btn-text">Create Account & Add Establishment</span>
                         <div class="loading-spinner"></div>
                     </button>
                 </div>
             </form>
         </div>
          </div>
 </div>

   <!-- Edit Establishment Modal -->
  <div id="editEstablishmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
      <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
          <div class="mt-3">
              <!-- Header -->
              <div class="flex justify-between items-center mb-6">
                  <h3 class="text-lg font-semibold text-gray-900">Edit Establishment</h3>
                  <button id="closeEditModal" class="text-gray-400 hover:text-gray-600">
                      <i data-lucide="x" class="w-6 h-6"></i>
                  </button>
              </div>

              <!-- Form -->
              <form id="editEstablishmentForm" class="space-y-6" enctype="multipart/form-data">
                  @csrf
                  <input type="hidden" name="_method" value="PUT">
                  <input type="hidden" id="edit_establishment_id" name="establishment_id">
                  
                  <!-- Establishment Name -->
                  <div>
                      <label for="edit_establishment_name" class="block text-sm font-medium text-gray-700 mb-2">Establishment Name *</label>
                      <input type="text" id="edit_establishment_name" name="establishment_name" required
                             class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                             placeholder="Enter establishment name">
                  </div>

                  <!-- Location -->
                  <div>
                      <label for="edit_location" class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                      <input type="text" id="edit_location" name="location" required
                             class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                             placeholder="Enter location">
                  </div>

                  <!-- Location Picker -->
                  <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Location on Map</label>
                      <div class="border border-gray-300 rounded-lg overflow-hidden">
                          <div id="edit_map" style="height: 300px; width: 100%;"></div>
                      </div>
                      <div class="mt-2 flex space-x-2">
                          <button type="button" id="editGetCurrentLocation" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                              Use Current Location
                          </button>
                          <button type="button" id="editSearchLocation" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                              Search Location
                          </button>
                      </div>
                      <input type="hidden" id="edit_maps_data" name="maps_data">
                      <input type="hidden" id="edit_latitude" name="latitude">
                      <input type="hidden" id="edit_longitude" name="longitude">
                  </div>

                  <!-- Short Description -->
                  <div>
                      <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-2">Short Description *</label>
                      <textarea id="edit_description" name="description" rows="4" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter a brief description of the establishment"></textarea>
                  </div>

                  <!-- Schedule -->
                  <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Schedule *</label>
                      
                      <!-- 24 Hours Option -->
                      <div class="mb-4">
                          <label class="flex items-center">
                              <input type="checkbox" id="edit_open_24_hours" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" onchange="toggleEdit24Hours()">
                              <span class="ml-2 text-sm font-medium text-gray-700">Open 24 Hours</span>
                          </label>
                          <p class="mt-1 text-xs text-gray-500">Check this to set all days as 24 hours open</p>
                      </div>

                      <!-- Set Same Time for All Days -->
                      <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                          <label class="flex items-center mb-2">
                              <input type="checkbox" id="edit_set_same_time" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" onchange="toggleEditSameTime()">
                              <span class="ml-2 text-sm font-medium text-gray-700">Set Same Time for All Days</span>
                          </label>
                          <div id="edit_same_time_controls" class="hidden space-y-2">
                              <div class="flex items-center space-x-2">
                                  <input type="time" id="edit_same_open_time" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                  <span class="text-sm text-gray-500">to</span>
                                  <input type="time" id="edit_same_close_time" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                  <button type="button" id="edit_apply_same_time" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                      Apply to All Days
                                  </button>
                              </div>
                          </div>
                      </div>
                      
                      <div id="edit_schedule_editor" class="space-y-3">
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                              <!-- Monday -->
                              <div class="border rounded-lg p-3">
                                  <div class="flex items-center justify-between mb-2">
                                      <label class="text-sm font-medium text-gray-700">Monday</label>
                                      <input type="checkbox" id="edit_monday_closed" class="text-blue-600 rounded" onchange="toggleEditDaySchedule('monday')">
                                      <label for="edit_monday_closed" class="text-sm text-gray-600">Closed</label>
                                  </div>
                                  <div id="edit_monday_schedule" class="space-y-2">
                                      <div class="flex items-center space-x-2">
                                          <input type="time" id="edit_monday_open" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                          <span class="text-sm text-gray-500">to</span>
                                          <input type="time" id="edit_monday_close" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                      </div>
                                  </div>
                              </div>

                              <!-- Tuesday -->
                              <div class="border rounded-lg p-3">
                                  <div class="flex items-center justify-between mb-2">
                                      <label class="text-sm font-medium text-gray-700">Tuesday</label>
                                      <input type="checkbox" id="edit_tuesday_closed" class="text-blue-600 rounded" onchange="toggleEditDaySchedule('tuesday')">
                                      <label for="edit_tuesday_closed" class="text-sm text-gray-600">Closed</label>
                                  </div>
                                  <div id="edit_tuesday_schedule" class="space-y-2">
                                      <div class="flex items-center space-x-2">
                                          <input type="time" id="edit_tuesday_open" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                          <span class="text-sm text-gray-500">to</span>
                                          <input type="time" id="edit_tuesday_close" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                      </div>
                                  </div>
                              </div>

                              <!-- Wednesday -->
                              <div class="border rounded-lg p-3">
                                  <div class="flex items-center justify-between mb-2">
                                      <label class="text-sm font-medium text-gray-700">Wednesday</label>
                                      <input type="checkbox" id="edit_wednesday_closed" class="text-blue-600 rounded" onchange="toggleEditDaySchedule('wednesday')">
                                      <label for="edit_wednesday_closed" class="text-sm text-gray-600">Closed</label>
                                  </div>
                                  <div id="edit_wednesday_schedule" class="space-y-2">
                                      <div class="flex items-center space-x-2">
                                          <input type="time" id="edit_wednesday_open" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                          <span class="text-sm text-gray-500">to</span>
                                          <input type="time" id="edit_wednesday_close" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                      </div>
                                  </div>
                              </div>

                              <!-- Thursday -->
                              <div class="border rounded-lg p-3">
                                  <div class="flex items-center justify-between mb-2">
                                      <label class="text-sm font-medium text-gray-700">Thursday</label>
                                      <input type="checkbox" id="edit_thursday_closed" class="text-blue-600 rounded" onchange="toggleEditDaySchedule('thursday')">
                                      <label for="edit_thursday_closed" class="text-sm text-gray-600">Closed</label>
                                  </div>
                                  <div id="edit_thursday_schedule" class="space-y-2">
                                      <div class="flex items-center space-x-2">
                                          <input type="time" id="edit_thursday_open" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                          <span class="text-sm text-gray-500">to</span>
                                          <input type="time" id="edit_thursday_close" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                      </div>
                                  </div>
                              </div>

                              <!-- Friday -->
                              <div class="border rounded-lg p-3">
                                  <div class="flex items-center justify-between mb-2">
                                      <label class="text-sm font-medium text-gray-700">Friday</label>
                                      <input type="checkbox" id="edit_friday_closed" class="text-blue-600 rounded" onchange="toggleEditDaySchedule('friday')">
                                      <label for="edit_friday_closed" class="text-sm text-gray-600">Closed</label>
                                  </div>
                                  <div id="edit_friday_schedule" class="space-y-2">
                                      <div class="flex items-center space-x-2">
                                          <input type="time" id="edit_friday_open" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                          <span class="text-sm text-gray-500">to</span>
                                          <input type="time" id="edit_friday_close" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                      </div>
                                  </div>
                              </div>

                              <!-- Saturday -->
                              <div class="border rounded-lg p-3">
                                  <div class="flex items-center justify-between mb-2">
                                      <label class="text-sm font-medium text-gray-700">Saturday</label>
                                      <input type="checkbox" id="edit_saturday_closed" class="text-blue-600 rounded" onchange="toggleEditDaySchedule('saturday')">
                                      <label for="edit_saturday_closed" class="text-sm text-gray-600">Closed</label>
                                  </div>
                                  <div id="edit_saturday_schedule" class="space-y-2">
                                      <div class="flex items-center space-x-2">
                                          <input type="time" id="edit_saturday_open" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                          <span class="text-sm text-gray-500">to</span>
                                          <input type="time" id="edit_saturday_close" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                      </div>
                                  </div>
                              </div>

                              <!-- Sunday -->
                              <div class="border rounded-lg p-3">
                                  <div class="flex items-center justify-between mb-2">
                                      <label class="text-sm font-medium text-gray-700">Sunday</label>
                                      <input type="checkbox" id="edit_sunday_closed" class="text-blue-600 rounded" onchange="toggleEditDaySchedule('sunday')">
                                      <label for="edit_sunday_closed" class="text-sm text-gray-600">Closed</label>
                                  </div>
                                  <div id="edit_sunday_schedule" class="space-y-2">
                                      <div class="flex items-center space-x-2">
                                          <input type="time" id="edit_sunday_open" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                          <span class="text-sm text-gray-500">to</span>
                                          <input type="time" id="edit_sunday_close" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <input type="hidden" id="edit_schedule" name="schedule" required>
                  </div>
                  </div>

                  <!-- Category -->
                  <div>
                      <label for="edit_category" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                      <select id="edit_category" name="category" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                          <option value="">Select a category</option>
                          <option value="Restaurant">Restaurant</option>
                          <option value="Hotel">Hotel</option>
                          <option value="Attraction">Attraction</option>
                          <option value="Park">Park</option>
                          <option value="Museum">Museum</option>
                          <option value="Resort">Resort</option>
                          <option value="Mall">Mall</option>
                          <option value="Church">Church</option>
                          <option value="Cafe">Cafe</option>
                          <option value="Bar">Bar</option>
                          <option value="Spa">Spa</option>
                          <option value="Gym">Gym</option>
                          <option value="Hospital">Hospital</option>
                          <option value="School">School</option>
                          <option value="Bank">Bank</option>
                          <option value="Gas Station">Gas Station</option>
                          <option value="Market">Market</option>
                          <option value="Beach">Beach</option>
                          <option value="Mountain">Mountain</option>
                          <option value="Historical Site">Historical Site</option>
                          <option value="Other">Other</option>
                      </select>
                  </div>

                  <!-- Current Pictures -->
                  <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Current Pictures</label>
                      <div id="currentPictures" class="grid grid-cols-3 gap-4 mb-4">
                          <!-- Current pictures will be loaded here -->
                      </div>
                  </div>

                  <!-- Add New Pictures Upload -->
                  <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Add New Pictures</label>
                      <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                          <div class="flex flex-col items-center">
                              <i data-lucide="upload" class="w-8 h-8 text-gray-400 mb-2"></i>
                              <p class="text-sm text-gray-600 mb-2">Click to upload or drag and drop</p>
                              <p class="text-xs text-gray-500">PNG, JPG, JPEG, WebP up to 10MB each</p>
                              <input type="file" id="edit_pictures" name="pictures[]" multiple accept="image/*" class="hidden">
                              <button type="button" id="editUploadBtn" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                  Choose Files
                              </button>
                          </div>
                      </div>
                      <div id="editFileList" class="mt-2 space-y-2"></div>
                  </div>

                  <!-- Form Actions -->
                  <div class="flex justify-end space-x-3 pt-4 border-t">
                      <button type="button" id="cancelEditBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                          Cancel
                      </button>
                      <button type="submit" id="updateEstablishmentBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2">
                          <span class="btn-text">Update Establishment</span>
                          <div class="loading-spinner"></div>
                      </button>
                  </div>
              </form>
          </div>
      </div>
  </div>

  <!-- Photo Viewer Modal -->
  <div id="photoViewerModal" class="fixed inset-0 bg-black bg-opacity-75 overflow-y-auto h-full w-full hidden z-50">
     <div class="relative top-10 mx-auto p-5 w-11/12 md:w-3/4 lg:w-2/3">
         <div class="bg-white rounded-lg shadow-xl">
             <!-- Header -->
             <div class="flex justify-between items-center p-6 border-b">
                 <h3 class="text-lg font-semibold text-gray-900" id="photoViewerTitle">Establishment Photos</h3>
                 <button id="closePhotoViewer" class="text-gray-400 hover:text-gray-600">
                     <i data-lucide="x" class="w-6 h-6"></i>
                 </button>
             </div>

             <!-- Photo Content -->
             <div class="p-6">
                 <div id="photoViewerContent" class="space-y-4">
                     <!-- Photos will be loaded here dynamically -->
                 </div>
             </div>
         </div>
     </div>
   </div>

  <!-- QR Code Viewer Modal -->
  <div id="qrCodeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
     <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
         <div class="mt-3">
             <!-- Header -->
             <div class="flex justify-between items-center mb-6">
                 <h3 class="text-lg font-semibold text-gray-900" id="qrCodeTitle">QR Code</h3>
                 <button id="closeQRModal" class="text-gray-400 hover:text-gray-600">
                     <i data-lucide="x" class="w-6 h-6"></i>
                 </button>
             </div>

             <!-- QR Code Content -->
             <div class="text-center">
                 <div id="qrCodeContent" class="mb-4">
                     <!-- QR code will be loaded here -->
                 </div>
                 <div class="mb-4">
                     <p class="text-sm text-gray-600 mb-2">Scan this QR code to collect a stamp!</p>
                     <div class="bg-gray-100 p-3 rounded-lg">
                         <p class="text-xs text-gray-500 mb-1">Direct Link:</p>
                         <a id="qrCodeLink" href="#" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm break-all">
                             <!-- Link will be loaded here -->
                         </a>
                     </div>
                 </div>
                 <div class="flex justify-center space-x-3">
                     <button onclick="testStampFromQR()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                         Test Stamp Collection (No Visitor Count)
                     </button>
                     <button onclick="downloadQRCode()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                         Download QR Code
                     </button>
                 </div>
             </div>
         </div>
     </div>
 </div>
 <script>
     window.STORAGE_BASE_URL = "{{ rtrim(Storage::url(''), '/') }}";
 </script>
   <script src="/js/manage-establishments.js"></script>
@endsection