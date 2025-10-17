@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
{{-- Header with Guest Logging Button --}}
<div class="flex justify-between items-center mb-6">
  <h1 class="text-2xl font-bold text-gray-800">Dashboard - {{ $establishment->establishment_name }}</h1>
  <button id="logGuestBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center space-x-2">
    <i data-lucide="user-plus" class="w-4 h-4"></i>
    <span>Log Guest</span>
  </button>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
  <div class="bg-white p-6 shadow rounded-lg">
    <h3 class="text-sm text-gray-500">Today's Visitors</h3>
    <p class="text-3xl font-bold mt-2">{{ $todayVisitors }}</p>
    <div class="text-xs text-gray-400 mt-1">
      <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded mr-2">{{ $todayStamps }} registered</span>
      <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded">{{ $todayGuests }} guests</span>
    </div>
  </div>
  <div class="bg-white p-6 shadow rounded-lg">
    <h3 class="text-sm text-gray-500">This Week's Total</h3>
    <p class="text-3xl font-bold mt-2">{{ $weekVisitors }}</p>
    <div class="text-xs text-gray-400 mt-1">
      <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded mr-2">{{ $weekStamps }} registered</span>
      <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded">{{ $weekGuests }} guests</span>
    </div>
  </div>
  <div class="bg-white p-6 shadow rounded-lg">
    <h3 class="text-sm text-gray-500">This Month's Total</h3>
    <p class="text-3xl font-bold mt-2">{{ $monthVisitors }}</p>
    <div class="text-xs text-gray-400 mt-1">
      <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded mr-2">{{ $monthStamps }} registered</span>
      <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded">{{ $monthGuests }} guests</span>
    </div>
  </div>
</div>

{{-- Charts --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
  <div class="bg-white p-6 shadow rounded-lg">
    <div class="flex justify-between items-center mb-4 pb-2 border-b">
      <h3 class="font-semibold text-gray-700">Weekly Visitor Trends</h3>
      <div class="flex space-x-2">
        <select id="weeklyPeriod" class="px-3 py-1 border border-gray-300 rounded text-sm">
          <option value="4">Last 4 Weeks</option>
          <option value="8">Last 8 Weeks</option>
          <option value="12">Last 12 Weeks</option>
        </select>
      </div>
    </div>
    <div class="relative" style="height: 300px;">
      <canvas id="visitorTrends"></canvas>
    </div>
  </div>
  <div class="bg-white p-6 shadow rounded-lg">
    <div class="flex justify-between items-center mb-4 pb-2 border-b">
      <h3 class="font-semibold text-gray-700">Monthly Visitor Trends</h3>
      <div class="flex space-x-2">
        <select id="monthlyPeriod" class="px-3 py-1 border border-gray-300 rounded text-sm">
          <option value="4">Last 4 Months</option>
          <option value="6">Last 6 Months</option>
          <option value="12">Last 12 Months</option>
        </select>
      </div>
    </div>
    <div class="relative" style="height: 300px;">
      <canvas id="monthlyVisitors"></canvas>
    </div>
  </div>
</div>

{{-- Tourist Feedback --}}
<div class="bg-white p-6 shadow rounded-lg mb-6">
  <h3 class="font-semibold text-gray-700 mb-4 pb-3 border-b">Tourist Feedback</h3>

  {{-- Rating summary --}}
  <div class="flex flex-col items-center justify-center text-center mb-6">
    <p class="text-2xl font-bold">
      {{ number_format($averageRating, 1) }} <span class="text-gray-700">/ 5</span>
      <span class="text-sm text-gray-500">from {{ $totalReviews }} reviews</span>
    </p>

    {{-- Tabs --}}
    <div class="flex mt-4 text-sm rounded-md ring-1 ring-gray-200 overflow-hidden">
      <button class="feedback-tab px-4 py-2 bg-white hover:bg-gray-100 text-blue-600 font-semibold" data-sort="newest">Newest</button>
      <button class="feedback-tab px-4 py-2 bg-white hover:bg-gray-100 text-gray-700" data-sort="highest">Highest Rated</button>
      <button class="feedback-tab px-4 py-2 bg-white hover:bg-gray-100 text-gray-700" data-sort="lowest">Lowest Rated</button>
    </div>
  </div>

  {{-- Feedback + Overview --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Feedback list --}}
    <div class="lg:col-span-2 space-y-4" id="feedback-list">
      @forelse($reviews as $review)
        <article class="p-4 rounded-md border border-gray-200" data-rating="{{ $review->rating }}" data-date="{{ $review->created_at->toISOString() }}">
          <p class="font-medium text-gray-700">
            @for($i = 1; $i <= 5; $i++)
              @if($i <= $review->rating)
                ★
              @else
                ☆
              @endif
            @endfor
            {{ $review->rating }} stars • {{ $review->user->name ?? 'Anonymous' }}
          </p>
          <p class="text-gray-600 mt-1">{{ $review->comment }}</p>
          <p class="text-xs text-gray-400 mt-2">{{ $review->created_at->format('F j, Y') }}</p>
        </article>
      @empty
        <div class="text-center py-8">
          <i data-lucide="message-circle" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
          <p class="text-gray-500">No reviews yet for this establishment.</p>
        </div>
      @endforelse
    </div>

    {{-- Satisfaction overview --}}
    <aside class="lg:col-span-1">
      <div class="rounded-md border border-gray-200 p-4">
        <h4 class="font-semibold text-gray-700 mb-2">Visitor Satisfaction Overview</h4>
        <ul class="space-y-2 text-sm">
          @for($i = 5; $i >= 1; $i--)
            <li class="flex items-center justify-between">
              <span class="text-yellow-500">
                @for($j = 1; $j <= 5; $j++)
                  @if($j <= $i)
                    ★
                  @else
                    ☆
                  @endif
                @endfor
              </span>
              <span>{{ $ratingDistribution[$i] }}%</span>
            </li>
          @endfor
        </ul>
      </div>
    </aside>
  </div>
</div>

{{-- Guest Logging Modal --}}
<div id="guestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
  <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
    <div class="mt-3">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Log Guest Visit</h3>
        <button id="closeGuestModal" class="text-gray-400 hover:text-gray-600">
          <i data-lucide="x" class="w-6 h-6"></i>
        </button>
      </div>
      
      <form id="guestForm" class="space-y-4">
        @csrf
        <div>
          <label for="guest_name" class="block text-sm font-medium text-gray-700 mb-2">Guest Name *</label>
          <input type="text" id="guest_name" name="guest_name" required
                 class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                 placeholder="Enter guest name">
        </div>
        
        <div>
          <label for="guest_contact" class="block text-sm font-medium text-gray-700 mb-2">Contact (Optional)</label>
          <input type="text" id="guest_contact" name="guest_contact"
                 class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                 placeholder="Phone number or email">
        </div>
        
        <div class="flex justify-end space-x-3 pt-4 border-t">
          <button type="button" id="cancelGuestBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            Cancel
          </button>
          <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
            Log Guest
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Initialize charts with dynamic data
  let weeklyChart, monthlyChart;
  
  // Weekly chart data
  const weeklyData = @json($weeklyData);
  const weeklyLabels = weeklyData.map(item => item.week);
  const weeklyCounts = weeklyData.map(item => item.count);
  
  // Monthly chart data
  const monthlyData = @json($monthlyData);
  const monthlyLabels = monthlyData.map(item => item.month);
  const monthlyCounts = monthlyData.map(item => item.count);
  
  // Initialize weekly chart
  weeklyChart = new Chart(document.getElementById('visitorTrends').getContext('2d'), {
    type: 'line',
    data: {
      labels: weeklyLabels,
      datasets: [{
        label: 'Visitors',
        data: weeklyCounts,
        borderColor: 'orange',
        backgroundColor: 'rgba(255,165,0,0.2)',
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // Initialize monthly chart
  monthlyChart = new Chart(document.getElementById('monthlyVisitors').getContext('2d'), {
    type: 'bar',
    data: {
      labels: monthlyLabels,
      datasets: [{
        label: 'Visitors',
        data: monthlyCounts,
        backgroundColor: 'blue'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // Update charts when period changes
  document.getElementById('weeklyPeriod').addEventListener('change', function() {
    updateChart('week', this.value);
  });

  document.getElementById('monthlyPeriod').addEventListener('change', function() {
    updateChart('month', this.value);
  });

  function updateChart(period, weeks) {
    fetch(`/admin/visitor-data?period=${period}&weeks=${weeks}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const labels = data.data.map(item => item.label);
          const counts = data.data.map(item => item.count);
          
          if (period === 'week') {
            weeklyChart.data.labels = labels;
            weeklyChart.data.datasets[0].data = counts;
            weeklyChart.update();
          } else {
            monthlyChart.data.labels = labels;
            monthlyChart.data.datasets[0].data = counts;
            monthlyChart.update();
          }
        }
      })
      .catch(error => {
        console.error('Error updating chart:', error);
      });
  }

  // Guest logging functionality
  const guestModal = document.getElementById('guestModal');
  const logGuestBtn = document.getElementById('logGuestBtn');
  const closeGuestModal = document.getElementById('closeGuestModal');
  const cancelGuestBtn = document.getElementById('cancelGuestBtn');
  const guestForm = document.getElementById('guestForm');

  logGuestBtn.addEventListener('click', () => {
    guestModal.classList.remove('hidden');
  });

  function closeGuestModalFunc() {
    guestModal.classList.add('hidden');
    guestForm.reset();
  }

  closeGuestModal.addEventListener('click', closeGuestModalFunc);
  cancelGuestBtn.addEventListener('click', closeGuestModalFunc);

  guestModal.addEventListener('click', function(e) {
    if (e.target === guestModal) {
      closeGuestModalFunc();
    }
  });

  guestForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(guestForm);
    
    fetch('/admin/log-guest', {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Guest logged successfully!');
        closeGuestModalFunc();
        location.reload(); // Refresh to update stats
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while logging the guest.');
    });
  });

  // Feedback sorting
  const tabs = document.querySelectorAll('.feedback-tab');
  const list = document.getElementById('feedback-list');

  function sortFeedback(mode) {
    const items = Array.from(list.children);
    items.sort((a, b) => {
      if (mode === 'newest') {
        return new Date(b.dataset.date) - new Date(a.dataset.date);
      } else if (mode === 'highest') {
        return Number(b.dataset.rating) - Number(a.dataset.rating);
      } else {
        return Number(a.dataset.rating) - Number(b.dataset.rating);
      }
    });
    items.forEach(i => list.appendChild(i));
  }

  tabs.forEach(btn => {
    btn.addEventListener('click', () => {
      tabs.forEach(b => b.classList.remove('text-blue-600','font-semibold'));
      tabs.forEach(b => b.classList.add('text-gray-700'));
      btn.classList.add('text-blue-600','font-semibold');
      btn.classList.remove('text-gray-700');
      sortFeedback(btn.dataset.sort);
    });
  });

  sortFeedback('newest');
</script>
@endsection
