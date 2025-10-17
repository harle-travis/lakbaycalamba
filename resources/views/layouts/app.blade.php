<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lakbay Calamba')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Weather Icons CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.10/css/weather-icons.min.css">
    
    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="bg-white font-sans min-h-screen flex flex-col">
    @include('components.header') 
    
    <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
        @yield('content')
    </main>
    
    @include('components.footer')
    
    {{-- Initialize Lucide Icons --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        });
    </script>

    <!-- Privacy Consent Modal -->
    <div id="privacy-consent-backdrop" class="hidden fixed inset-0 bg-black/50 z-50"></div>
    <div id="privacy-consent" class="hidden fixed inset-0 z-50 flex items-center justify-center p-6">
        <div class="max-w-[560px] w-full rounded-sm bg-white text-[#1b1b18] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_8px_24px_0px_rgba(0,0,0,0.18)] border border-[#e3e3e0]">
            <div class="p-6">
                <h2 class="text-base font-medium mb-2">Data Privacy Notice</h2>
                <p class="text-sm leading-[20px] text-[#706f6c]">
                    We value your privacy. We collect and process limited personal data to provide and improve this service in accordance with the Data Privacy Act. By clicking "I Agree", you consent to the processing of your data as described in our Privacy Policy.
                </p>
                <div class="mt-4 flex items-center justify-end gap-3">
                    <a href="{{ url('/privacy-policy') }}" target="_blank" class="inline-block px-5 py-1.5 text-sm underline underline-offset-4 text-blue-700">Learn more</a>
                    <button id="privacy-consent-accept" type="button" class="inline-block px-5 py-1.5 bg-[#1b1b18] text-white rounded-sm border border-black hover:bg-black hover:border-black text-sm leading-normal">I Agree</button>
                </div>
            </div>
        </div>
    </div>
    <script>
    (function() {
        var KEY = 'privacyConsentV1';
        function hasConsent() {
            try { return localStorage.getItem(KEY) === 'true'; } catch(e) { return false; }
        }
        function setConsent() {
            try {
                localStorage.setItem(KEY, 'true');
                localStorage.setItem(KEY + ':ts', String(Date.now()));
            } catch(e) {}
        }
        function showModal(show) {
            var m = document.getElementById('privacy-consent');
            var b = document.getElementById('privacy-consent-backdrop');
            if (!m || !b) return;
            if (show) { m.classList.remove('hidden'); b.classList.remove('hidden'); }
            else { m.classList.add('hidden'); b.classList.add('hidden'); }
        }
        document.addEventListener('DOMContentLoaded', function() {
            if (!hasConsent()) {
                showModal(true);
            }
            var acceptBtn = document.getElementById('privacy-consent-accept');
            if (acceptBtn) {
                acceptBtn.addEventListener('click', function() {
                    setConsent();
                    showModal(false);
                });
            }
            var backdrop = document.getElementById('privacy-consent-backdrop');
            if (backdrop) {
                backdrop.addEventListener('click', function() {
                    // Require explicit consent; ignore backdrop clicks
                });
            }
        });
    })();
    </script>
</body>
</html>
