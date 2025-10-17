@extends('layouts.admin')

@section('title', 'Manage Establishment')

@section('content')
<!-- Google Maps API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAqTc2s31QM_gQanGqCxE1hw5QI0OxoUbg&libraries=places&callback=initMaps"></script>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manage Establishment</h2>
    </div>

    @if(isset($establishment))
    <!-- Establishment Details Form -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <form id="establishmentForm" class="space-y-6" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            
            <!-- Establishment Name -->
            <div>
                <label for="establishment_name" class="block text-sm font-medium text-gray-700 mb-2">Establishment Name *</label>
                <input type="text" id="establishment_name" name="establishment_name" required
                       value="{{ $establishment->establishment_name }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Enter establishment name">
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                <input type="text" id="location" name="location" required
                       value="{{ $establishment->location }}"
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
                <input type="hidden" id="maps_data" name="maps_data" value="{{ $establishment->maps_data }}">
                <input type="hidden" id="latitude" name="latitude" value="{{ $establishment->latitude }}">
                <input type="hidden" id="longitude" name="longitude" value="{{ $establishment->longitude }}">
            </div>

            <!-- Short Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Short Description *</label>
                <textarea id="description" name="description" rows="4" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Enter a brief description of the establishment">{{ $establishment->description }}</textarea>
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
                
                <!-- Hidden schedule input for form submission -->
                <input type="hidden" id="schedule" name="schedule" value="{{ 
                    $establishment->schedule 
                        ? (is_string($establishment->schedule) 
                            ? $establishment->schedule 
                            : json_encode($establishment->schedule))
                        : '{}'
                }}">
            </div>

            <!-- Category -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                <select id="category" name="category" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select a category</option>
                    <option value="Restaurant" {{ $establishment->category == 'Restaurant' ? 'selected' : '' }}>Restaurant</option>
                    <option value="Hotel" {{ $establishment->category == 'Hotel' ? 'selected' : '' }}>Hotel</option>
                    <option value="Attraction" {{ $establishment->category == 'Attraction' ? 'selected' : '' }}>Attraction</option>
                    <option value="Park" {{ $establishment->category == 'Park' ? 'selected' : '' }}>Park</option>
                    <option value="Museum" {{ $establishment->category == 'Museum' ? 'selected' : '' }}>Museum</option>
                    <option value="Resort" {{ $establishment->category == 'Resort' ? 'selected' : '' }}>Resort</option>
                    <option value="Mall" {{ $establishment->category == 'Mall' ? 'selected' : '' }}>Mall</option>
                    <option value="Church" {{ $establishment->category == 'Church' ? 'selected' : '' }}>Church</option>
                    <option value="Cafe" {{ $establishment->category == 'Cafe' ? 'selected' : '' }}>Cafe</option>
                    <option value="Bar" {{ $establishment->category == 'Bar' ? 'selected' : '' }}>Bar</option>
                    <option value="Spa" {{ $establishment->category == 'Spa' ? 'selected' : '' }}>Spa</option>
                    <option value="Gym" {{ $establishment->category == 'Gym' ? 'selected' : '' }}>Gym</option>
                    <option value="Hospital" {{ $establishment->category == 'Hospital' ? 'selected' : '' }}>Hospital</option>
                    <option value="School" {{ $establishment->category == 'School' ? 'selected' : '' }}>School</option>
                    <option value="Bank" {{ $establishment->category == 'Bank' ? 'selected' : '' }}>Bank</option>
                    <option value="Gas Station" {{ $establishment->category == 'Gas Station' ? 'selected' : '' }}>Gas Station</option>
                    <option value="Market" {{ $establishment->category == 'Market' ? 'selected' : '' }}>Market</option>
                    <option value="Beach" {{ $establishment->category == 'Beach' ? 'selected' : '' }}>Beach</option>
                    <option value="Mountain" {{ $establishment->category == 'Mountain' ? 'selected' : '' }}>Mountain</option>
                    <option value="Historical Site" {{ $establishment->category == 'Historical Site' ? 'selected' : '' }}>Historical Site</option>
                    <option value="Other" {{ $establishment->category == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <!-- Current Pictures -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Pictures</label>
                <div id="currentPictures" class="grid grid-cols-3 gap-4 mb-4">
                    @forelse($establishment->pictures as $picture)
                    <div class="relative">
                        <img src="{{ Storage::url($picture->image_path) }}" 
                             alt="Establishment photo" 
                             class="w-full h-24 object-cover rounded-lg">
                        <button type="button" 
                                onclick="deletePicture({{ $picture->id }})" 
                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                            <i data-lucide="x" class="w-3 h-3"></i>
                        </button>
                    </div>
                    @empty
                    <p class="text-gray-500 text-sm">No current pictures</p>
                    @endforelse
                </div>
            </div>

            <!-- Add New Pictures Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Add New Pictures</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <div class="flex flex-col items-center">
                        <i data-lucide="upload" class="w-8 h-8 text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600 mb-2">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 10MB each</p>
                        <input type="file" id="pictures" name="pictures[]" multiple accept="image/*" class="hidden">
                        <button type="button" id="uploadBtn" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Choose Files
                        </button>
                    </div>
                </div>
                <div id="fileList" class="mt-2 space-y-2"></div>
            </div>

            <!-- QR Code Section -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">QR Code</label>
                <div class="border border-gray-300 rounded-lg p-4">
                    @if($establishment->qr_code)
                        <div class="flex flex-col items-center space-y-4">
                            <div class="w-32 h-32 bg-white border border-gray-200 rounded-lg flex items-center justify-center">
                                {!! $establishment->qr_code !!}
                            </div>
                            <div class="text-center space-y-2">
                                <p class="text-sm text-gray-600">Scan this QR code to collect a stamp!</p>
                                <div class="flex space-x-2 justify-center">
                                    <button type="button" onclick="viewQRCode({{ $establishment->id }})" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                        View & Test
                                    </button>
                                    <button type="button" onclick="regenerateQRCode({{ $establishment->id }})" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                        Regenerate
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i data-lucide="qr-code" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                            <p class="text-gray-500 mb-4">No QR code generated yet</p>
                            <button type="button" onclick="generateQRCode({{ $establishment->id }})" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Generate QR Code
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Update Establishment
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="text-center">
            <i data-lucide="alert-circle" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Establishment Found</h3>
            <p class="text-gray-500">No establishment is associated with your admin account. Please contact the superadmin.</p>
        </div>
    </div>
    @endif
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
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('establishmentForm');
    const uploadBtn = document.getElementById('uploadBtn');
    const fileInput = document.getElementById('pictures');
    const fileList = document.getElementById('fileList');

    // File upload handling
    if (uploadBtn && fileInput) {
        uploadBtn.addEventListener('click', function() {
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            fileList.innerHTML = '';
            Array.from(this.files).forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center justify-between p-2 bg-gray-50 rounded';
                fileItem.innerHTML = `
                    <span class="text-sm text-gray-700">${file.name}</span>
                    <button type="button" class="text-red-500 hover:text-red-700" onclick="this.parentElement.remove()">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                `;
                fileList.appendChild(fileItem);
            });
        });
    }

    // Form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            fetch('{{ route("admin.establishment.update") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-HTTP-Method-Override': 'PUT'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Establishment updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the establishment.');
            });
        });
    }

    // Delete picture function
    window.deletePicture = function(pictureId) {
        if (confirm('Are you sure you want to delete this picture?')) {
            fetch(`/admin/establishment-pictures/${pictureId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the picture from the UI
                    const pictureElement = document.querySelector(`[onclick="deletePicture(${pictureId})"]`).parentElement;
                    pictureElement.remove();
                    
                    // If no pictures left, show "No current pictures" message
                    const currentPictures = document.getElementById('currentPictures');
                    if (currentPictures.children.length === 0) {
                        currentPictures.innerHTML = '<p class="text-gray-500 text-sm">No current pictures</p>';
                    }
                    
                    alert('Picture deleted successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the picture.');
            });
        }
    };

    // Load existing schedule data
    loadScheduleToForm();
    
    // Initialize maps if coordinates exist
    if (document.getElementById('latitude').value && document.getElementById('longitude').value) {
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(document.getElementById('longitude').value);
        if (lat && lng) {
            initMapWithLocation(lat, lng);
        }
    }
});

// Schedule editor functions
function toggle24Hours() {
    const is24Hours = document.getElementById('open_24_hours').checked;
    const scheduleEditor = document.getElementById('schedule_editor');
    
    if (is24Hours) {
        scheduleEditor.style.display = 'none';
        // Set all days to 24 hours
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        days.forEach(day => {
            document.getElementById(`${day}_closed`).checked = false;
            document.getElementById(`${day}_open`).value = '00:00';
            document.getElementById(`${day}_close`).value = '23:59';
        });
    } else {
        scheduleEditor.style.display = 'block';
    }
    
    updateScheduleJSON();
}

function toggleDaySchedule(day) {
    const isClosed = document.getElementById(`${day}_closed`).checked;
    const scheduleDiv = document.getElementById(`${day}_schedule`);
    
    if (isClosed) {
        scheduleDiv.style.display = 'none';
        document.getElementById(`${day}_open`).value = '';
        document.getElementById(`${day}_close`).value = '';
    } else {
        scheduleDiv.style.display = 'block';
        // Set default times if empty
        if (!document.getElementById(`${day}_open`).value) {
            document.getElementById(`${day}_open`).value = '09:00';
        }
        if (!document.getElementById(`${day}_close`).value) {
            document.getElementById(`${day}_close`).value = '18:00';
        }
    }
    
    updateScheduleJSON();
}

function updateScheduleJSON() {
    const is24Hours = document.getElementById('open_24_hours').checked;
    const schedule = {};
    
    if (is24Hours) {
        const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        days.forEach(day => {
            schedule[day] = '12:00 AM - 11:59 PM';
        });
    } else {
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        const dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        days.forEach((day, index) => {
            const isClosed = document.getElementById(`${day}_closed`).checked;
            if (isClosed) {
                schedule[dayNames[index]] = 'Closed';
            } else {
                const openTime = document.getElementById(`${day}_open`).value;
                const closeTime = document.getElementById(`${day}_close`).value;
                
                if (openTime && closeTime) {
                    // Convert 24-hour format to 12-hour format
                    const open12Hour = convertTo12Hour(openTime);
                    const close12Hour = convertTo12Hour(closeTime);
                    schedule[dayNames[index]] = `${open12Hour} - ${close12Hour}`;
                } else {
                    schedule[dayNames[index]] = '9:00 AM - 6:00 PM';
                }
            }
        });
    }
    
    document.getElementById('schedule').value = JSON.stringify(schedule);
}

function convertTo12Hour(time24) {
    const [hours, minutes] = time24.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const hour12 = hour % 12 || 12;
    return `${hour12}:${minutes} ${ampm}`;
}

function loadScheduleToForm() {
    const scheduleInput = document.getElementById('schedule');
    if (!scheduleInput.value) return;
    
    try {
        let schedule;
        // Handle both string and array formats
        if (typeof scheduleInput.value === 'string') {
            schedule = JSON.parse(scheduleInput.value);
        } else {
            schedule = scheduleInput.value;
        }
        
        // Check if it's 24 hours
        const is24Hours = Object.values(schedule).every(time => time === '12:00 AM - 11:59 PM');
        if (is24Hours) {
            document.getElementById('open_24_hours').checked = true;
            toggle24Hours();
            return;
        }
        
        // Load individual day schedules
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        const dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        days.forEach((day, index) => {
            const daySchedule = schedule[dayNames[index]];
            if (daySchedule === 'Closed') {
                document.getElementById(`${day}_closed`).checked = true;
                toggleDaySchedule(day);
            } else if (daySchedule) {
                // Parse time format like "9:00 AM - 6:00 PM"
                const timeMatch = daySchedule.match(/(\d{1,2}):(\d{2})\s*(AM|PM)\s*-\s*(\d{1,2}):(\d{2})\s*(AM|PM)/);
                if (timeMatch) {
                    const openTime = convertTo24Hour(timeMatch[1], timeMatch[2], timeMatch[3]);
                    const closeTime = convertTo24Hour(timeMatch[4], timeMatch[5], timeMatch[6]);
                    
                    document.getElementById(`${day}_open`).value = openTime;
                    document.getElementById(`${day}_close`).value = closeTime;
                }
            }
        });
        
        updateScheduleJSON();
    } catch (error) {
        console.error('Error loading schedule:', error);
        // Set default schedule if there's an error
        const defaultSchedule = {
            'Monday': '9:00 AM - 6:00 PM',
            'Tuesday': '9:00 AM - 6:00 PM',
            'Wednesday': '9:00 AM - 6:00 PM',
            'Thursday': '9:00 AM - 6:00 PM',
            'Friday': '9:00 AM - 6:00 PM',
            'Saturday': '9:00 AM - 6:00 PM',
            'Sunday': '9:00 AM - 6:00 PM'
        };
        scheduleInput.value = JSON.stringify(defaultSchedule);
    }
}

function convertTo24Hour(hour, minute, period) {
    let hour24 = parseInt(hour);
    if (period === 'PM' && hour24 !== 12) {
        hour24 += 12;
    } else if (period === 'AM' && hour24 === 12) {
        hour24 = 0;
    }
    return `${hour24.toString().padStart(2, '0')}:${minute}`;
}

// Add event listeners for time inputs
document.addEventListener('DOMContentLoaded', function() {
    const timeInputs = document.querySelectorAll('input[type="time"]');
    timeInputs.forEach(input => {
        input.addEventListener('change', updateScheduleJSON);
    });
});

// Same time functionality
function toggleSameTime() {
    const isSameTime = document.getElementById('set_same_time').checked;
    const sameTimeControls = document.getElementById('same_time_controls');
    
    if (isSameTime) {
        sameTimeControls.classList.remove('hidden');
    } else {
        sameTimeControls.classList.add('hidden');
    }
}

// Apply same time to all days
document.addEventListener('DOMContentLoaded', function() {
    const applySameTimeBtn = document.getElementById('apply_same_time');
    if (applySameTimeBtn) {
        applySameTimeBtn.addEventListener('click', function() {
            const openTime = document.getElementById('same_open_time').value;
            const closeTime = document.getElementById('same_close_time').value;
            
            if (openTime && closeTime) {
                const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                days.forEach(day => {
                    document.getElementById(`${day}_open`).value = openTime;
                    document.getElementById(`${day}_close`).value = closeTime;
                    document.getElementById(`${day}_closed`).checked = false;
                    document.getElementById(`${day}_schedule`).style.display = 'block';
                });
                updateScheduleJSON();
            } else {
                alert('Please select both open and close times.');
            }
        });
    }
});

// Maps functionality
let map;
let marker;

function initMaps() {
    // Default location (Calamba, Laguna)
    const defaultLocation = { lat: 14.2117, lng: 121.1653 };
    
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: defaultLocation
    });

    // Add click listener to map
    map.addListener('click', function(event) {
        placeMarker(event.latLng, map, 'main');
    });

    // Get current location button
    const getCurrentLocationBtn = document.getElementById('getCurrentLocation');
    if (getCurrentLocationBtn) {
        getCurrentLocationBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const location = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    map.setCenter(location);
                    placeMarker(location, map, 'main');
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        });
    }

    // Search location button
    const searchLocationBtn = document.getElementById('searchLocation');
    if (searchLocationBtn) {
        searchLocationBtn.addEventListener('click', function() {
            const locationInput = prompt('Enter location to search:');
            if (locationInput) {
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ address: locationInput }, function(results, status) {
                    if (status === 'OK') {
                        const location = results[0].geometry.location;
                        map.setCenter(location);
                        placeMarker(location, map, 'main');
                    } else {
                        alert('Location not found: ' + status);
                    }
                });
            }
        });
    }
}

function initMapWithLocation(lat, lng) {
    const location = { lat: lat, lng: lng };
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: location
    });
    
    placeMarker(location, map, 'main');
    
    // Add click listener to map
    map.addListener('click', function(event) {
        placeMarker(event.latLng, map, 'main');
    });

    // Get current location button
    const getCurrentLocationBtn = document.getElementById('getCurrentLocation');
    if (getCurrentLocationBtn) {
        getCurrentLocationBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const location = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    map.setCenter(location);
                    placeMarker(location, map, 'main');
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        });
    }

    // Search location button
    const searchLocationBtn = document.getElementById('searchLocation');
    if (searchLocationBtn) {
        searchLocationBtn.addEventListener('click', function() {
            const locationInput = prompt('Enter location to search:');
            if (locationInput) {
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ address: locationInput }, function(results, status) {
                    if (status === 'OK') {
                        const location = results[0].geometry.location;
                        map.setCenter(location);
                        placeMarker(location, map, 'main');
                    } else {
                        alert('Location not found: ' + status);
                    }
                });
            }
        });
    }
}

function placeMarker(location, mapInstance, type) {
    // Remove existing marker
    if (marker) {
        marker.setMap(null);
    }

    // Create new marker
    marker = new google.maps.Marker({
        position: location,
        map: mapInstance,
        draggable: true
    });

    // Update form fields
    document.getElementById('latitude').value = location.lat();
    document.getElementById('longitude').value = location.lng();
    
    // Generate maps embed
    const mapsEmbed = generateMapsEmbed(location.lat(), location.lng(), type);
    document.getElementById('maps_data').value = mapsEmbed;

    // Add drag listener
    marker.addListener('dragend', function() {
        const newLocation = marker.getPosition();
        document.getElementById('latitude').value = newLocation.lat();
        document.getElementById('longitude').value = newLocation.lng();
        
        const mapsEmbed = generateMapsEmbed(newLocation.lat(), newLocation.lng(), type);
        document.getElementById('maps_data').value = mapsEmbed;
    });
}

function generateMapsEmbed(lat, lng, type) {
    return `<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12345!2d${lng}!3d${lat}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTTCsDEyJzQyLjEiTiAxMjHCsDA5JzU1LjEiRQ!5e0!3m2!1sen!2sph!4v1234567890123!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>`;
}

// QR Code Functions
let currentEstablishmentId = null;

function viewQRCode(establishmentId) {
    currentEstablishmentId = establishmentId;
    
    // Get establishment name from the page
    const establishmentName = document.querySelector('h2').textContent.replace('Manage Establishment', '').trim() || 'Establishment';
    
    // Fetch QR code data
    fetch(`/qr-code/${establishmentId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('qrCodeTitle').textContent = `${establishmentName} - QR Code`;
            document.getElementById('qrCodeContent').innerHTML = data.qr_code;
            document.getElementById('qrCodeLink').href = `/stamp/process/${establishmentId}`;
            document.getElementById('qrCodeLink').textContent = `${window.location.origin}/stamp/process/${establishmentId}`;
            document.getElementById('qrCodeModal').classList.remove('hidden');
            lucide.createIcons(); // Reinitialize icons
        } else {
            alert('Error loading QR code: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while loading the QR code.');
    });
}

function generateQRCode(establishmentId) {
    if (confirm('Generate QR code for this establishment?')) {
        fetch(`/stamp/generate-qr/${establishmentId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload(); // Reload to show the new QR code
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while generating the QR code.');
        });
    }
}

function regenerateQRCode(establishmentId) {
    if (confirm('Regenerate QR code for this establishment?')) {
        fetch(`/stamp/regenerate-qr/${establishmentId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload(); // Reload to show the new QR code
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while regenerating the QR code.');
        });
    }
}

function testStampFromQR() {
    if (!currentEstablishmentId) {
        alert('No establishment selected');
        return;
    }
    
    fetch(`/stamp/test/${currentEstablishmentId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while testing the stamp collection.');
    });
}

function downloadQRCode() {
    if (!currentEstablishmentId) {
        alert('No establishment selected');
        return;
    }
    
    // Create a temporary link to download the QR code
    const qrCodeElement = document.getElementById('qrCodeContent');
    const svgElement = qrCodeElement.querySelector('svg');
    
    if (svgElement) {
        const svgData = new XMLSerializer().serializeToString(svgElement);
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();
        
        img.onload = function() {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
            
            const pngFile = canvas.toDataURL('image/png');
            const downloadLink = document.createElement('a');
            downloadLink.download = `qr-code-${currentEstablishmentId}.png`;
            downloadLink.href = pngFile;
            downloadLink.click();
        };
        
        img.src = 'data:image/svg+xml;base64,' + btoa(svgData);
    } else {
        alert('QR code not found');
    }
}

// QR Code Modal event listeners
document.addEventListener('DOMContentLoaded', function() {
    const qrCodeModal = document.getElementById('qrCodeModal');
    const closeQRModal = document.getElementById('closeQRModal');
    
    if (closeQRModal) {
        closeQRModal.addEventListener('click', function() {
            qrCodeModal.classList.add('hidden');
        });
    }
    
    // Close modal when clicking outside
    if (qrCodeModal) {
        qrCodeModal.addEventListener('click', function(e) {
            if (e.target === qrCodeModal) {
                qrCodeModal.classList.add('hidden');
            }
        });
    }
});
</script>

<script>
    window.STORAGE_BASE_URL = "{{ rtrim(Storage::url(''), '/') }}";
</script>
@endsection
