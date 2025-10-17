@extends('layouts.app')

@section('title', 'Leaderboard - Lakbay Calamba')

@section('content')
<div class="max-w-3xl mx-auto mt-16 sm:mt-24 px-4">
    <div class="flex justify-center">
        <div class="bg-blue-700 text-white px-4 sm:px-6 py-3 rounded-xl font-bold text-center shadow-sm w-full text-base sm:text-lg">
            Overall Leaderboard
        </div>
    </div>

    <p class="text-xs sm:text-sm text-gray-600 mt-3 sm:mt-4 text-center">
        See who's leading the adventure! Below are the top explorers who have collected the most stamps.
        To be part of the leaderboard, start visiting places and claim stamps in your digital passport!
    </p>

    @php
        $rowBg = function (int $rank) {
            return match ($rank) {
                1 => 'bg-yellow-100',
                2 => 'bg-gray-100',
                3 => 'bg-amber-100',
                default => 'bg-white',
            };
        };
    @endphp

    @if($leaders->count() > 0)
        <div class="mt-5 sm:mt-6 space-y-3">
            @foreach ($leaders as $index => $leader)
                @php $rank = $index + 1; @endphp
                <div class="{{ $rowBg($rank) }} rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between px-3 sm:px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if ($rank === 1)
                                <div class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-full bg-yellow-400 shadow text-lg">
                                    🥇
                                </div>
                            @elseif ($rank === 2)
                                <div class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-full bg-gray-300 shadow text-lg">
                                    🥈
                                </div>
                            @elseif ($rank === 3)
                                <div class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-full bg-amber-300 shadow text-lg">
                                    🥉
                                </div>
                            @else
                                <div class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-full bg-gray-100 text-gray-700 font-bold">
                                    {{ $rank }}
                                </div>
                            @endif

                            <div class="flex flex-col">
                                <span class="text-gray-900 font-medium text-sm sm:text-base">{{ $leader->name }}</span>
                                <span class="text-[10px] sm:text-xs text-gray-500">{{ $leader->lakbay_id }}</span>
                            </div>
                        </div>
                        <div class="text-xs sm:text-sm font-medium text-gray-800">
                            {{ $leader->stamps_count }} {{ $leader->stamps_count == 1 ? 'stamp' : 'stamps' }} collected
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No Stamps Yet</h3>
            <p class="text-gray-500 mb-6">Be the first to collect stamps and appear on the leaderboard!</p>
            <a href="{{ route('home') }}" class="bg-blue-700 text-white px-6 py-3 rounded-full font-medium hover:bg-blue-800 transition">
                Start Exploring
            </a>
        </div>
    @endif
</div>
@endsection
