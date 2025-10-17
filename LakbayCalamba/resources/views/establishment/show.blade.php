@extends('layouts.app')

@section('title', $establishment->establishment_name)

@section('content')
<div class="min-h-screen">
    <!-- Back Button -->
    <div class="container mx-auto px-4 py-6">
        <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800 mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>

        <!-- Image Gallery -->
        <div class="mb-8">
            @if($establishment->pictures->count() > 0)
                <!-- Main Image -->
                <div class="mb-4">
                    <img id="mainImage"
                                 src="{{ url('storage/' . $establishment->pictures->first()->image_path) }}"
                                 alt="{{ $establishment->establishment_name }}"
                                 class="w-full h-auto max-h-96 object-contain rounded-lg shadow-lg cursor-pointer hover:opacity-90 transition-opacity bg-gray-100"
                                 onclick="openImageViewer(0)">
                </div>
                
                <!-- Thumbnail Images -->
                @foreach($establishment->pictures as $index => $picture)
                     <img src="{{ url('storage/' . $picture->image_path) }}"
                                  alt="{{ $establishment->establishment_name }}"
                                  class="w-24 h-24 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity flex-shrink-0"
                                    onclick="openImageViewer({{ $index }})">
                @endforeach

            @else
                <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                    <span class="text-gray-400 text-lg">No images available</span>
                </div>
            @endif
        </div>

        <!-- Image Viewer Modal -->
        <div id="imageViewerModal" class="fixed inset-0 bg-black bg-opacity-95 z-50 hidden flex items-center justify-center">
            <div class="relative max-w-7xl max-h-full p-4 w-full h-full flex items-center justify-center">
                <!-- Close Button - Top Right -->
                <button onclick="closeImageViewer()" class="absolute top-6 right-6 text-white hover:text-gray-300 z-20 bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-70 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <!-- Navigation Arrows - More Visible -->
                @if($establishment->pictures->count() > 1)
                    <button id="prevBtn" onclick="previousImage()" class="absolute left-6 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 z-20 bg-black bg-opacity-50 rounded-full p-3 hover:bg-opacity-70 transition-all">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button id="nextBtn" onclick="nextImage()" class="absolute right-6 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 z-20 bg-black bg-opacity-50 rounded-full p-3 hover:bg-opacity-70 transition-all">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                @endif
                
                <!-- Main Image -->
                <img id="modalImage" src="" alt="{{ $establishment->establishment_name }}" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
                
                <!-- Image Counter - Bottom Center -->
                @if($establishment->pictures->count() > 1)
                    <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 text-white text-sm bg-black bg-opacity-70 px-4 py-2 rounded-full backdrop-blur-sm">
                        <span id="imageCounter">1</span> / {{ $establishment->pictures->count() }}
                    </div>
                @endif
                
                <!-- Slideshow Controls -->
                @if($establishment->pictures->count() > 1)
                    <div class="absolute bottom-6 right-6 flex gap-2">
                        <button id="playPauseBtn" onclick="toggleSlideshow()" class="text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-70 transition-all">
                            <svg id="playIcon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1m-6-8h8a2 2 0 012 2v8a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                            </svg>
                            <svg id="pauseIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Establishment Details -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex justify-between items-start mb-4">
                <h1 class="text-3xl font-bold text-gray-800">{{ $establishment->establishment_name }}</h1>
                <button onclick="toggleFavorite({{ $establishment->id }})" 
                        class="favorite-btn text-gray-400 hover:text-red-500 transition-colors duration-200"
                        data-establishment-id="{{ $establishment->id }}">
                    <svg class="w-6 h-6 heart-outline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <svg class="w-6 h-6 heart-filled hidden" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </button>
            </div>
            
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-3">Description</h2>
                <p class="text-gray-600 leading-relaxed">
                    {{ $establishment->description ?? 'No description available for this establishment.' }}
                </p>
            </div>
        </div>

        <!-- Schedule and Map Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Schedule Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Schedule</h2>
                
                <!-- Location -->
                <div class="flex items-center mb-4">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-gray-600">{{ $establishment->location }}</span>
                </div>

                <!-- Schedule -->
                <div class="space-y-2">
                    @php
                        $schedule = $establishment->schedule;
                        if (is_string($schedule)) {
                            $schedule = json_decode($schedule, true);
                        }
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    @endphp
                    
                    @foreach($days as $day)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">{{ $day }}</span>
                            <span class="text-gray-600">
                                @if(isset($schedule[$day]))
                                    @if($schedule[$day] === 'Closed')
                                        <span class="text-red-500">Closed</span>
                                    @else
                                        {{ $schedule[$day] }}
                                    @endif
                                @else
                                    <span class="text-gray-400">Not set</span>
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>

                <!-- Claim Stamp Button -->
                @auth
                    <a href="{{ route('qr.scanner') }}" class="block w-full bg-blue-700 text-white rounded-lg py-3 font-semibold hover:bg-blue-800 transition-colors mt-6 text-center">
                        Claim Your Stamp
                    </a>
                @else
                    <button onclick="showStampPopup()" class="w-full bg-blue-700 text-white rounded-lg py-3 font-semibold hover:bg-blue-800 transition-colors mt-6">
                        Claim Your Stamp
                    </button>
                @endauth
            </div>

            <!-- Map Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Location</h2>
                @if($establishment->maps_data)
                    <div class="rounded-lg overflow-hidden">
                        {!! $establishment->maps_data !!}
                    </div>
                @else
                    <div class="bg-gray-200 rounded-lg h-64 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="text-gray-500">No map data available</p>
                            <p class="text-sm text-gray-400 mt-2">{{ $establishment->location }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Reviews</h2>
            
            <!-- Review Filters -->
            <div class="flex flex-wrap gap-2 mb-6">
                <button class="px-4 py-2 bg-blue-700 text-white rounded-full text-sm font-medium filter-btn" data-rating="all">All Reviews</button>
                <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-full text-sm font-medium hover:bg-gray-300 filter-btn" data-rating="5">5 Stars</button>
                <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-full text-sm font-medium hover:bg-gray-300 filter-btn" data-rating="4">4 Stars</button>
                <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-full text-sm font-medium hover:bg-gray-300 filter-btn" data-rating="3">3 Stars</button>
                <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-full text-sm font-medium hover:bg-gray-300 filter-btn" data-rating="2">2 Stars</button>
                <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-full text-sm font-medium hover:bg-gray-300 filter-btn" data-rating="1">1 Star</button>
                <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-full text-sm font-medium hover:bg-gray-300 ml-auto sort-btn" data-sort="newest">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    Most Recent
                </button>
            </div>

            <!-- Reviews List -->
            <div id="reviewsContainer">
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-700 mx-auto mb-4"></div>
                    <p class="text-gray-500">Loading reviews...</p>
                </div>
            </div>

            <!-- Add Review Form -->
            <div class="mt-6 pt-6 border-t border-gray-200" id="reviewFormContainer">
                @auth
                    @if(auth()->user()->role === 'tourist')
                        <div id="reviewFormContent">
                            <!-- Loading state -->
                            <div id="reviewFormLoading" class="text-center py-4">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-700 mx-auto mb-2"></div>
                                <p class="text-gray-500">Checking review eligibility...</p>
                            </div>
                            
                            <!-- Review form (will be shown if user has stamp) -->
                            <form id="reviewForm" class="hidden space-y-4">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Write a Review</h3>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                    <div class="flex gap-2" id="ratingStars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" class="text-2xl text-gray-300 hover:text-yellow-400 transition-colors star-btn" data-rating="{{ $i }}">
                                                ★
                                            </button>
                                        @endfor
                                    </div>
                                    <input type="hidden" id="selectedRating" name="rating" value="">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                                    <textarea id="reviewComment" name="comment" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Share your experience..."></textarea>
                                </div>
                                <button type="submit" class="bg-blue-700 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-800 transition-colors">
                                    Submit Review
                                </button>
                            </form>
                            
                            <!-- No stamp message -->
                            <div id="noStampMessage" class="hidden text-center py-6">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                                    <div class="flex items-center justify-center mb-4">
                                        <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Visit Required</h3>
                                    <p class="text-yellow-700 mb-4">You must visit this establishment and get a stamp before you can leave a review.</p>
                                    <div class="flex items-center justify-center space-x-2 text-sm text-yellow-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Scan the QR code at the establishment to get your stamp</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Already reviewed message -->
                            <div id="alreadyReviewedMessage" class="hidden text-center py-4">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                    <div class="flex items-center justify-center mb-4">
                                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-green-800 mb-2">Review Submitted</h3>
                                    <p class="text-green-700">You have already reviewed this establishment.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">Only tourist users can submit reviews.</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-500 mb-3">Sign in to submit a review</p>
                        <a href="{{ route('login') }}" class="inline-block bg-blue-700 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-800 transition-colors">
                            Sign In
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>

<script>
const establishmentId = {{ $establishment->id }};
let currentFilter = 'all';
let currentSort = 'newest';
let selectedRating = 0;

function changeMainImage(src) {
    document.getElementById('mainImage').src = src;
}

document.addEventListener('DOMContentLoaded', function() {
    
    // Check favorite status on page load
    checkFavoriteStatus(establishmentId);
    
    // Filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentFilter = this.dataset.rating;
            updateFilterButtons();
            loadReviews();
        });
    });
    
    // Sort button
    document.querySelectorAll('.sort-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentSort = this.dataset.sort;
            updateSortButton();
            loadReviews();
        });
    });
    
    // Initialize review form (will be called when form becomes visible)
    initializeReviewForm();
    
    // Load initial reviews
    loadReviews();
    checkUserReview();
});

function initializeReviewForm() {
    console.log('initializeReviewForm called');
    
    // Review form submission
    const reviewForm = document.getElementById('reviewForm');
    console.log('reviewForm element:', reviewForm);
    
    if (reviewForm) {
        console.log('Review form found, setting up event listeners');
        
        // Add event listener to the form
        reviewForm.addEventListener('submit', function(e) {
            console.log('Form submit event triggered');
            e.preventDefault();
            submitReview();
        });
        
        console.log('Form submit event listener attached');
    } else {
        console.log('Review form not found!');
    }
    
    // Star rating functionality
    const starButtons = document.querySelectorAll('.star-btn');
    starButtons.forEach((star, index) => {
        star.addEventListener('click', function() {
            selectedRating = index + 1;
            updateStarDisplay();
            document.getElementById('selectedRating').value = selectedRating;
        });
        
        star.addEventListener('mouseenter', function() {
            highlightStars(index + 1);
        });
    });
    
    const ratingStars = document.getElementById('ratingStars');
    if (ratingStars) {
        ratingStars.addEventListener('mouseleave', function() {
            updateStarDisplay();
        });
    }
}

function updateStarDisplay() {
    const stars = document.querySelectorAll('.star-btn');
    stars.forEach((star, index) => {
        if (index < selectedRating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}

function highlightStars(rating) {
    const stars = document.querySelectorAll('.star-btn');
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

function updateFilterButtons() {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        if (btn.dataset.rating === currentFilter) {
            btn.classList.remove('bg-gray-200', 'text-gray-700');
            btn.classList.add('bg-blue-700', 'text-white');
        } else {
            btn.classList.remove('bg-blue-700', 'text-white');
            btn.classList.add('bg-gray-200', 'text-gray-700');
        }
    });
}

function updateSortButton() {
    const sortBtn = document.querySelector('.sort-btn');
    if (currentSort === 'newest') {
        sortBtn.innerHTML = `
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            Most Recent
        `;
        sortBtn.dataset.sort = 'newest';
    } else {
        sortBtn.innerHTML = `
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            Oldest First
        `;
        sortBtn.dataset.sort = 'oldest';
    }
}

function loadReviews() {
    const container = document.getElementById('reviewsContainer');
    container.innerHTML = `
        <div class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-700 mx-auto mb-4"></div>
            <p class="text-gray-500">Loading reviews...</p>
        </div>
    `;
    
    const params = new URLSearchParams({
        rating: currentFilter,
        sort: currentSort
    });
    
    fetch(`/establishment/${establishmentId}/reviews?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayReviews(data.reviews);
            } else {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-red-500">Error loading reviews</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-red-500">Error loading reviews</p>
                </div>
            `;
        });
}

function displayReviews(reviews) {
    const container = document.getElementById('reviewsContainer');
    
    if (reviews.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-gray-500 text-lg mb-2">No reviews yet</p>
                <p class="text-gray-400">Be the first to share your experience!</p>
            </div>
        `;
        return;
    }
    
    const reviewsHTML = reviews.map(review => `
        <div class="border-b border-gray-100 pb-6 last:border-b-0">
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                        ${review.user.name ? review.user.name.charAt(0).toUpperCase() : 'U'}
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800">${review.user.name || 'Anonymous'}</h4>
                        <div class="flex items-center">
                            ${Array.from({length: 5}, (_, i) => `
                                <svg class="w-4 h-4 ${i < review.rating ? 'text-yellow-400' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            `).join('')}
                        </div>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    <span>${new Date(review.created_at).toLocaleDateString()}</span>
                    ${review.updated_at && review.updated_at !== review.created_at ? 
                        `<span class="ml-2 text-blue-600 font-medium">(Edited)</span>` : ''}
                </div>
            </div>
            <p class="text-gray-600">${review.comment || 'No comment provided.'}</p>
        </div>
    `).join('');
    
    container.innerHTML = `<div class="space-y-6">${reviewsHTML}</div>`;
}

function checkUserReview() {
    console.log('checkUserReview called for establishment:', establishmentId);
    
    fetch(`/establishment/${establishmentId}/user-review`)
        .then(response => response.json())
        .then(data => {
            console.log('checkUserReview response:', data);
            
            // Hide loading state
            const loadingElement = document.getElementById('reviewFormLoading');
            if (loadingElement) {
                loadingElement.classList.add('hidden');
            }

            if (data.success) {
                if (data.hasReview) {
                    // User already has a review
                    document.getElementById('alreadyReviewedMessage').classList.remove('hidden');
                } else if (data.hasStamp) {
                    // User has stamp but no review - show review form
                    console.log('User has stamp, showing review form');
                    document.getElementById('reviewForm').classList.remove('hidden');
                    // Initialize form event listeners when form becomes visible
                    initializeReviewForm();
                } else {
                    // User doesn't have stamp - show no stamp message
                    document.getElementById('noStampMessage').classList.remove('hidden');
                }
            } else {
                // Error or not logged in
                console.error('Error checking user review:', data.message);
                document.getElementById('noStampMessage').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error checking user review:', error);
            // Hide loading state
            const loadingElement = document.getElementById('reviewFormLoading');
            if (loadingElement) {
                loadingElement.classList.add('hidden');
            }
            // Show no stamp message as fallback
            document.getElementById('noStampMessage').classList.remove('hidden');
        });
}

function submitReview() {
    console.log('submitReview function called');
    console.log('selectedRating:', selectedRating);
    
    if (selectedRating === 0) {
        alert('Please select a rating before submitting your review.');
        return;
    }
    
    const formData = new FormData();
    formData.append('rating', selectedRating);
    formData.append('comment', document.getElementById('reviewComment').value);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    const submitBtn = document.querySelector('#reviewForm button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Submitting...';
    submitBtn.disabled = true;
    
    fetch(`/establishment/${establishmentId}/review`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reset form
            selectedRating = 0;
            updateStarDisplay();
            document.getElementById('selectedRating').value = '';
            document.getElementById('reviewComment').value = '';
            
            // Hide form and show success message
            const formContainer = document.getElementById('reviewFormContainer');
            formContainer.innerHTML = `
                <div class="text-center py-4">
                    <p class="text-green-600 font-medium">Review submitted successfully!</p>
                </div>
            `;
            
            // Reload reviews
            loadReviews();
            
            // Show success notification
            showNotification('Review submitted successfully!', 'success');
        } else {
            // Handle specific error messages
            if (data.message && data.message.includes('stamp')) {
                // User doesn't have stamp - show no stamp message
                document.getElementById('reviewForm').classList.add('hidden');
                document.getElementById('noStampMessage').classList.remove('hidden');
            } else {
                alert(data.message || 'Failed to submit review. Please try again.');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting your review. Please try again.');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-medium z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Favorite functions
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

function toggleFavorite(establishmentId) {
    // Check if user is authenticated first
    fetch(`/favorites/${establishmentId}/check`)
        .then(response => response.json())
        .then(checkData => {
            if (!checkData.success) {
                // User not authenticated, show popup and redirect to login
                const establishmentName = document.querySelector('h1').textContent;
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
                const establishmentName = document.querySelector('h1').textContent;
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
}

function updateHeartIcon(establishmentId, isFavorited) {
    const favoriteBtn = document.querySelector(`[data-establishment-id="${establishmentId}"]`);
    if (favoriteBtn) {
        const heartOutline = favoriteBtn.querySelector('.heart-outline');
        const heartFilled = favoriteBtn.querySelector('.heart-filled');
        
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

function showStampPopup() {
    const establishmentName = document.querySelector('h1').textContent;
    const confirmed = confirm(`Please sign in to claim your stamp for "${establishmentName}".\n\nDon't have an account yet? You can sign up after clicking OK.`);
    if (confirmed) {
        window.location.href = '{{ route("login", ["redirect" => route("qr.scanner")]) }}';
    }
}

// Image viewer functions
let currentImageIndex = 0;
let images = [];
let slideshowInterval = null;
let isSlideshowPlaying = false;


function closeImageViewer() {
    const modal = document.getElementById('imageViewerModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    stopSlideshow();
}

function nextImage() {
    stopSlideshow(); // Stop slideshow when manually navigating
    if (currentImageIndex < images.length - 1) {
        currentImageIndex++;
        updateModalImage();
    }
}

function previousImage() {
    stopSlideshow(); // Stop slideshow when manually navigating
    if (currentImageIndex > 0) {
        currentImageIndex--;
        updateModalImage();
    }
}

function updateModalImage() {
    const modalImage = document.getElementById('modalImage');
    const imageCounter = document.getElementById('imageCounter');
    
    modalImage.src = images[currentImageIndex];
    
    if (imageCounter) {
        imageCounter.textContent = currentImageIndex + 1;
    }
    
    updateNavigationButtons();
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    if (prevBtn) {
        prevBtn.style.opacity = currentImageIndex === 0 ? '0.5' : '1';
        prevBtn.style.pointerEvents = currentImageIndex === 0 ? 'none' : 'auto';
    }
    
    if (nextBtn) {
        nextBtn.style.opacity = currentImageIndex === images.length - 1 ? '0.5' : '1';
        nextBtn.style.pointerEvents = currentImageIndex === images.length - 1 ? 'none' : 'auto';
    }
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('imageViewerModal');
    if (modal && !modal.classList.contains('hidden')) {
        if (e.key === 'Escape') {
            closeImageViewer();
        } else if (e.key === 'ArrowLeft') {
            previousImage();
        } else if (e.key === 'ArrowRight') {
            nextImage();
        }
    }
});

// Close modal when clicking outside the image
document.getElementById('imageViewerModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageViewer();
    }
});

// Slideshow functions
function toggleSlideshow() {
    if (isSlideshowPlaying) {
        stopSlideshow();
    } else {
        startSlideshow();
    }
}

function startSlideshow() {
    if (images.length <= 1) return;
    
    isSlideshowPlaying = true;
    const playIcon = document.getElementById('playIcon');
    const pauseIcon = document.getElementById('pauseIcon');
    
    if (playIcon && pauseIcon) {
        playIcon.classList.add('hidden');
        pauseIcon.classList.remove('hidden');
    }
    
    slideshowInterval = setInterval(() => {
        if (currentImageIndex < images.length - 1) {
            currentImageIndex++;
        } else {
            currentImageIndex = 0; // Loop back to first image
        }
        updateModalImage();
    }, 3000); // Change image every 3 seconds
}

function stopSlideshow() {
    isSlideshowPlaying = false;
    const playIcon = document.getElementById('playIcon');
    const pauseIcon = document.getElementById('pauseIcon');
    
    if (playIcon && pauseIcon) {
        playIcon.classList.remove('hidden');
        pauseIcon.classList.add('hidden');
    }
    
    if (slideshowInterval) {
        clearInterval(slideshowInterval);
        slideshowInterval = null;
    }
}

// Auto-start slideshow when modal opens (if multiple images)
function openImageViewer(index) {
    currentImageIndex = index;
   images = [
    @foreach($establishment->pictures as $picture)
        '{{ url('storage/' . $picture->image_path) }}',
    @endforeach
];

    
    const modal = document.getElementById('imageViewerModal');
    const modalImage = document.getElementById('modalImage');
    const imageCounter = document.getElementById('imageCounter');
    
    modalImage.src = images[currentImageIndex];
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    if (imageCounter) {
        imageCounter.textContent = currentImageIndex + 1;
    }
    
    updateNavigationButtons();
    
    // Auto-start slideshow if multiple images
    if (images.length > 1) {
        setTimeout(() => {
            startSlideshow();
        }, 1000); // Start slideshow after 1 second
    }
}
</script>
@endsection
