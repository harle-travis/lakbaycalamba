@extends('layouts.app')

@section('title', 'QR Code Scanner')

@section('content')
<div class="min-h-screen"><br><br><br>
    <div class="container mx-auto px-4 py-6">
        <!-- Back Button - Top Left -->
        <div class="mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center text-gray-500 hover:text-blue-800 font-medium bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Home
            </a>
        </div>

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-3">Scan QR Code</h1>
            <p class="text-gray-600 text-sm sm:text-base">Point your camera at the QR code to claim your stamp</p>
        </div>

        <!-- QR Scanner -->
        <div class="max-w-sm sm:max-w-md mx-auto bg-white rounded-xl shadow-xl overflow-hidden border border-gray-200">
            <div class="bg-blue-600 text-white p-3 sm:p-4 text-center">
                <h2 class="text-sm sm:text-base font-semibold">📷 Camera Scanner</h2>
            </div>
            <div id="qr-reader" class="w-full h-64 sm:h-80 bg-gray-100 flex items-center justify-center relative">
                <div class="text-center p-4">
                    <div class="animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-3 border-blue-600 border-t-transparent mx-auto mb-3"></div>
                    <p class="text-gray-600 font-medium text-sm sm:text-base">Initializing camera...</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Please allow camera access when prompted</p>
                </div>
            </div>
            
            <div class="p-4">
                <div id="qr-reader-results" class="hidden">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">QR Code Detected!</h3>
                        <p class="text-gray-600 mb-4">Processing your stamp...</p>
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div>
                    </div>
                </div>
                
                <div id="qr-error" class="hidden text-center">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Invalid QR Code</h3>
                    <p class="text-gray-600 mb-4">This QR code is not valid for stamp collection.</p>
                    <button onclick="restartScanner()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Try Again
                    </button>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="max-w-sm sm:max-w-md mx-auto mt-6 sm:mt-8 bg-blue-50 rounded-xl p-4 sm:p-6 border border-blue-100">
            <div class="flex items-center mb-3 sm:mb-4">
                <div class="w-6 h-6 sm:w-8 sm:h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2 sm:mr-3">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm sm:text-base font-semibold text-blue-800">How to use:</h3>
            </div>
            <ul class="text-xs sm:text-sm text-blue-700 space-y-2">
                <li class="flex items-start">
                    <span class="w-4 h-4 sm:w-5 sm:h-5 bg-blue-200 text-blue-700 rounded-full flex items-center justify-center text-xs font-medium mr-2 sm:mr-3 mt-0.5 flex-shrink-0">1</span>
                    <span>Allow camera access when your browser prompts you</span>
                </li>
                <li class="flex items-start">
                    <span class="w-4 h-4 sm:w-5 sm:h-5 bg-blue-200 text-blue-700 rounded-full flex items-center justify-center text-xs font-medium mr-2 sm:mr-3 mt-0.5 flex-shrink-0">2</span>
                    <span>Point your camera at the establishment's QR code</span>
                </li>
                <li class="flex items-start">
                    <span class="w-4 h-4 sm:w-5 sm:h-5 bg-blue-200 text-blue-700 rounded-full flex items-center justify-center text-xs font-medium mr-2 sm:mr-3 mt-0.5 flex-shrink-0">3</span>
                    <span>Hold steady until the code is automatically detected</span>
                </li>
                <li class="flex items-start">
                    <span class="w-4 h-4 sm:w-5 sm:h-5 bg-blue-200 text-blue-700 rounded-full flex items-center justify-center text-xs font-medium mr-2 sm:mr-3 mt-0.5 flex-shrink-0">4</span>
                    <span>Your stamp will be collected automatically!</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Include QR Scanner Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
let html5QrcodeScanner;

function onScanSuccess(decodedText, decodedResult) {
    console.log(`Code scanned = ${decodedText}`, decodedResult);
    
    // Show processing state
    document.getElementById('qr-reader').classList.add('hidden');
    document.getElementById('qr-reader-results').classList.remove('hidden');
    
    // Extract establishment ID from URL
    const url = new URL(decodedText);
    const pathParts = url.pathname.split('/');
    const establishmentId = pathParts[pathParts.length - 1];
    
    if (establishmentId && !isNaN(establishmentId)) {
        // Redirect to stamp collection page
        window.location.href = `/stamp/process/${establishmentId}`;
    } else {
        // Invalid QR code
        showError();
    }
}

function onScanFailure(error) {
    // Handle scan failure, usually better to ignore and keep scanning.
    // console.warn(`Code scan error = ${error}`);
}

function showError() {
    document.getElementById('qr-reader-results').classList.add('hidden');
    document.getElementById('qr-error').classList.remove('hidden');
}

function restartScanner() {
    document.getElementById('qr-reader').classList.remove('hidden');
    document.getElementById('qr-reader-results').classList.add('hidden');
    document.getElementById('qr-error').classList.add('hidden');
    
    // Restart the scanner
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear();
        html5QrcodeScanner.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onScanSuccess,
            onScanFailure
        );
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on HTTPS or localhost
    const isSecure = location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1';
    
    if (!isSecure) {
        document.getElementById('qr-reader').innerHTML = `
            <div class="text-center p-4 sm:p-6">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2 sm:mb-3">Camera Access Required</h3>
                <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">QR code scanning requires camera access. Please use HTTPS or localhost to access this feature.</p>
                <a href="{{ route('home') }}" class="bg-blue-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-blue-700 transition-colors text-sm sm:text-base font-medium">
                    Back to Home
                </a>
            </div>
        `;
        return;
    }
    
    // Initialize QR scanner
    html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader",
        { 
            fps: 10, 
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        },
        false
    );
    
    html5QrcodeScanner.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        onScanSuccess,
        onScanFailure
    );
});
</script>
@endsection

