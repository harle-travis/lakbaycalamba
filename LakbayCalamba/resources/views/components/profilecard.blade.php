<div class="relative w-full max-w-2xl mx-auto rounded-t-3xl overflow-hidden" style="aspect-ratio: 565/261.92;">
    <!-- Background image -->
    <img src="{{ $backgroundUrl }}"
         alt="Profile Background"
         class="absolute inset-0 object-cover w-full h-full z-0">


    <!-- Content inside the card -->
    <div class="relative z-20 flex justify-between items-end h-full px-6 pb-6 text-white">
        <!-- Left: Info -->
        <div>
            <h2 class="text-xl font-semibold">{{ $name ?? 'Juan Dela Cruz' }}</h2>
            <p class="text-sm text-white/80">Lakbay ID: {{ $lakbayId ?? 'LK-ID#09234523445' }}</p>
        </div>

    </div>
</div>