@extends('layouts.app')

@section('title', 'All Establishments - Lakbay Calamba')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-8 mt-6 sm:mt-8">
        <form id="searchForm" method="GET" action="{{ route('all-establishments') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <!-- Search Input -->
                <div>
                    <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" 
                           id="searchInput" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search establishments or locations..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="categoryFilter" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="categoryFilter" 
                            name="category" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ request('category') === 'all' ? 'selected' : '' }}>All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="statusFilter" 
                            name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open Now</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <!-- Rating Filter -->
                <div>
                    <label for="ratingFilter" class="block text-sm font-medium text-gray-700 mb-2">Minimum Rating</label>
                    <select id="ratingFilter" 
                            name="rating" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ request('rating') === 'all' ? 'selected' : '' }}>All Ratings</option>
                        <option value="0" {{ request('rating') === '0' ? 'selected' : '' }}>No Reviews</option>
                        <option value="1" {{ request('rating') === '1' ? 'selected' : '' }}>1+ Stars</option>
                        <option value="2" {{ request('rating') === '2' ? 'selected' : '' }}>2+ Stars</option>
                        <option value="3" {{ request('rating') === '3' ? 'selected' : '' }}>3+ Stars</option>
                        <option value="4" {{ request('rating') === '4' ? 'selected' : '' }}>4+ Stars</option>
                        <option value="5" {{ request('rating') === '5' ? 'selected' : '' }}>5 Stars</option>
                    </select>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 sm:justify-between sm:items-center">
                <div class="flex space-x-3 order-2 sm:order-1">
                   
                    <a href="{{ route('all-establishments') }}" 
                       class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition font-medium">
                        Clear Filters
                    </a>
                </div>
                <div class="text-sm text-gray-600 order-1 sm:order-2 text-center sm:text-right">
                    Showing {{ $establishments->count() }} establishment{{ $establishments->count() !== 1 ? 's' : '' }}
                </div>
            </div>
        </form>
    </div>
    
    <!-- Establishments Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8 justify-items-center" id="establishmentsGrid"> 
        @foreach($establishments as $establishment)
            <x-location-card :establishment="$establishment" />
        @endforeach
    </div>
    
    @if($establishments->count() === 0)
        <div class="text-center text-gray-500 mb-8">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <p class="text-lg">No establishments found matching your criteria.</p>
            <p class="text-sm">Try adjusting your search or filters.</p>
        </div>
    @endif

    <!-- Back to Home -->
    <div class="flex justify-center mb-12">
        <a href="{{ route('home') }}" class="rounded-full bg-gray-600 text-white px-8 py-3 font-semibold hover:bg-gray-700 transition">
            ← Back to Home
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const ratingFilter = document.getElementById('ratingFilter');

    // Auto-submit form when filters change
    [statusFilter, categoryFilter, ratingFilter].forEach(filter => {
        filter.addEventListener('change', function() {
            searchForm.submit();
        });
    });

    // Debounced search
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchForm.submit();
        }, 500);
    });
});
</script>
@endsection
