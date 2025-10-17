@extends('layouts.app')

@section('title', 'About Us - Lakbay Calamba')

@section('content')
<!-- Hero Section -->
<div class="relative bg-gradient-to-br from-blue-500 to-blue-600 min-h-[50vh] sm:min-h-[60vh] flex items-center rounded-2xl mx-4 sm:mx-6">
    <!-- Background Image -->
    <img src="{{ asset('images/jose_rizal.png') }}" alt="Jose Rizal" class="absolute inset-0 w-full h-full object-cover opacity-30 select-none pointer-events-none" style="object-position: center 15%;" />
    
    <!-- Content -->
    <div class="relative z-10 container mx-auto px-4 py-10 sm:py-12">
        <div class="grid md:grid-cols-2 gap-6 sm:gap-8 items-center">
            <!-- Left Content -->
            <div class="text-white space-y-3 sm:space-y-4">
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-wide uppercase text-center md:text-left" style="color:#fff !important;">About Us</h1>
                <p class="text-base sm:text-lg leading-relaxed text-center">
                    Lakbay Calamba is a digital tourism companion developed to help you explore the rich culture, history, and beauty of Calamba City. Whether you're a first-time visitor or a local adventurer, Lakbay Calamba brings you closer to each destination with detailed guides, digital stamps, and easy navigation.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="py-10 sm:py-16">
    <div class="container mx-auto px-4">
        <!-- Title -->
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-900 mb-8 sm:mb-12">Features</h2>
        
        <!-- Features Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-12 text-center">
            
            <!-- Feature 1: Explore Spots -->
            <div>
                <i data-lucide="map" class="w-12 h-12 text-blue-600 mx-auto mb-4"></i>
                <h3 class="text-lg font-bold text-blue-600 mb-2">Explore Spots</h3>
                <p class="text-gray-700">
                    Discover must see landmarks and top rated tourist destinations around Calamba City. Each location includes descriptions, highlights and guides.
                </p>
            </div>
            
            <!-- Feature 2: Collect Digital Stamps -->
            <div>
                <i data-lucide="map-pin" class="w-12 h-12 text-blue-600 mx-auto mb-4"></i>
                <h3 class="text-lg font-bold text-blue-600 mb-2">Collect Digital Stamps</h3>
                <p class="text-gray-700">
                    Earn a digital stamp each time you visit a location. Collect all stamps to complete your journey and unlock special rewards.
                </p>
            </div>
            
            <!-- Feature 3: View Reports & Insights -->
            <div>
                <i data-lucide="bar-chart-2" class="w-12 h-12 text-blue-600 mx-auto mb-4"></i>
                <h3 class="text-lg font-bold text-blue-600 mb-2">View Reports & Insights</h3>
                <p class="text-gray-700">
                    Generate and review visitor statistics, feedback, and activity trends to guide better decision-making and enhance tourist experiences.
                </p>
            </div>
            
            <!-- Feature 4: Track Visitors -->
            <div>
                <i data-lucide="shopping-bag" class="w-12 h-12 text-blue-600 mx-auto mb-4"></i>
                <h3 class="text-lg font-bold text-blue-600 mb-2">Track Visitors</h3>
                <p class="text-gray-700">
                    Establishments and City Hall can monitor visitor arrivals in real time, helping improve tourism planning and management.
                </p>
            </div>

        </div>
    </div>
</div>

<!-- ✅ Lucide script for icons -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons(); // initialize icons
</script>

<!-- Mission and Purpose Section -->
<div class="py-10 sm:py-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-md p-6 sm:p-8 min-h-[240px] border border-gray-200">
            <div class="grid md:grid-cols-2 gap-8 md:gap-12">
                <div>
                    <h3 class="text-2xl font-bold text-blue-600 mb-4">Our Mission</h3>
                    <p class="text-gray-700 text-sm sm:text-base leading-snug text-justify mt-1">
                        hatdoghatdoghatdogOur mission is to promote local tourism in Calamba by using digital tools that make exploring
                        easier, more engaging, and accessible for everyone. We aim to support both travelers and local
                        tourism offices in preserving culture, boosting visitor interest, and encouraging appreciation
                        of historical landmarks and natural attractions.
                    </p>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-blue-600 mb-4">Purpose of This App</h3>
                    <p class="text-gray-700 text-sm sm:text-base leading-snug text-justify mt-1">
                        The purpose of Lakbay Calamba is to serve as a digital companion for tourists and locals who
                        want to discover the beauty and heritage of Calamba City. It helps users explore destinations,
                        get useful travel info, collect digital stamps, and track visits, all in one place. This app
                        also reduces the need for paper-based systems and makes tourism more organized and efficient.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audience Section -->
<div class="py-12">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-10 items-center">
            <div>
                <h3 class="text-2xl font-bold text-blue-600 mb-6">Who Is This For?</h3>
                <ul class="list-disc pl-6 space-y-4 text-gray-800">
                    <li>Tourists visiting Calamba</li>
                    <li>Students doing local explorations or heritage tours</li>
                    <li>Families looking for new places to visit</li>
                    <li>Locals who want to know more about their city</li>
                    <li>The Calamba City Government and its Tourism Office</li>
                </ul>
            </div>
            <div class="w-full">
                <div class="rounded-2xl shadow-md overflow-hidden border border-gray-200">
                    <img src="{{ asset('images/calambamap.png') }}" alt="Calamba map" class="w-full h-56 sm:h-72 md:h-80 object-cover" />
                </div>
            </div>
        </div>

        <div class="text-center mt-10 sm:mt-12 px-4">
            <p class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 mb-4">Ready to explore Calamba? Let's get started!</p>
            <a href="{{ url('/') }}" class="inline-block px-5 py-3 sm:px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow transition-colors">Explore Now</a>
        </div>
    </div>
</div>
@endsection
