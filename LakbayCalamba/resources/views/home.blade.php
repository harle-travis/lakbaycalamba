@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Featured Images Section -->
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="flex flex-col lg:flex-row">
            <!-- Thumbnails Section (Left Side) -->
            <div class="lg:w-1/4 p-4 bg-gray-50">
                <div class="space-y-3">
                    <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden cursor-pointer hover:opacity-80 transition-opacity" onclick="showFeaturedImage(0)">
                        <img src="{{ url('images/calambamap.png') }}" alt="Calamba Map" class="w-full h-full object-cover">
                    </div>
                    <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden cursor-pointer hover:opacity-80 transition-opacity" onclick="showFeaturedImage(1)">
                        <img src="{{ url('images/jose_rizal.png') }}" alt="Jose Rizal" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
            
            <!-- Main Image Section -->
            <div class="lg:w-3/4 relative">
                <div class="featured-image-container relative h-64 sm:h-80 lg:h-96">
                    <div class="featured-slide active absolute inset-0 transition-opacity duration-500">
                        <img src="{{ url('images/calambamap.png') }}" alt="Calamba Map" class="w-full h-full object-cover">
                    </div>
                    <div class="featured-slide absolute inset-0 transition-opacity duration-500 opacity-0">
                        <img src="{{ url('images/jose_rizal.png') }}" alt="Jose Rizal" class="w-full h-full object-cover">
                    </div>
                    
                    <!-- Image Overlay with Title -->
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-6">
                        <h2 class="text-white text-xl sm:text-2xl font-bold mb-2">SM City Calamba</h2>
                        <p class="text-white/90 text-sm sm:text-base">Discover the vibrant shopping and entertainment hub of Calamba City</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="flex flex-col items-center justify-center min-h-70 pt-16 sm:pt-20 px-4 w-full">
    <h1 class="text-2xl sm:text-3xl font-bold text-center mb-4">Which Calamba Spot Will You Discover Today?</h1>
    
    <form class="search-form mb-6 sm:mb-8 w-full max-w-3xl flex gap-2" id="searchForm">
        <input
            type="text" 
            name="search"
            id="searchInput"
            placeholder="Search for a spot..." 
            value="{{ request('search') }}"
            class="flex-1 min-w-0 w-full border rounded-full px-4 py-2"
        >
        <button type="submit" class="shrink-0 rounded-full bg-blue-700 text-white px-4 py-2">
            <svg width="20" height="20" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="9" r="7"/>
                <line x1="16" y1="16" x2="13.5" y2="13.5"/>
            </svg>
        </button>
    </form>
    
    <div class="w-full max-w-3xl grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6 sm:mb-8 text-blue-600">
        <select class="w-full border rounded-full px-4 py-2" name="status" id="statusFilter">
            <option value="all">All Status</option>
            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
        </select>
        
        <select class="w-full border rounded-full px-4 py-2" name="category" id="categoryFilter">
            <option value="all">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                    {{ $category }}
                </option>
            @endforeach
        </select>
        
        <select class="w-full border rounded-full px-4 py-2" name="rating" id="ratingFilter">
            <option value="all">All Ratings</option>
            <option value="0" {{ request('rating') === '0' ? 'selected' : '' }}>No Reviews</option>
            <option value="5" {{ request('rating') === '5' ? 'selected' : '' }}>5 Stars</option>
            <option value="4" {{ request('rating') === '4' ? 'selected' : '' }}>4+ Stars</option>
            <option value="3" {{ request('rating') === '3' ? 'selected' : '' }}>3+ Stars</option>
            <option value="2" {{ request('rating') === '2' ? 'selected' : '' }}>2+ Stars</option>
            <option value="1" {{ request('rating') === '1' ? 'selected' : '' }}>1+ Stars</option>
        </select>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8 justify-items-center" id="establishmentsGrid"> 
        @foreach($establishments as $establishment)
            <x-location-card :establishment="$establishment" />
        @endforeach
    </div>
    
    @if($establishments->count() === 0)
        <div class="text-center text-gray-500 mb-8">
            <p class="text-lg">No establishments found matching your criteria.</p>
            <p class="text-sm">Try adjusting your search or filters.</p>
        </div>
    @endif
</div>

<div class="flex justify-center mb-12">
    <a href="{{ route('all-establishments') }}" class="rounded-full bg-blue-700 text-white px-8 py-3 font-semibold hover:bg-blue-800 transition">
        See More
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const ratingFilter = document.getElementById('ratingFilter');

    // Function to apply filters by redirecting with URL parameters
    function applyFilters() {
        const params = new URLSearchParams();
        
        if (searchInput.value) params.set('search', searchInput.value);
        if (statusFilter.value !== 'all') params.set('status', statusFilter.value);
        if (categoryFilter.value !== 'all') params.set('category', categoryFilter.value);
        if (ratingFilter.value !== 'all') params.set('rating', ratingFilter.value);
        
        const newURL = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.location.href = newURL;
    }

    // Event listeners
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        applyFilters();
    });

    statusFilter.addEventListener('change', applyFilters);
    categoryFilter.addEventListener('change', applyFilters);
    ratingFilter.addEventListener('change', applyFilters);

    // Debounced search input
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 1000); // 1 second delay
    });
});

// Featured Images functionality
function showFeaturedImage(index) {
    const slides = document.querySelectorAll('.featured-slide');
    const thumbnails = document.querySelectorAll('.aspect-square');
    
    // Hide all slides
    slides.forEach(slide => {
        slide.classList.remove('active');
        slide.style.opacity = '0';
    });
    
    // Show selected slide
    slides[index].classList.add('active');
    slides[index].style.opacity = '1';
    
    // Update thumbnail borders/selection
    thumbnails.forEach((thumb, i) => {
        thumb.classList.remove('ring-2', 'ring-blue-500');
        if (i === index) {
            thumb.classList.add('ring-2', 'ring-blue-500');
        }
    });
}

// Auto-play for featured images
let featuredImageInterval;
function startFeaturedImageAutoPlay() {
    featuredImageInterval = setInterval(() => {
        const currentActive = document.querySelector('.featured-slide.active');
        const slides = document.querySelectorAll('.featured-slide');
        const currentIndex = Array.from(slides).indexOf(currentActive);
        const nextIndex = (currentIndex + 1) % slides.length;
        showFeaturedImage(nextIndex);
    }, 5000); // Change every 5 seconds
}

// Pause auto-play on hover
document.addEventListener('DOMContentLoaded', function() {
    const featuredContainer = document.querySelector('.featured-image-container');
    if (featuredContainer) {
        featuredContainer.addEventListener('mouseenter', () => {
            clearInterval(featuredImageInterval);
        });
        
        featuredContainer.addEventListener('mouseleave', () => {
            startFeaturedImageAutoPlay();
        });
        
        // Start auto-play
        startFeaturedImageAutoPlay();
        
        // Set initial thumbnail selection
        showFeaturedImage(0);
    }
});
</script>
    
@endsection