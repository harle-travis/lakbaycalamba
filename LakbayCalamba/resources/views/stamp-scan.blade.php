@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 sm:py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6 text-center">
        <div class="mb-6">
            <div class="w-14 h-14 sm:w-16 sm:h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="map-pin" class="w-7 h-7 sm:w-8 sm:h-8 text-green-600"></i>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">Collect Your Stamp!</h1>
            <p class="text-gray-600">You've scanned the QR code for</p>
            <h2 class="text-lg sm:text-xl font-semibold text-blue-600">{{ $establishment->establishment_name }}</h2>
        </div>

        <div class="mb-6">
            <p class="text-sm text-gray-500 mb-3">Click the button below to collect your digital stamp</p>
            <p class="text-xs text-gray-400 mb-4">Note: You can only collect one stamp per establishment</p>
            <button id="collectStampBtn" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                Collect Stamp
            </button>
        </div>

        <div id="result" class="hidden">
            <!-- Result will be shown here -->
        </div>

        <div class="mt-5 sm:mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Back to Home
            </a>
        </div>
    </div>
</div>

<script>
document.getElementById('collectStampBtn').addEventListener('click', function() {
    const btn = this;
    const resultDiv = document.getElementById('result');
    
    // Disable button and show loading
    btn.disabled = true;
    btn.textContent = 'Processing...';
    
    // Make the request to collect the stamp
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log('CSRF Token:', csrfToken);
    console.log('Request URL:', `/stamp/process/{{ $establishment->id }}`);
    
    fetch(`/stamp/process/{{ $establishment->id }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        console.log('Response ok:', response.ok);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        resultDiv.classList.remove('hidden');
        
        if (data && data.success) {
            resultDiv.innerHTML = `
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <div class="flex items-center">
                        <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                        <span class="font-semibold">Success!</span>
                    </div>
                    <p class="mt-1">${data.message}</p>
                </div>
            `;
            btn.textContent = 'Stamp Collected!';
            btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            btn.classList.add('bg-green-600');
        } else {
            resultDiv.innerHTML = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <div class="flex items-center">
                        <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
                        <span class="font-semibold">Error</span>
                    </div>
                    <p class="mt-1">${data.message || 'An unexpected error occurred.'}</p>
                </div>
            `;
            btn.disabled = false;
            btn.textContent = 'Try Again';
        }
        
        // Reinitialize icons (if lucide is available)
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        console.error('Error details:', error.message);
        console.error('Error stack:', error.stack);
        resultDiv.classList.remove('hidden');
        resultDiv.innerHTML = `
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <div class="flex items-center">
                    <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
                    <span class="font-semibold">Error</span>
                </div>
                <p class="mt-1">An error occurred while processing your stamp. Please try again.</p>
            </div>
        `;
        btn.disabled = false;
        btn.textContent = 'Try Again';
        // Reinitialize icons (if lucide is available)
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    });
});
</script>
@endsection

