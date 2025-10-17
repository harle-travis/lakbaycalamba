<footer class="bg-blue-800 text-white py-4 mt-auto">
    <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-y-4 gap-x-8 md:gap-y-4 md:gap-x-24 px-4 sm:px-6 items-start">

        <!-- City Banner & Description -->
        <div>
            <a href="{{ route('home') }}" class="cursor-pointer hover:opacity-80 transition-opacity">
                <img src="{{ asset('images/calamba_logo.webp') }}" alt="Republic of the Philippines - City of Calamba" class="mb-2 h-8 sm:h-10 md:h-12 w-auto">
            </a>
            <p class="text-sm leading-relaxed text-justify">
                Calamba, officially known as the City of Calamba, is a 1st class component city in the province of
                Laguna, Philippines. According to the 2020 census, it has a population of 539,671 people. It is the
                regional center of the Calabarzon region.
            </p>
        </div>

        <!-- Contact Info -->
        <div class="pt-4 md:pt-0 border-t md:border-t-0 border-blue-700 md:border-none">
            <h2 class="text-lg font-semibold mb-2">Contact Info</h2>
            <p class="text-sm leading-relaxed text-justify">
                Address: New City Hall Complex, Bacnotan Road, Brgy. Real, City of Calamba, Laguna, 4027
            </p>
            <p class="mt-2 text-sm leading-relaxed text-justify">Phone: (049) 545 6789</p>
            <p class="mt-2 text-sm leading-relaxed text-justify">Email: info@calambacity.gov.ph</p>
        </div>

        <!-- Government Links -->
        <div class="pt-4 md:pt-0 border-t md:border-t-0 border-blue-700 md:border-none">
            <h2 class="text-lg font-semibold mb-2">Government Links</h2>
            <ul class="text-sm space-y-2">
                <li><a href="#" class="hover:underline">Official Gazette</a></li>
                <li><a href="#" class="hover:underline">Philippine Government Portal</a></li>
                <li><a href="#" class="hover:underline">LGU Transparency</a></li>
            </ul>
        </div>

    </div>
    <div class="mt-4 px-4 sm:px-6 text-xs text-blue-100/80 text-center">© {{ date('Y') }} City of Calamba. All rights reserved.</div>
</footer>
