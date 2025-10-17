@extends('layouts.app')

@section('title', 'Weather')

@section('content')
<div class="min-h-screen flex flex-col justify-between">

    {{-- Weather Section --}}
    <div class="container mx-auto px-6 pt-28 pb-12">

        {{-- Location --}}
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Calamba, Laguna</h2>

        {{-- Date Display --}}
        <div class="flex items-center justify-center bg-white rounded-xl shadow-lg px-6 py-3 mb-8">
            <h5 class="text-gray-800 font-medium text-lg">
                {{ \Carbon\Carbon::now($weather['timezone'] ?? 'Asia/Manila')->isoFormat('dddd, MMMM D') }}
            </h5>
        </div>

        {{-- Weather Content --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                
            {{-- Current Weather Card --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col justify-between">

                {{-- Header --}}
                <div class="mb-4">
                    <p class="text-gray-500 text-base font-medium">Current Weather</p>
                    <p class="text-gray-400 text-sm">
                        {{ \Carbon\Carbon::createFromTimestamp($weather['current']['dt'], $weather['timezone'] ?? 'Asia/Manila')->format('g:i A') }}
                    </p>
                </div>

                {{-- Middle Section (Icon + Condition) --}}
                <div class="flex items-center justify-center mb-8">
                    {{-- ICON (Weather Icons) --}}
                    <i class="{{ $weather['current']['icon_class'] ?? 'wi wi-na' }} text-6xl text-blue-500 mr-4"></i>
                    <h3 class="text-2xl font-semibold text-gray-800">
                        {{ ucfirst($weather['current']['weather'][0]['description'] ?? 'N/A') }}
                    </h3>
                </div>

                {{-- Bottom Section --}}
                <div class="flex justify-between items-end">
                    {{-- Temperature --}}
                    <h1 class="text-5xl font-bold text-blue-600">{{ round($weather['current']['temp']) }}°C</h1>

                    {{-- Details --}}
                    <div class="text-right text-sm text-gray-700 space-y-1">
                        <p>Precipitation: 
                            <span class="font-semibold">
                                {{ isset($weather['hourly'][0]['pop']) ? round($weather['hourly'][0]['pop'] * 100) : 0 }}%
                            </span>
                        </p>
                        <p>Humidity: <span class="font-semibold">{{ $weather['current']['humidity'] }}%</span></p>
                        <p>Wind: <span class="font-semibold">{{ round($weather['current']['wind_speed'] * 3.6) }} km/h</span></p>
                    </div>
                </div>
            </div>

            {{-- Map --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden relative z-0">
                <div id="weather-map" class="w-full h-64 sm:h-[320px] lg:h-[360px] z-0"></div>
            </div>
        </div>

        {{-- Hourly Forecast --}}
<div class="bg-white rounded-2xl shadow-lg px-6 py-4 mb-8">
    <h3 class="text-gray-700 font-medium mb-4">Today's Forecast (Next 24 Hours)</h3>
    
    {{-- Scrollable 24 hours --}}
    <div class="flex gap-4 overflow-x-auto pb-2">
        @for ($i = 0; $i < 24; $i++)
            @php
                $h = $weather['hourly'][$i] ?? null;
            @endphp
            @if ($h)
                <div class="flex-shrink-0 text-center min-w-[80px]">
                    <p class="text-gray-500 text-sm">
                        {{ \Carbon\Carbon::createFromTimestamp($h['dt'], $weather['timezone'] ?? 'Asia/Manila')->format('h A') }}
                    </p>
                    {{-- ICON --}}
                    <i class="{{ $h['icon_class'] ?? 'wi wi-na' }} text-3xl text-blue-500 my-2"></i>
                    <p class="text-gray-800 font-medium">{{ round($h['temp']) }}°</p>
                </div>
            @endif
        @endfor
    </div>
</div>


        {{-- Weekly Forecast --}}
        <div class="mt-10">
            {{-- Date Display Above Weekly Forecast --}}
            <div class="flex items-center justify-center bg-white rounded-xl shadow-lg px-6 py-3 mb-8">
                <h4 class="font-semibold text-lg">
                    {{ \Carbon\Carbon::createFromTimestamp($weather['daily'][0]['dt'], $weather['timezone'] ?? 'Asia/Manila')->isoFormat('dddd, MMMM D') }}
                </h4>
            </div>

            {{-- Forecast Days Scroll --}}
            <div class="flex gap-4 overflow-x-auto pb-2">
                @foreach(array_slice($weather['daily'], 0, 7) as $d)
                    <div class="bg-white shadow-lg rounded-2xl p-7 text-center min-w-[160px]">
                        <p class="font-medium mb-2">
                            {{ \Carbon\Carbon::createFromTimestamp($d['dt'], $weather['timezone'] ?? 'Asia/Manila')->isoFormat('dddd') }}
                        </p>
                        {{-- ICON --}}
                        <i class="{{ $d['icon_class'] ?? 'wi wi-na' }} text-4xl text-blue-500 mb-2"></i>
                        <p class="text-sm text-gray-600">
                            {{ round($d['temp']['max']) }}° | {{ round($d['temp']['min']) }}°
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- Leaflet map scripts for the weather overlay --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var mapEl = document.getElementById('weather-map');
        if (!mapEl) return;
        var map = L.map('weather-map').setView([{{ $lat }}, {{ $lon }}], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        L.marker([{{ $lat }}, {{ $lon }}]).addTo(map);
    });
</script>
<style>
    /* Ensure Leaflet map doesn't float above other cards */
    .leaflet-container { z-index: 0 !important; }
    .leaflet-control-container { z-index: 1; }
</style>
@endsection
