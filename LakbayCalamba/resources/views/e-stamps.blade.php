@extends('layouts.app')

@section('title', 'E-Stamps')

@section('content')
    <div class="container mt-2 sm:mt-4 min-h-70 pt-8 sm:pt-10 px-4">
        {{-- Back Button --}}
        <a href="{{ url()->previous() }}" class="btn btn-link">← Back</a>

        {{-- User Profile Section --}}
        <x-profilecard
            name="{{ auth()->user()->name }}"
            lakbayId="{{ auth()->user()->lakbay_id }}"
            backgroundUrl="{{ url('images/profilecardtemp.png') }}"
        />
        <br>
        
        
        {{-- Earned Lakbay Stamps --}}
        <div class="flex justify-center mb-4 sm:mb-6 max-w-3xl mx-auto w-full">
            <h3 class="section-title bg-blue-700 text-white px-4 sm:px-6 py-3 rounded-xl font-bold text-center shadow-sm w-full text-base sm:text-lg">
                Lakbay Stamps ({{ $stamps->count() }})
            </h3>
        </div>
        
        @if($stamps->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-8 mb-6 sm:mb-8">
                @foreach($stamps as $stamp)
                    <div class="bg-white rounded-xl shadow p-4 w-full max-w-sm mx-auto">
                        <div class="bg-gray-200 rounded-lg h-44 sm:h-40 mb-4 flex items-center justify-center overflow-hidden">
                            @if($stamp->establishment->pictures->count() > 0)
                                <img src="{{ asset('storage/' . $stamp->establishment->pictures->first()->image_path) }}" 
                                     alt="{{ $stamp->establishment->establishment_name }}" 
                                     class="object-cover h-full w-full rounded-lg">
                            @else
                                <span class="text-gray-400">No Image</span>
                            @endif
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h2 class="font-bold text-lg truncate w-3/4">{{ $stamp->establishment->establishment_name }}</h2>
                            <div class="text-green-600">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center text-sm mb-1">
                            <span class="text-yellow-500 mr-1">★</span>
                            @if($stamp->establishment->reviews->count() > 0)
                                @php
                                    $averageRating = $stamp->establishment->reviews->avg('rating');
                                    $reviewCount = $stamp->establishment->reviews->count();
                                @endphp
                                <span>{{ number_format($averageRating, 1) }} ({{ $reviewCount }} {{ $reviewCount == 1 ? 'review' : 'reviews' }})</span>
                            @else
                                <span>No reviews yet</span>
                            @endif
                        </div>
                        <div class="flex items-center text-sm text-gray-700 mb-2">
                            <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                <circle cx="12" cy="9" r="2.5"/>
                            </svg>
                            <span>{{ $stamp->establishment->location }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600 mb-4">
                            <svg class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Visited: {{ $stamp->visit_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($stamp->establishment->category)
                                <span class="border border-blue-600 text-blue-600 rounded-full px-3 py-1 text-xs">{{ $stamp->establishment->category }}</span>
                            @endif
                            <span class="border border-green-600 text-green-600 rounded-full px-3 py-1 text-xs">✓ Stamped</span>
                        </div>
                        <a href="{{ route('establishment.show', $stamp->establishment) }}" class="block w-full bg-blue-700 text-white rounded-full py-2 text-center font-medium hover:bg-blue-800 transition">View Details</a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10 sm:py-12 px-4">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Stamps Yet</h3>
                <p class="text-gray-500 mb-6">Start exploring Calamba and collect your first stamp!</p>
                <a href="{{ route('home') }}" class="bg-blue-700 text-white px-6 py-3 rounded-full font-medium hover:bg-blue-800 transition">
                    Explore Establishments
                </a>
            </div>
        @endif

        {{-- Favorite Places Section --}}
        <div class="flex justify-center mb-4 sm:mb-6 max-w-3xl mx-auto w-full">
            <h3 class="section-title bg-blue-700 text-white px-4 sm:px-6 py-3 rounded-xl font-bold text-center shadow-sm w-full text-base sm:text-lg">
                My Favorites ({{ $favorites->count() }})
            </h3>
        </div>
        
        @if($favorites->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-8 mb-6 sm:mb-8">
                @foreach($favorites as $favorite)
                    <div class="bg-white rounded-xl shadow p-4 w-full max-w-sm mx-auto">
                        <div class="bg-gray-200 rounded-lg h-44 sm:h-40 mb-4 flex items-center justify-center overflow-hidden">
                            @if($favorite->establishment->pictures->count() > 0)
                                <img src="{{ asset('storage/' . $favorite->establishment->pictures->first()->image_path) }}" 
                                     alt="{{ $favorite->establishment->establishment_name }}" 
                                     class="object-cover h-full w-full rounded-lg">
                            @else
                                <span class="text-gray-400">No Image</span>
                            @endif
                        </div>
                        <div class="flex justify-between items-start mb-2">
                            <h2 class="font-bold text-lg truncate w-3/4">{{ $favorite->establishment->establishment_name }}</h2>
                            <button onclick="removeFavorite({{ $favorite->establishment->id }}, '{{ $favorite->establishment->establishment_name }}')" 
                                    class="text-red-500 hover:text-red-700 transition-colors duration-200 p-1 hover:bg-red-50 rounded">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center text-sm mb-1">
                            <span class="text-yellow-500 mr-1">★</span>
                            @if($favorite->establishment->reviews->count() > 0)
                                @php
                                    $averageRating = $favorite->establishment->reviews->avg('rating');
                                    $reviewCount = $favorite->establishment->reviews->count();
                                @endphp
                                <span>{{ number_format($averageRating, 1) }} ({{ $reviewCount }} {{ $reviewCount == 1 ? 'review' : 'reviews' }})</span>
                            @else
                                <span>No reviews yet</span>
                            @endif
                        </div>
                        <div class="flex items-center text-sm text-gray-700 mb-2">
                            <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                <circle cx="12" cy="9" r="2.5"/>
                            </svg>
                            <span>{{ $favorite->establishment->location }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600 mb-4">
                            <svg class="w-4 h-4 mr-1 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span>Added to favorites: {{ $favorite->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($favorite->establishment->category)
                                <span class="border border-blue-600 text-blue-600 rounded-full px-3 py-1 text-xs">{{ $favorite->establishment->category }}</span>
                            @endif
                            <span class="border border-red-600 text-red-600 rounded-full px-3 py-1 text-xs">♥ Favorite</span>
                        </div>
                        <a href="{{ route('establishment.show', $favorite->establishment) }}" class="block w-full bg-blue-700 text-white rounded-full py-2 text-center font-medium hover:bg-blue-800 transition">View Details</a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10 sm:py-12 px-4">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Favorites Yet</h3>
                <p class="text-gray-500 mb-6">Start exploring and add places to your favorites by clicking the heart icon!</p>
                
            </div>
        @endif

    </div>

    <!-- My Reviews Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">My Reviews</h2>
            <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">{{ $reviews->count() }} Review{{ $reviews->count() !== 1 ? 's' : '' }}</span>
        </div>

        @if($reviews->count() > 0)
            <div class="space-y-4">
                @foreach($reviews as $review)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-gray-800 mb-1">
                                    <a href="{{ route('establishment.show', $review->establishment) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $review->establishment->establishment_name }}
                                    </a>
                                </h3>
                                <p class="text-gray-600 text-sm mb-2">{{ $review->establishment->location }}</p>
                                
                                <!-- Star Rating -->
                                <div class="flex items-center mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-600">{{ $review->rating }} star{{ $review->rating !== 1 ? 's' : '' }}</span>
                                </div>
                                
                                <!-- Review Comment -->
                                @if($review->comment)
                                    <p class="text-gray-700 mb-2">{{ $review->comment }}</p>
                                @endif
                                
                                <!-- Review Date and Edit Status -->
                                <div class="flex items-center text-sm text-gray-500">
                                    <span>Reviewed {{ $review->created_at->diffForHumans() }}</span>
                                    @if($review->updated_at && $review->updated_at->ne($review->created_at))
                                        <span class="mx-2">•</span>
                                        <span class="text-blue-600 font-medium">Edited {{ $review->updated_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex space-x-2 ml-4">
                                <button onclick="editReview({{ $review->id }}, {{ $review->rating }}, '{{ addslashes($review->comment) }}', '{{ $review->establishment->establishment_name }}')" 
                                        class="text-blue-600 hover:text-blue-800 transition-colors" 
                                        title="Edit Review">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button onclick="deleteReview({{ $review->id }}, '{{ $review->establishment->establishment_name }}')" 
                                        class="text-red-600 hover:text-red-800 transition-colors" 
                                        title="Delete Review">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Reviews Yet</h3>
                <p class="text-gray-500 mb-6">Share your experiences by reviewing the places you've visited!</p>
            </div>
        @endif
    </div>

    <!-- Edit Review Modal -->
    <div id="editReviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Edit Review</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600">Editing review for: <span id="editEstablishmentName" class="font-medium"></span></p>
                </div>
                
                <form id="editReviewForm">
                    <input type="hidden" id="editReviewId" name="review_id">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <div class="flex gap-2" id="editRatingStars">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="text-2xl text-gray-300 hover:text-yellow-400 transition-colors edit-star-btn" data-rating="{{ $i }}">
                                    ★
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" id="editSelectedRating" name="rating" value="">
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                        <textarea id="editReviewComment" name="comment" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Share your experience..."></textarea>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-600 transition">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 bg-blue-700 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-800 transition">
                            Update Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </div>

<script>
function removeFavorite(establishmentId, establishmentName) {
    if (confirm(`Are you sure you want to remove "${establishmentName}" from your favorites?`)) {
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
                showNotification(data.message, 'info');
                // Reload the page to update the favorites list
                location.reload();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error removing favorite:', error);
            showNotification('An error occurred while removing the favorite.', 'error');
        });
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

// Review management functions
let editSelectedRating = 0;

function editReview(reviewId, currentRating, currentComment, establishmentName) {
    // Set up the modal
    document.getElementById('editReviewId').value = reviewId;
    document.getElementById('editEstablishmentName').textContent = establishmentName;
    document.getElementById('editReviewComment').value = currentComment;
    
    // Set up star rating
    editSelectedRating = currentRating;
    updateEditStarDisplay();
    document.getElementById('editSelectedRating').value = currentRating;
    
    // Show modal
    document.getElementById('editReviewModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editReviewModal').classList.add('hidden');
    editSelectedRating = 0;
    document.getElementById('editSelectedRating').value = '';
    document.getElementById('editReviewComment').value = '';
}

function updateEditStarDisplay() {
    const stars = document.querySelectorAll('.edit-star-btn');
    stars.forEach((star, index) => {
        if (index < editSelectedRating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}

function deleteReview(reviewId, establishmentName) {
    if (confirm(`Are you sure you want to delete your review for "${establishmentName}"? This action cannot be undone.`)) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('_method', 'DELETE');
        
        fetch(`/reviews/${reviewId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Review deleted successfully!', 'success');
                // Reload the page to update the reviews list
                location.reload();
            } else {
                showNotification(data.message || 'Failed to delete review.', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting review:', error);
            showNotification('An error occurred while deleting the review.', 'error');
        });
    }
}

function highlightEditStars(rating) {
    const stars = document.querySelectorAll('.edit-star-btn');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}

function submitEditReview() {
    if (editSelectedRating === 0) {
        alert('Please select a rating before updating your review.');
        return;
    }
    
    const reviewId = document.getElementById('editReviewId').value;
    const formData = new FormData();
    formData.append('rating', editSelectedRating);
    formData.append('comment', document.getElementById('editReviewComment').value);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('_method', 'PUT');
    
    const submitBtn = document.querySelector('#editReviewForm button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Updating...';
    submitBtn.disabled = true;
    
    fetch(`/reviews/${reviewId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditModal();
            showNotification('Review updated successfully!', 'success');
            // Reload the page to update the reviews list
            location.reload();
        } else {
            alert(data.message || 'Failed to update review. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating your review. Please try again.');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Initialize edit review functionality
document.addEventListener('DOMContentLoaded', function() {
    // Edit star rating functionality
    const editStarButtons = document.querySelectorAll('.edit-star-btn');
    editStarButtons.forEach((star, index) => {
        star.addEventListener('click', function() {
            editSelectedRating = index + 1;
            updateEditStarDisplay();
            document.getElementById('editSelectedRating').value = editSelectedRating;
        });
        
        star.addEventListener('mouseenter', function() {
            highlightEditStars(index + 1);
        });
    });
    
    document.getElementById('editRatingStars').addEventListener('mouseleave', function() {
        updateEditStarDisplay();
    });
    
    // Edit form submission
    const editReviewForm = document.getElementById('editReviewForm');
    if (editReviewForm) {
        editReviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitEditReview();
        });
    }
});
</script>
@endsection
