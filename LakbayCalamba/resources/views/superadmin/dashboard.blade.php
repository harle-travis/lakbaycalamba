@extends('layouts.superadmin')

@section('title', 'Dashboard')
@section('breadcrumb')
<li>
    <div class="flex items-center">
        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Dashboard</span>
    </div>
</li>
@endsection

@section('content')
<div class="p-6">
    

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Visitors Today -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Visitors Today</p>
                    <p class="text-3xl font-bold">{{ number_format($todayVisitors) }}</p>
                    <p class="text-blue-100 text-sm">
                        @if($todayChange >= 0)
                            +{{ $todayChange }}% from yesterday
                        @else
                            {{ $todayChange }}% from yesterday
                        @endif
                    </p>
                    <div class="text-xs text-blue-200 mt-1">
                        <span class="inline-block bg-blue-400 text-blue-900 px-2 py-1 rounded mr-2">{{ $todayStamps }} registered</span>
                        <span class="inline-block bg-blue-300 text-blue-900 px-2 py-1 rounded">{{ $todayGuests }} guests</span>
                    </div>
                </div>
                <div class="bg-blue-400 rounded-full p-3">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- This Week -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">This Week</p>
                    <p class="text-3xl font-bold">{{ number_format($weekVisitors) }}</p>
                    <p class="text-green-100 text-sm">
                        @if($weekChange >= 0)
                            +{{ $weekChange }}% from last week
                        @else
                            {{ $weekChange }}% from last week
                        @endif
                    </p>
                    <div class="text-xs text-green-200 mt-1">
                        <span class="inline-block bg-green-400 text-green-900 px-2 py-1 rounded mr-2">{{ $weekStamps }} registered</span>
                        <span class="inline-block bg-green-300 text-green-900 px-2 py-1 rounded">{{ $weekGuests }} guests</span>
                    </div>
                </div>
                <div class="bg-green-400 rounded-full p-3">
                    <i data-lucide="calendar" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">This Month</p>
                    <p class="text-3xl font-bold">{{ number_format($monthVisitors) }}</p>
                    <p class="text-yellow-100 text-sm">
                        @if($monthChange >= 0)
                            +{{ $monthChange }}% from last month
                        @else
                            {{ $monthChange }}% from last month
                        @endif
                    </p>
                    <div class="text-xs text-yellow-200 mt-1">
                        <span class="inline-block bg-yellow-400 text-yellow-900 px-2 py-1 rounded mr-2">{{ $monthStamps }} registered</span>
                        <span class="inline-block bg-yellow-300 text-yellow-900 px-2 py-1 rounded">{{ $monthGuests }} guests</span>
                    </div>
                </div>
                <div class="bg-yellow-400 rounded-full p-3">
                    <i data-lucide="trending-up" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Active Tourist Spots -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Establishments</p>
                    <p class="text-3xl font-bold">{{ number_format($activeEstablishments) }}</p>
                    <p class="text-purple-100 text-sm">All locations active</p>
                </div>
                <div class="bg-purple-400 rounded-full p-3">
                    <i data-lucide="map-pin" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Date and Time Display with Date Picker -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <form method="GET" action="{{ route('superadmin.dashboard') }}" class="flex items-center space-x-3">
                <div class="flex items-center space-x-2">
                    <label for="start_date" class="text-sm font-medium text-gray-700">From:</label>
                    <input type="date" 
                           id="start_date" 
                           name="start_date" 
                           value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                           class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center space-x-2">
                    <label for="end_date" class="text-sm font-medium text-gray-700">To:</label>
                    <input type="date" 
                           id="end_date" 
                           name="end_date" 
                           value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                           class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded-lg text-sm transition-colors">
                    Apply
                </button>
                <a href="{{ route('superadmin.dashboard') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-1 rounded-lg text-sm transition-colors">
                    Reset
                </a>
            </form>
        </div>
        <div class="flex items-center space-x-4">
            <span id="datetime" class="text-gray-600 text-sm"></span>
        </div>
    </div>

    <!-- Visitor Trends Chart -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Visitor Trends (Last 7 Days)</h3>
        </div>
        <div class="h-64 bg-gray-50 rounded-lg p-4">
            <canvas id="visitorTrendsChart"></canvas>
        </div>
    </div>

    <!-- Visitors Tracking Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Visitors Tracking</h3>
            <div class="flex items-center space-x-2">
                <select class="border border-gray-300 rounded-lg px-3 py-1 text-sm">
                    <option>Sort By</option>
                    <option></option>
                    <option>Sort By</option>

                </select>
                <button class="flex items-center space-x-1 bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded-lg text-sm transition-colors">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    <span>Descending</span>
                </button>
                <button class="p-1 text-gray-500 hover:text-gray-700 transition-colors">
                    <i data-lucide="maximize-2" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Establishment</th>
                        @if(request('start_date') || request('end_date'))
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Custom Range</th>
                        @else
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Today</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">This Week</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">This Month</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($establishmentStats as $establishment)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-800">{{ $establishment->establishment_name }}</td>
                        @if(request('start_date') || request('end_date'))
                            <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($establishment->custom_range_visitors) }}</td>
                        @else
                            <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($establishment->today_visitors) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($establishment->week_visitors) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($establishment->month_visitors) }}</td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ request('start_date') || request('end_date') ? 2 : 4 }}" class="px-4 py-8 text-center text-gray-500">
                            <i data-lucide="map-pin" class="w-8 h-8 mx-auto mb-2 text-gray-400"></i>
                            <p>No establishments found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Live date & time (Asia/Manila)
    function updateDateTime() {
        const now = new Date();
        const opts = {
            year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit',
            hour12: true, timeZone: 'Asia/Manila'
        };
        document.getElementById('datetime').textContent =
            now.toLocaleString('en-PH', opts);
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Visitor Trends Chart
    const ctx = document.getElementById('visitorTrendsChart').getContext('2d');
    const visitorTrendsData = @json($visitorTrends);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: visitorTrendsData.map(item => item.date),
            datasets: [{
                label: 'Visitors',
                data: visitorTrendsData.map(item => item.visitors),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endsection
