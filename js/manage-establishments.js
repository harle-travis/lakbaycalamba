// Global variables for Google Maps
let map, editMap;
let marker, editMarker;
let geocoder, editGeocoder;
let searchBox, editSearchBox;

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

// Initialize Google Maps
function initMaps() {
    // Initialize main map
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 14.2118, lng: 121.1653 }, // Calamba, Laguna coordinates
        zoom: 15
    });
    
    // Initialize edit map
    editMap = new google.maps.Map(document.getElementById('edit_map'), {
        center: { lat: 14.2118, lng: 121.1653 }, // Calamba, Laguna coordinates
        zoom: 15
    });
    
    // Initialize geocoders
    geocoder = new google.maps.Geocoder();
    editGeocoder = new google.maps.Geocoder();
    
    // Initialize search boxes
    searchBox = new google.maps.places.SearchBox(document.getElementById('location'));
    editSearchBox = new google.maps.places.SearchBox(document.getElementById('edit_location'));
    
    // Add click listener to main map
    map.addListener('click', function(event) {
        placeMarker(event.latLng, map, 'main');
    });
    
    // Add click listener to edit map
    editMap.addListener('click', function(event) {
        placeMarker(event.latLng, editMap, 'edit');
    });
    
    // Add search box listeners
    searchBox.addListener('places_changed', function() {
        const places = searchBox.getPlaces();
        if (places.length > 0) {
            const place = places[0];
            if (place.geometry && place.geometry.location) {
                map.setCenter(place.geometry.location);
                placeMarker(place.geometry.location, map, 'main');
            }
        }
    });
    
    editSearchBox.addListener('places_changed', function() {
        const places = editSearchBox.getPlaces();
        if (places.length > 0) {
            const place = places[0];
            if (place.geometry && place.geometry.location) {
                editMap.setCenter(place.geometry.location);
                placeMarker(place.geometry.location, editMap, 'edit');
            }
        }
    });
}

// Place marker on map
function placeMarker(location, mapInstance, type) {
    // Remove existing marker
    if (type === 'main' && marker) {
        marker.setMap(null);
    } else if (type === 'edit' && editMarker) {
        editMarker.setMap(null);
    }
    
    // Create new marker
    const newMarker = new google.maps.Marker({
        position: location,
        map: mapInstance,
        draggable: true
    });
    
    // Update marker reference
    if (type === 'main') {
        marker = newMarker;
        document.getElementById('latitude').value = location.lat();
        document.getElementById('longitude').value = location.lng();
        generateMapsEmbed(location.lat(), location.lng(), 'main');
    } else if (type === 'edit') {
        editMarker = newMarker;
        document.getElementById('edit_latitude').value = location.lat();
        document.getElementById('edit_longitude').value = location.lng();
        generateMapsEmbed(location.lat(), location.lng(), 'edit');
    }
    
    // Add drag listener
    newMarker.addListener('dragend', function() {
        const newLocation = newMarker.getPosition();
        if (type === 'main') {
            document.getElementById('latitude').value = newLocation.lat();
            document.getElementById('longitude').value = newLocation.lng();
            generateMapsEmbed(newLocation.lat(), newLocation.lng(), 'main');
        } else if (type === 'edit') {
            document.getElementById('edit_latitude').value = newLocation.lat();
            document.getElementById('edit_longitude').value = newLocation.lng();
            generateMapsEmbed(newLocation.lat(), newLocation.lng(), 'edit');
        }
    });
}

// Generate maps embed code
function generateMapsEmbed(lat, lng, type) {
    // Generate a proper Google Maps embed URL
    const embedCode = `<iframe src="https://www.google.com/maps/embed/v1/place?key=AIzaSyAqTc2s31QM_gQanGqCxE1hw5QI0OxoUbg&q=${lat},${lng}&zoom=15" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>`;
    
    if (type === 'main') {
        document.getElementById('maps_data').value = embedCode;
    } else if (type === 'edit') {
        document.getElementById('edit_maps_data').value = embedCode;
    }
}

// Get current location
function getCurrentLocation(type) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const location = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            
            if (type === 'main') {
                map.setCenter(location);
                placeMarker(location, map, 'main');
            } else if (type === 'edit') {
                editMap.setCenter(location);
                placeMarker(location, editMap, 'edit');
            }
        });
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

// Toggle same time for all days
function toggleSameTime() {
    const checkbox = document.getElementById('set_same_time');
    const controls = document.getElementById('same_time_controls');
    
    if (checkbox.checked) {
        controls.classList.remove('hidden');
    } else {
        controls.classList.add('hidden');
    }
}

// Toggle edit same time for all days
function toggleEditSameTime() {
    const checkbox = document.getElementById('edit_set_same_time');
    const controls = document.getElementById('edit_same_time_controls');
    
    if (checkbox.checked) {
        controls.classList.remove('hidden');
    } else {
        controls.classList.add('hidden');
    }
}

// Apply same time to all days
function applySameTime() {
    const openTime = document.getElementById('same_open_time').value;
    const closeTime = document.getElementById('same_close_time').value;
    
    if (!openTime || !closeTime) {
        alert('Please select both open and close times.');
        return;
    }
    
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    
    days.forEach(day => {
        const openInput = document.getElementById(`${day}_open`);
        const closeInput = document.getElementById(`${day}_close`);
        const closedCheckbox = document.getElementById(`${day}_closed`);
        
        if (openInput && closeInput && closedCheckbox) {
            openInput.value = openTime;
            closeInput.value = closeTime;
            closedCheckbox.checked = false;
        }
    });
}

// Apply same time to all days (edit form)
function applyEditSameTime() {
    const openTime = document.getElementById('edit_same_open_time').value;
    const closeTime = document.getElementById('edit_same_close_time').value;
    
    if (!openTime || !closeTime) {
        alert('Please select both open and close times.');
        return;
    }
    
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    
    days.forEach(day => {
        const openInput = document.getElementById(`edit_${day}_open`);
        const closeInput = document.getElementById(`edit_${day}_close`);
        const closedCheckbox = document.getElementById(`edit_${day}_closed`);
        
        if (openInput && closeInput && closedCheckbox) {
            openInput.value = openTime;
            closeInput.value = closeTime;
            closedCheckbox.checked = false;
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('addEstablishmentModal');
    const userModal = document.getElementById('addUserModal');
    const addBtn = document.getElementById('addEstablishmentBtn');
    const closeBtn = document.getElementById('closeModal');
    const closeUserBtn = document.getElementById('closeUserModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const nextBtn = document.getElementById('nextBtn');
    const backBtn = document.getElementById('backBtn');
    const uploadBtn = document.getElementById('uploadBtn');
    const fileInput = document.getElementById('pictures');
    const fileList = document.getElementById('fileList');
    const form = document.getElementById('establishmentForm');
    const userForm = document.getElementById('userForm');
    const userEstablishmentName = document.getElementById('user_establishment_name');
    const photoViewerModal = document.getElementById('photoViewerModal');
    const closePhotoViewer = document.getElementById('closePhotoViewer');
    const photoViewerTitle = document.getElementById('photoViewerTitle');
    const photoViewerContent = document.getElementById('photoViewerContent');
    
    // QR Code modal elements
    const qrCodeModal = document.getElementById('qrCodeModal');
    const closeQRModal = document.getElementById('closeQRModal');
    const qrCodeTitle = document.getElementById('qrCodeTitle');
    const qrCodeContent = document.getElementById('qrCodeContent');
    const qrCodeLink = document.getElementById('qrCodeLink');
    
    // Edit modal elements
    const editModal = document.getElementById('editEstablishmentModal');
    const closeEditBtn = document.getElementById('closeEditModal');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const editForm = document.getElementById('editEstablishmentForm');
    const editUploadBtn = document.getElementById('editUploadBtn');
    const editFileInput = document.getElementById('edit_pictures');
    const editFileList = document.getElementById('editFileList');
    const currentPictures = document.getElementById('currentPictures');

    // Open modal
    addBtn.addEventListener('click', function() {
        modal.classList.remove('hidden');
    });

    // Close modal
    function closeModal() {
        modal.classList.add('hidden');
        form.reset();
        fileList.innerHTML = '';
    }

    function closeUserModal() {
        userModal.classList.add('hidden');
        userForm.reset();
    }

    function closePhotoViewerModal() {
        photoViewerModal.classList.add('hidden');
        photoViewerContent.innerHTML = '';
    }

    function closeEditModal() {
        editModal.classList.add('hidden');
        editForm.reset();
        editFileList.innerHTML = '';
        currentPictures.innerHTML = '';
    }

    function closeQRCodeModal() {
        qrCodeModal.classList.add('hidden');
        qrCodeContent.innerHTML = '';
        qrCodeLink.href = '#';
        qrCodeLink.textContent = '';
    }

    closeBtn.addEventListener('click', closeModal);
    closeUserBtn.addEventListener('click', closeUserModal);
    closePhotoViewer.addEventListener('click', closePhotoViewerModal);
    closeQRModal.addEventListener('click', closeQRCodeModal);
    cancelBtn.addEventListener('click', closeModal);
    
    // Edit modal close handlers
    closeEditBtn.addEventListener('click', closeEditModal);
    cancelEditBtn.addEventListener('click', closeEditModal);

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    userModal.addEventListener('click', function(e) {
        if (e.target === userModal) {
            closeUserModal();
        }
    });

    photoViewerModal.addEventListener('click', function(e) {
        if (e.target === photoViewerModal) {
            closePhotoViewerModal();
        }
    });

    editModal.addEventListener('click', function(e) {
        if (e.target === editModal) {
            closeEditModal();
        }
    });

    qrCodeModal.addEventListener('click', function(e) {
        if (e.target === qrCodeModal) {
            closeQRCodeModal();
        }
    });

    // Google Maps event listeners
    document.getElementById('getCurrentLocation').addEventListener('click', function() {
        getCurrentLocation('main');
    });
    
    document.getElementById('searchLocation').addEventListener('click', function() {
        const locationInput = document.getElementById('location');
        if (locationInput.value.trim()) {
            geocoder.geocode({ address: locationInput.value }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    const location = results[0].geometry.location;
                    map.setCenter(location);
                    placeMarker(location, map, 'main');
                } else {
                    alert('Location not found. Please try a different search term.');
                }
            });
        } else {
            alert('Please enter a location to search.');
        }
    });
    
    document.getElementById('editGetCurrentLocation').addEventListener('click', function() {
        getCurrentLocation('edit');
    });
    
    document.getElementById('editSearchLocation').addEventListener('click', function() {
        const locationInput = document.getElementById('edit_location');
        if (locationInput.value.trim()) {
            editGeocoder.geocode({ address: locationInput.value }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    const location = results[0].geometry.location;
                    editMap.setCenter(location);
                    placeMarker(location, editMap, 'edit');
                } else {
                    alert('Location not found. Please try a different search term.');
                }
            });
        } else {
            alert('Please enter a location to search.');
        }
    });
    
    // Same time feature event listeners
    document.getElementById('apply_same_time').addEventListener('click', applySameTime);
    document.getElementById('edit_apply_same_time').addEventListener('click', applyEditSameTime);

    // Next button - validate establishment form and show user form
    nextBtn.addEventListener('click', function() {
        // Ensure schedule JSON is generated before validation
        updateScheduleJSON();
        
        // Validate establishment form
        const establishmentName = document.getElementById('establishment_name').value;
        const location = document.getElementById('location').value;
        const description = document.getElementById('description').value;
        const schedule = document.getElementById('schedule').value;
        const category = document.getElementById('category').value;

        // Debug: Log the values being checked
        console.log('Validation check:', {
            establishmentName: establishmentName,
            location: location,
            description: description,
            schedule: schedule,
            category: category
        });

        if (!establishmentName || !location || !description || !schedule || !category) {
            alert('Please fill in all required fields before proceeding.');
            return;
        }

        // Set establishment name in user form
        userEstablishmentName.value = establishmentName;

        // Hide establishment modal and show user modal
        modal.classList.add('hidden');
        userModal.classList.remove('hidden');
    });

    // Back button - return to establishment form
    backBtn.addEventListener('click', function() {
        userModal.classList.add('hidden');
        modal.classList.remove('hidden');
    });

    // File upload handling
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

    // Edit file upload handling
    editUploadBtn.addEventListener('click', function() {
        editFileInput.click();
    });

    editFileInput.addEventListener('change', function() {
        editFileList.innerHTML = '';
        Array.from(this.files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-2 bg-gray-50 rounded';
            fileItem.innerHTML = `
                <span class="text-sm text-gray-700">${file.name}</span>
                <button type="button" class="text-red-500 hover:text-red-700" onclick="this.parentElement.remove()">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            `;
            editFileList.appendChild(fileItem);
        });
    });

    // User form submission - create both establishment and user
    userForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        
        if (password !== passwordConfirmation) {
            alert('Passwords do not match!');
            return;
        }

        // Show loading state
        showButtonLoading('createAccountBtn');
        showFullPageLoader('Creating establishment and admin account...');

        // Ensure schedule JSON is generated before submission
        updateScheduleJSON();
        
        // Ensure maps data is generated if coordinates are available
        const latitude = document.getElementById('latitude').value;
        const longitude = document.getElementById('longitude').value;
        if (latitude && longitude && !document.getElementById('maps_data').value) {
            generateMapsEmbed(parseFloat(latitude), parseFloat(longitude), 'main');
        }
        
        // Create FormData for establishment
        const establishmentData = new FormData(form);
        
        // Debug: Log the data being sent
        console.log('Establishment data being sent:', {
            establishment_name: establishmentData.get('establishment_name'),
            location: establishmentData.get('location'),
            latitude: establishmentData.get('latitude'),
            longitude: establishmentData.get('longitude'),
            maps_data: establishmentData.get('maps_data'),
            description: establishmentData.get('description'),
            schedule: establishmentData.get('schedule'),
            category: establishmentData.get('category')
        });
        
        // Debug: Log file information
        console.log('Files being sent:', {
            file_count: establishmentData.getAll('pictures[]').length,
            files: Array.from(establishmentData.getAll('pictures[]')).map(file => ({
                name: file.name,
                size: file.size,
                type: file.type
            }))
        });
        
        // Create FormData for user
        const userData = new FormData(userForm);
        
        // First, create the establishment
        fetch('/superadmin/establishments', {
            method: 'POST',
            body: establishmentData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Establishment response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Establishment creation response:', data);
            if (data.success) {
                // Now create the user account
                const userPayload = {
                    name: userEstablishmentName.value,
                    email: userData.get('email'),
                    password: userData.get('password'),
                    password_confirmation: userData.get('password_confirmation'),
                    role: 'admin'
                };

                console.log('Creating user with payload:', userPayload);

                return fetch('/superadmin/users', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(userPayload)
                });
            } else {
                throw new Error(data.message);
            }
        })
        .then(response => {
            console.log('User response status:', response.status);
            console.log('User response headers:', response.headers);
            
            if (!response.ok) {
                // Try to get error details from response
                return response.json().then(errorData => {
                    console.error('User creation error response:', errorData);
                    throw new Error(`HTTP error! status: ${response.status}, message: ${errorData.message || 'Unknown error'}`);
                }).catch(() => {
                    throw new Error(`HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(userData => {
            console.log('User creation response:', userData);
            hideButtonLoading('createAccountBtn');
            hideFullPageLoader();
            
            if (userData.success) {
                alert('Establishment and admin account created successfully!');
                closeUserModal();
                window.location.reload();
            } else {
                alert('Error creating user account: ' + (userData.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error details:', error);
            hideButtonLoading('createAccountBtn');
            hideFullPageLoader();
            alert('An error occurred while creating the establishment and user account: ' + error.message);
        });
    });

    // Edit form submission
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        showButtonLoading('updateEstablishmentBtn');
        showFullPageLoader('Updating establishment...');
        
        // Ensure schedule JSON is generated before submission
        updateEditScheduleJSON();
        
        const formData = new FormData(editForm);
        const establishmentId = document.getElementById('edit_establishment_id').value;
        
        // Validate required fields
        const establishmentName = formData.get('establishment_name');
        const location = formData.get('location');
        const description = formData.get('description');
        const category = formData.get('category');
        const schedule = formData.get('schedule');
        
        if (!establishmentName || !location || !description || !category || !schedule) {
            alert('Please fill in all required fields before submitting.');
            return;
        }
        
        // Debug: Log all form data being sent
        console.log('Schedule data being sent:', schedule);
        console.log('Establishment name:', establishmentName);
        console.log('Location:', location);
        console.log('Description:', description);
        console.log('Category:', category);
        
        // Send FormData directly for file uploads
        formData.append('_method', 'PUT');
        
        fetch(`/superadmin/establishments/${establishmentId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            hideButtonLoading('updateEstablishmentBtn');
            hideFullPageLoader();
            
            if (data.success) {
                alert('Establishment updated successfully!');
                closeEditModal();
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hideButtonLoading('updateEstablishmentBtn');
            hideFullPageLoader();
            alert('An error occurred while updating the establishment. Please check the console for details.');
        });
    });

    // Add event listeners for time inputs to update JSON
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    days.forEach(day => {
        const openInput = document.getElementById(`${day}_open`);
        const closeInput = document.getElementById(`${day}_close`);
        const editOpenInput = document.getElementById(`edit_${day}_open`);
        const editCloseInput = document.getElementById(`edit_${day}_close`);
        
        if (openInput) openInput.addEventListener('change', updateScheduleJSON);
        if (closeInput) closeInput.addEventListener('change', updateScheduleJSON);
        if (editOpenInput) editOpenInput.addEventListener('change', updateEditScheduleJSON);
        if (editCloseInput) editCloseInput.addEventListener('change', updateEditScheduleJSON);
    });
    
    // Initialize schedule with default values
    updateScheduleJSON();
});

// View photos function
function viewPhotos(establishmentId) {
    // Find the establishment data from the table
    const establishmentRow = document.querySelector(`tr[data-establishment-id="${establishmentId}"]`);
    if (!establishmentRow) {
        alert('Establishment not found');
        return;
    }

    const establishmentName = establishmentRow.querySelector('.text-sm.font-medium.text-gray-900').textContent;
    
    // Fetch photos for this establishment
    fetch(`/superadmin/establishments/${establishmentId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Establishment data:', data); // Debug log
        const photoViewerTitle = document.getElementById('photoViewerTitle');
        const photoViewerContent = document.getElementById('photoViewerContent');
        const photoViewerModal = document.getElementById('photoViewerModal');
        
        photoViewerTitle.textContent = `${establishmentName} Photos`;
        
        if (data.pictures && data.pictures.length > 0) {
            console.log('Pictures data:', data.pictures); // Debug log
            const photosHTML = data.pictures.map((picture, index) => `
                <div class="flex flex-col items-center">
                    <div class="mb-2 text-sm text-gray-600">Photo ${index + 1}: ${picture.image_path}</div>
                    <img src="/storage/${picture.image_path}" 
                         alt="${establishmentName}" 
                         class="max-w-full h-auto max-h-96 rounded-lg shadow-lg"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div style="display: none; text-align: center; padding: 20px; color: #666;">
                        <i data-lucide="image-off" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                        <p>Image could not be loaded</p>
                        <p class="text-xs">Path: /storage/${picture.image_path}</p>
                    </div>
                </div>
            `).join('');
            
            photoViewerContent.innerHTML = photosHTML;
        } else {
            photoViewerContent.innerHTML = `
                <div class="text-center py-8">
                    <i data-lucide="image-off" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <p class="text-gray-500">No photos available for this establishment.</p>
                </div>
            `;
        }
        
        photoViewerModal.classList.remove('hidden');
        lucide.createIcons(); // Reinitialize icons for the new content
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while loading photos.');
    });
}

// Delete establishment function
function deleteEstablishment(id) {
    if (confirm('Are you sure you want to delete this establishment?')) {
        showFullPageLoader('Deleting establishment...');
        
        fetch(`/superadmin/establishments/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideFullPageLoader();
            
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hideFullPageLoader();
            alert('An error occurred while deleting the establishment.');
        });
    }
}

// Delete picture function
function deletePicture(pictureId) {
    if (confirm('Are you sure you want to delete this picture?')) {
        fetch(`/superadmin/establishment-pictures/${pictureId}`, {
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
}

// Edit establishment function
function editEstablishment(id) {
    // Fetch establishment data
    fetch(`/superadmin/establishments/${id}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Populate form fields
        document.getElementById('edit_establishment_id').value = data.id;
        document.getElementById('edit_establishment_name').value = data.establishment_name;
        document.getElementById('edit_location').value = data.location;
        document.getElementById('edit_maps_data').value = data.maps_data || '';
        document.getElementById('edit_description').value = data.description;
        document.getElementById('edit_category').value = data.category || '';
        
        // Handle maps data for edit form
        if (data.maps_data) {
            // Try to extract coordinates from existing maps data
            const iframeMatch = data.maps_data.match(/!3d(-?\d+\.?\d*)!2d(-?\d+\.?\d*)/);
            if (iframeMatch) {
                const lat = parseFloat(iframeMatch[1]);
                const lng = parseFloat(iframeMatch[2]);
                document.getElementById('edit_latitude').value = lat;
                document.getElementById('edit_longitude').value = lng;
                
                // Set map center and place marker
                if (editMap) {
                    const location = { lat: lat, lng: lng };
                    editMap.setCenter(location);
                    placeMarker(location, editMap, 'edit');
                }
            }
        }
        
        // Load schedule data with error handling
        if (data.schedule) {
            try {
                loadScheduleToForm(data.schedule, true);
            } catch (scheduleError) {
                console.error('Error loading schedule:', scheduleError);
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
                loadScheduleToForm(defaultSchedule, true);
            }
        }
        
        // Load current pictures
        const currentPictures = document.getElementById('currentPictures');
        currentPictures.innerHTML = '';
        if (data.pictures && data.pictures.length > 0) {
            data.pictures.forEach((picture, index) => {
                const pictureDiv = document.createElement('div');
                pictureDiv.className = 'relative';
                pictureDiv.innerHTML = `
                    <img src="/storage/${picture.image_path}" 
                         alt="Current photo ${index + 1}" 
                         class="w-full h-24 object-cover rounded-lg">
                    <button type="button" 
                            onclick="deletePicture(${picture.id})" 
                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                        <i data-lucide="x" class="w-3 h-3"></i>
                    </button>
                `;
                currentPictures.appendChild(pictureDiv);
            });
        } else {
            currentPictures.innerHTML = '<p class="text-gray-500 text-sm">No current pictures</p>';
        }
        
        // Show modal
        const editModal = document.getElementById('editEstablishmentModal');
        editModal.classList.remove('hidden');
        lucide.createIcons(); // Reinitialize icons for the new content
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while loading establishment data. Please try again.');
    });
}

// 24 Hours functionality
function toggle24Hours() {
    const open24HoursCheckbox = document.getElementById('open_24_hours');
    const scheduleEditor = document.getElementById('schedule_editor');
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    
    if (open24HoursCheckbox.checked) {
        // Hide the schedule editor
        scheduleEditor.style.display = 'none';
        
        // Set all days to 24 hours
        days.forEach(day => {
            const closedCheckbox = document.getElementById(`${day}_closed`);
            const openInput = document.getElementById(`${day}_open`);
            const closeInput = document.getElementById(`${day}_close`);
            
            closedCheckbox.checked = false;
            openInput.value = '00:00';
            closeInput.value = '23:59';
        });
        
        // Update the JSON
        updateScheduleJSON();
    } else {
        // Show the schedule editor
        scheduleEditor.style.display = 'block';
        
        // Reset all inputs
        days.forEach(day => {
            const openInput = document.getElementById(`${day}_open`);
            const closeInput = document.getElementById(`${day}_close`);
            
            openInput.value = '';
            closeInput.value = '';
        });
        
        // Update the JSON
        updateScheduleJSON();
    }
}

function toggleEdit24Hours() {
    const open24HoursCheckbox = document.getElementById('edit_open_24_hours');
    const scheduleEditor = document.getElementById('edit_schedule_editor');
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    
    if (open24HoursCheckbox.checked) {
        // Hide the schedule editor
        scheduleEditor.style.display = 'none';
        
        // Set all days to 24 hours
        days.forEach(day => {
            const closedCheckbox = document.getElementById(`edit_${day}_closed`);
            const openInput = document.getElementById(`edit_${day}_open`);
            const closeInput = document.getElementById(`edit_${day}_close`);
            
            closedCheckbox.checked = false;
            openInput.value = '00:00';
            closeInput.value = '23:59';
        });
        
        // Update the JSON
        updateEditScheduleJSON();
    } else {
        // Show the schedule editor
        scheduleEditor.style.display = 'block';
        
        // Reset all inputs
        days.forEach(day => {
            const openInput = document.getElementById(`edit_${day}_open`);
            const closeInput = document.getElementById(`edit_${day}_close`);
            
            openInput.value = '';
            closeInput.value = '';
        });
        
        // Update the JSON
        updateEditScheduleJSON();
    }
}

// Schedule handling functions
function toggleDaySchedule(day) {
    const closedCheckbox = document.getElementById(`${day}_closed`);
    const scheduleDiv = document.getElementById(`${day}_schedule`);
    const openInput = document.getElementById(`${day}_open`);
    const closeInput = document.getElementById(`${day}_close`);
    
    if (closedCheckbox.checked) {
        scheduleDiv.style.display = 'none';
        openInput.value = '';
        closeInput.value = '';
    } else {
        scheduleDiv.style.display = 'block';
    }
    
    updateScheduleJSON();
}

function toggleEditDaySchedule(day) {
    const closedCheckbox = document.getElementById(`edit_${day}_closed`);
    const scheduleDiv = document.getElementById(`edit_${day}_schedule`);
    const openInput = document.getElementById(`edit_${day}_open`);
    const closeInput = document.getElementById(`edit_${day}_close`);
    
    if (closedCheckbox.checked) {
        scheduleDiv.style.display = 'none';
        openInput.value = '';
        closeInput.value = '';
    } else {
        scheduleDiv.style.display = 'block';
    }
    
    updateEditScheduleJSON();
}

function updateScheduleJSON() {
    const schedule = {};
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    const open24HoursCheckbox = document.getElementById('open_24_hours');
    
    // Check if 24 hours is selected
    if (open24HoursCheckbox && open24HoursCheckbox.checked) {
        days.forEach(day => {
            schedule[day.charAt(0).toUpperCase() + day.slice(1)] = '12:00 AM - 11:59 PM';
        });
    } else {
        days.forEach(day => {
            const closedCheckbox = document.getElementById(`${day}_closed`);
            const openInput = document.getElementById(`${day}_open`);
            const closeInput = document.getElementById(`${day}_close`);
            
            if (closedCheckbox && closedCheckbox.checked) {
                schedule[day.charAt(0).toUpperCase() + day.slice(1)] = 'Closed';
            } else if (openInput && closeInput && openInput.value && closeInput.value) {
                const openTime = formatTimeForDisplay(openInput.value);
                const closeTime = formatTimeForDisplay(closeInput.value);
                schedule[day.charAt(0).toUpperCase() + day.slice(1)] = `${openTime} - ${closeTime}`;
            } else {
                // Set default hours if no input is provided
                schedule[day.charAt(0).toUpperCase() + day.slice(1)] = '9:00 AM - 6:00 PM';
            }
        });
    }
    
    document.getElementById('schedule').value = JSON.stringify(schedule);
}

function updateEditScheduleJSON() {
    const schedule = {};
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    
    days.forEach(day => {
        const closedCheckbox = document.getElementById(`edit_${day}_closed`);
        const openInput = document.getElementById(`edit_${day}_open`);
        const closeInput = document.getElementById(`edit_${day}_close`);
        
        if (closedCheckbox.checked) {
            schedule[day.charAt(0).toUpperCase() + day.slice(1)] = 'Closed';
        } else if (openInput.value && closeInput.value) {
            const openTime = formatTimeForDisplay(openInput.value);
            const closeTime = formatTimeForDisplay(closeInput.value);
            schedule[day.charAt(0).toUpperCase() + day.slice(1)] = `${openTime} - ${closeTime}`;
        }
    });
    
    document.getElementById('edit_schedule').value = JSON.stringify(schedule);
}

function formatTimeForDisplay(timeString) {
    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour === 0 ? 12 : hour > 12 ? hour - 12 : hour;
    return `${displayHour}:${minutes} ${ampm}`;
}

function parseTimeFromDisplay(timeString) {
    const match = timeString.match(/(\d{1,2}):(\d{2})\s*(AM|PM)/);
    if (match) {
        let hour = parseInt(match[1]);
        const minute = match[2];
        const period = match[3];
        
        if (period === 'PM' && hour !== 12) {
            hour += 12;
        }
        if (period === 'AM' && hour === 12) {
            hour = 0;
        }
        
        return `${hour.toString().padStart(2, '0')}:${minute}`;
    }
    return '';
}

function loadScheduleToForm(scheduleData, isEdit = false) {
    const prefix = isEdit ? 'edit_' : '';
    let schedule;
    
    try {
        schedule = typeof scheduleData === 'string' ? JSON.parse(scheduleData) : scheduleData;
    } catch (error) {
        console.error('Error parsing schedule data:', error);
        // Return default schedule if parsing fails
        schedule = {
            'Monday': '9:00 AM - 6:00 PM',
            'Tuesday': '9:00 AM - 6:00 PM',
            'Wednesday': '9:00 AM - 6:00 PM',
            'Thursday': '9:00 AM - 6:00 PM',
            'Friday': '9:00 AM - 6:00 PM',
            'Saturday': '9:00 AM - 6:00 PM',
            'Sunday': '9:00 AM - 6:00 PM'
        };
    }
    
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    
    // Check if all days are 24 hours
    let is24Hours = true;
    const dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    
    dayNames.forEach(dayName => {
        if (schedule[dayName] && schedule[dayName] !== 'Closed') {
            const timeMatch = schedule[dayName].match(/(\d{1,2}):(\d{2})\s*(AM|PM)\s*-\s*(\d{1,2}):(\d{2})\s*(AM|PM)/);
            if (timeMatch) {
                const openTime = parseTimeFromDisplay(`${timeMatch[1]}:${timeMatch[2]} ${timeMatch[3]}`);
                const closeTime = parseTimeFromDisplay(`${timeMatch[4]}:${timeMatch[5]} ${timeMatch[6]}`);
                
                // Check if it's 24 hours (00:00 to 23:59)
                if (openTime !== '00:00' || closeTime !== '23:59') {
                    is24Hours = false;
                }
            } else {
                is24Hours = false;
            }
        } else {
            is24Hours = false;
        }
    });
    
    // Set 24 hours checkbox
    const open24HoursCheckbox = document.getElementById(`${prefix}open_24_hours`);
    const scheduleEditor = document.getElementById(`${prefix}schedule_editor`);
    
    if (is24Hours) {
        open24HoursCheckbox.checked = true;
        scheduleEditor.style.display = 'none';
    } else {
        open24HoursCheckbox.checked = false;
        scheduleEditor.style.display = 'block';
    }
    
    days.forEach(day => {
        const dayName = day.charAt(0).toUpperCase() + day.slice(1);
        const closedCheckbox = document.getElementById(`${prefix}${day}_closed`);
        const openInput = document.getElementById(`${prefix}${day}_open`);
        const closeInput = document.getElementById(`${prefix}${day}_close`);
        const scheduleDiv = document.getElementById(`${prefix}${day}_schedule`);
        
        if (schedule[dayName]) {
            if (schedule[dayName] === 'Closed') {
                closedCheckbox.checked = true;
                scheduleDiv.style.display = 'none';
                openInput.value = '';
                closeInput.value = '';
            } else {
                closedCheckbox.checked = false;
                scheduleDiv.style.display = 'block';
                
                const timeMatch = schedule[dayName].match(/(\d{1,2}):(\d{2})\s*(AM|PM)\s*-\s*(\d{1,2}):(\d{2})\s*(AM|PM)/);
                if (timeMatch) {
                    const openTime = parseTimeFromDisplay(`${timeMatch[1]}:${timeMatch[2]} ${timeMatch[3]}`);
                    const closeTime = parseTimeFromDisplay(`${timeMatch[4]}:${timeMatch[5]} ${timeMatch[6]}`);
                    openInput.value = openTime;
                    closeInput.value = closeTime;
                }
            }
        }
    });
    
    if (isEdit) {
        updateEditScheduleJSON();
    } else {
        updateScheduleJSON();
    }
}

// QR Code Functions
let currentEstablishmentId = null;

function viewQRCode(establishmentId) {
    currentEstablishmentId = establishmentId;
    
    // Find the establishment data from the table
    const establishmentRow = document.querySelector(`tr[data-establishment-id="${establishmentId}"]`);
    if (!establishmentRow) {
        alert('Establishment not found');
        return;
    }

    const establishmentName = establishmentRow.querySelector('.text-sm.font-medium.text-gray-900').textContent;
    
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
            qrCodeTitle.textContent = `${establishmentName} - QR Code`;
            qrCodeContent.innerHTML = data.qr_code;
            qrCodeLink.href = `/stamp/process/${establishmentId}`;
            qrCodeLink.textContent = `${window.location.origin}/stamp/process/${establishmentId}`;
            qrCodeModal.classList.remove('hidden');
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
        showFullPageLoader('Generating QR code...');
        
        fetch(`/stamp/generate-qr/${establishmentId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideFullPageLoader();
            
            if (data.success) {
                alert('QR code generated successfully!');
                window.location.reload();
            } else {
                alert('Error generating QR code: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hideFullPageLoader();
            alert('An error occurred while generating the QR code.');
        });
    }
}

function regenerateQRCode(establishmentId) {
    if (confirm('Regenerate QR code for this establishment? This will create a new QR code.')) {
        showFullPageLoader('Regenerating QR code...');
        
        fetch(`/stamp/regenerate-qr/${establishmentId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideFullPageLoader();
            
            if (data.success) {
                alert('QR code regenerated successfully!');
                window.location.reload();
            } else {
                alert('Error regenerating QR code: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hideFullPageLoader();
            alert('An error occurred while regenerating the QR code.');
        });
    }
}

function testStampFromQR() {
    if (!currentEstablishmentId) {
        alert('No establishment selected');
        return;
    }
    
    showFullPageLoader('Testing stamp collection...');
    
    fetch(`/stamp/test/${currentEstablishmentId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideFullPageLoader();
        
        if (data.success) {
            alert(data.message);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        hideFullPageLoader();
        alert('An error occurred while testing the stamp collection.');
    });
}

function downloadQRCode() {
    if (!currentEstablishmentId) {
        alert('No establishment selected');
        return;
    }
    
    // Create a temporary link to download the QR code
    const qrCodeElement = qrCodeContent.querySelector('svg');
    if (qrCodeElement) {
        const svgData = new XMLSerializer().serializeToString(qrCodeElement);
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();
        
        img.onload = function() {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
            
            const link = document.createElement('a');
            link.download = `qr-code-establishment-${currentEstablishmentId}.png`;
            link.href = canvas.toDataURL();
            link.click();
        };
        
        img.src = 'data:image/svg+xml;base64,' + btoa(svgData);
    } else {
        alert('QR code not found');
    }
}
