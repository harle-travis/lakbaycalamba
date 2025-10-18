@props(['establishment'])

<div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-4 w-full max-w-sm mx-auto" data-establishment-id="{{ $establishment->id }}">
    <a href="{{ route('establishment.show', $establishment) }}" class="block bg-gray-200 rounded-lg h-48 mb-4 flex items-center justify-center hover:opacity-90 transition-opacity overflow-hidden">
        @if($establishment->pictures->count() > 0)
            <img src="{{ asset('storage/' . $establishment->pictures->first()->image_path) }}" alt="{{ $establishment->establishment_name }}" class="object-cover h-full w-full rounded-lg">
        @else
            <span class="text-gray-400">No Image</span>
        @endif
    </a>
    <div class="flex justify-between items-start mb-2">
        <a href="{{ route('establishment.show', $establishment) }}" class="font-bold text-lg truncate w-3/4 hover:text-blue-600 transition-colors">
            {{ $establishment->establishment_name }}
        </a>
        <button onclick="toggleFavorite({{ $establishment->id }})" 
                class="favorite-btn text-gray-400 hover:text-red-500 transition-colors duration-200"
                data-establishment-id="{{ $establishment->id }}">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="heart-outline">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
            <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24" class="heart-filled hidden">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        </button>
    </div>
    <div class="flex items-center text-sm mb-1">
        <span class="text-yellow-500 mr-1">★</span>
        @if($establishment->reviews->count() > 0)
            @php
                $averageRating = $establishment->reviews->avg('rating');
                $reviewCount = $establishment->reviews->count();
            @endphp
            <span>{{ number_format($averageRating, 1) }} ({{ $reviewCount }} {{ $reviewCount == 1 ? 'review' : 'reviews' }})</span>
        @else
            <span>No reviews yet</span>
        @endif
        <span class="mx-2">•</span>
        @if($establishment->isCurrentlyOpen())
            <span class="text-green-600 font-semibold">Open</span>
            @if($establishment->getClosingTime() && $establishment->getClosingTime() !== 'Closed' && $establishment->getClosingTime() !== '24 Hours')
                <span class="ml-2 text-gray-500">until {{ $establishment->getClosingTime() }}</span>
            @endif
        @else
            <span class="text-red-600 font-semibold">Closed</span>
            @if($establishment->getOpeningTime() && $establishment->getOpeningTime() !== 'Closed' && $establishment->getOpeningTime() !== '24 Hours')
                <span class="ml-2 text-gray-500">Opens {{ $establishment->getOpeningTime() }}</span>
            @endif
        @endif
    </div>
    <div class="flex items-center text-sm text-gray-700 mb-2">
        <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
            <circle cx="12" cy="9" r="2.5"/>
        </svg>
        <span>{{ $establishment->location }}</span>
    </div>
    <div class="flex flex-wrap gap-2 mb-4">
        @if($establishment->category)
            <span class="border border-blue-600 text-blue-600 rounded-full px-3 py-1 text-xs">{{ $establishment->category }}</span>
        @endif
    </div>
    <a href="{{ route('establishment.show', $establishment) }}" class="block w-full bg-blue-700 text-white rounded-full py-2 text-center font-medium hover:bg-blue-800 transition">View</a>
</div>

<script>
// Make functions globally available
window.toggleFavorite = function(establishmentId) {
    // Check if user is authenticated first
    fetch(`/favorites/${establishmentId}/check`)
        .then(response => response.json())
        .then(checkData => {
            if (!checkData.success) {
                // User not authenticated, show popup and redirect to login
                const establishmentName = document.querySelector(`[data-establishment-id="${establishmentId}"] .font-bold`).textContent;
                const confirmed = confirm(`Please sign in to add "${establishmentName}" to your favorites.\n\nDon't have an account yet? You can sign up after clicking OK.`);
                if (confirmed) {
                    if (checkData.redirect) {
                        window.location.href = checkData.redirect;
                    } else {
                        window.location.href = '/login';
                    }
                }
                return;
            }
            
            if (checkData.isFavorited) {
                // If it's favorited, ask for confirmation before removing
                const establishmentName = document.querySelector(`[data-establishment-id="${establishmentId}"] .font-bold`).textContent;
                if (!confirm(`Are you sure you want to remove "${establishmentName}" from your favorites?`)) {
                    return; // User cancelled, do nothing
                }
            }
            
            // Proceed with the toggle
            fetch(`/favorites/${establishmentId}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateHeartIcon(establishmentId, data.isFavorited);
                    showNotification(data.message, data.action === 'added' ? 'success' : 'info');
                } else {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        showNotification(data.message, 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error toggling favorite:', error);
                showNotification('An error occurred while updating favorites.', 'error');
            });
        })
        .catch(error => {
            console.error('Error checking favorite status:', error);
            showNotification('An error occurred while checking favorite status.', 'error');
        });
};

// Check favorite status on page load
document.addEventListener('DOMContentLoaded', function() {
    const establishmentId = {{ $establishment->id }};
    checkFavoriteStatus(establishmentId);
});

function checkFavoriteStatus(establishmentId) {
    fetch(`/favorites/${establishmentId}/check`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateHeartIcon(establishmentId, data.isFavorited);
            }
        })
        .catch(error => {
            console.error('Error checking favorite status:', error);
        });
}


function updateHeartIcon(establishmentId, isFavorited) {
    const card = document.querySelector(`[data-establishment-id="${establishmentId}"]`);
    if (card) {
        const heartOutline = card.querySelector('.heart-outline');
        const heartFilled = card.querySelector('.heart-filled');
        const favoriteBtn = card.querySelector('.favorite-btn');
        
        if (isFavorited) {
            heartOutline.classList.add('hidden');
            heartFilled.classList.remove('hidden');
            favoriteBtn.classList.remove('text-gray-400');
            favoriteBtn.classList.add('text-red-500');
        } else {
            heartOutline.classList.remove('hidden');
            heartFilled.classList.add('hidden');
            favoriteBtn.classList.remove('text-red-500');
            favoriteBtn.classList.add('text-gray-400');
        }
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 max-w-sm ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'info' ? 'bg-blue-500 text-white' : 
        'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
