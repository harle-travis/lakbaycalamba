@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
    <!-- Date & Time -->
    <div class="flex justify-end mb-6">
        <span id="datetime" class="text-gray-600"></span>
    </div>

    <!-- Filter Buttons -->
    <div class="flex space-x-2 mb-4" id="filterButtons">
        <button data-filter="today" class="px-4 py-2 bg-blue-600 text-white rounded">Today</button>
        <button data-filter="week" class="px-4 py-2 bg-white border rounded">This Week</button>
        <button data-filter="month" class="px-4 py-2 bg-white border rounded">This Month</button>
        <button data-filter="custom" class="px-4 py-2 bg-white border rounded">Custom Range</button>
    </div>

    <!-- Summary Cards -->
    <div id="summaryCards" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-5 shadow rounded-lg">
            <div class="text-xs text-gray-500 mb-1">Total Visitors</div>
            <div id="totalVisitors" class="text-3xl font-bold">0</div>
        </div>
        <div class="bg-white p-5 shadow rounded-lg">
            <div class="text-xs text-gray-500 mb-1">Avg Daily Visitors</div>
            <div id="avgDaily" class="text-3xl font-bold">0/day</div>
        </div>
        <div class="bg-white p-5 shadow rounded-lg">
            <div class="text-xs text-gray-500 mb-1">Peak Day</div>
            <div id="peakDay" class="text-2xl font-bold">—</div>
        </div>
    </div>

    <!-- Custom Range Picker -->
    <div id="customRangePicker" class="hidden mb-6">
        <label class="block text-gray-700 font-medium">Select Date Range:</label>
        <div class="flex flex-wrap gap-3 mt-2">
            <input type="date" id="startDate" class="border p-2 rounded">
            <input type="date" id="endDate" class="border p-2 rounded">
            <button id="applyCustomRange" class="px-4 py-2 bg-blue-600 text-white rounded">
                Apply
            </button>
        </div>
    </div>

    <!-- Visitors Today -->
    <div class="mb-4">
        <p class="text-lg font-medium text-gray-700">
            Visitors Today : <span id="visitorsToday" class="text-2xl font-bold">0</span>
        </p>
    </div>

    <!-- Visitor Trends -->
    <div class="bg-white p-6 shadow rounded-lg mb-6">
        <h3 class="text-sm font-semibold text-gray-700 border-b pb-2 mb-4">Visitor Trends</h3>
        <canvas id="visitorChart" class="h-64"></canvas>
    </div>

    <!-- Visitors Table -->
    <div class="bg-white shadow rounded-lg mb-6">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="p-3 font-semibold text-gray-700">Date</th>
                    <th class="p-3 font-semibold text-gray-700">Visitors</th>
                </tr>
            </thead>
            <tbody id="visitorsTable"></tbody>
        </table>
    </div>

    <!-- Download Report (only for Custom Range) -->
    <div id="downloadWrapper" class="flex justify-center mt-6 hidden">
        <button id="downloadReport" class="px-4 py-2 bg-blue-600 text-white rounded">
            Download Report
        </button>
    </div>

    <!-- Chart.js & Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Date & Time
        function updateDateTime() {
            const now = new Date();
            const options = { year: 'numeric', month: 'long', day: 'numeric',
                              hour: 'numeric', minute: 'numeric', hour12: true };
            document.getElementById('datetime').textContent =
                now.toLocaleDateString('en-US', options);
        }
        setInterval(updateDateTime, 1000); updateDateTime();

        // Chart.js Setup
        const ctx = document.getElementById('visitorChart').getContext('2d');
        let visitorChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
                datasets: [{
                    label: 'Visitors',
                    data: [120, 190, 300, 250, 220, 310, 400],
                    borderColor: 'rgb(37, 99, 235)',
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(37, 99, 235)'
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } },
                       scales: { y: { beginAtZero: true } } }
        });

        // Dummy Data (replace with API/DB later)
        const reportData = {
            today: {
                labels: ["8AM","10AM","12PM","2PM","4PM","6PM","8PM"],
                values: [30,45,50,60,70,55,80],
                table: [
                    { date:"August 21, 2025", visitors:80 },
                    { date:"August 20, 2025", visitors:65 }
                ]
            },
            week: {
                labels: ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
                values: [120,190,300,250,220,310,400],
                table: [
                    { date:"Aug 21, 2025", visitors:245 },
                    { date:"Aug 20, 2025", visitors:198 },
                    { date:"Aug 19, 2025", visitors:150 },
                    { date:"Aug 18, 2025", visitors:180 },
                ]
            },
            month: {
                labels: ["Week 1","Week 2","Week 3","Week 4"],
                values: [800,950,1000,870],
                table: [
                    { date:"Aug 2025 (Week 4)", visitors:870 },
                    { date:"Aug 2025 (Week 3)", visitors:1000 },
                    { date:"Aug 2025 (Week 2)", visitors:950 },
                    { date:"Aug 2025 (Week 1)", visitors:800 },
                ]
            },
            custom: {
                labels: ["July","Aug","Sept","Oct"],
                values: [2000,2500,2200,2800],
                table: [
                    { date:"Oct 2025", visitors:2800 },
                    { date:"Sept 2025", visitors:2200 },
                    { date:"Aug 2025", visitors:2500 },
                    { date:"July 2025", visitors:2000 },
                ]
            }
        };

        // Helpers
        const nf = (n)=>Number(n).toLocaleString();
        const getDaysInCurrentMonth = ()=>{
            const d = new Date();
            return new Date(d.getFullYear(), d.getMonth()+1, 0).getDate();
        };
        const daysBetweenInclusive = (start, end)=>{
            const s = new Date(start), e = new Date(end);
            return Math.floor((e - s)/(1000*60*60*24)) + 1;
        };

        // Update Table
        function updateTable(filter) {
            const tbody = document.getElementById("visitorsTable");
            tbody.innerHTML = "";
            reportData[filter].table.forEach((row, i)=>{
                const tr = document.createElement("tr");
                tr.className = i % 2 === 1 ? "bg-gray-50" : "";
                tr.innerHTML = `<td class="p-3">${row.date}</td><td class="p-3">${nf(row.visitors)}</td>`;
                tbody.appendChild(tr);
            });
        }

        // Update Stat Cards
        function updateStats(filter, opts = {}) {
            const values = reportData[filter].values;
            const total = values.reduce((a,b)=>a+b,0);

            let days;
            if (filter === 'today') days = 1;
            else if (filter === 'week') days = 7;
            else if (filter === 'month') days = getDaysInCurrentMonth();
            else if (filter === 'custom') days = opts.days ?? 1;

            const avg = Math.round(total / Math.max(days,1));
            const peak = Math.max(...values);
            const peakLabel = reportData[filter].labels[values.indexOf(peak)];

            document.getElementById('totalVisitors').textContent = nf(total);
            document.getElementById('avgDaily').textContent = `${nf(avg)}/day`;
            document.getElementById('peakDay').textContent = `${peakLabel} (${nf(peak)} visitors)`;
        }

        // Update Dashboard (chart + table + stat cards + "Visitors Today")
        function updateDashboard(filter, opts = {}) {
            visitorChart.data.labels = reportData[filter].labels;
            visitorChart.data.datasets[0].data = reportData[filter].values;
            visitorChart.update();

            const lastVal = reportData[filter].values.slice(-1)[0] ?? 0;
            document.getElementById('visitorsToday').textContent = nf(lastVal);

            updateTable(filter);

            const summaryCards = document.getElementById("summaryCards");
            if (filter === "today") {
                summaryCards.classList.add("hidden");
            } else {
                summaryCards.classList.remove("hidden");
                updateStats(filter, opts);
            }
        }

        // Filter Buttons
        const filterButtons = document.querySelectorAll('#filterButtons button');
        const customRangePicker = document.getElementById("customRangePicker");
        const downloadWrapper = document.getElementById("downloadWrapper");
        const downloadReport = document.getElementById("downloadReport");

        let selectedStart = null;
        let selectedEnd = null;

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Reset button styles
                filterButtons.forEach(btn => btn.classList.remove('bg-blue-600','text-white'));
                filterButtons.forEach(btn => btn.classList.add('bg-white','text-gray-700','border'));

                // Active button
                button.classList.remove('bg-white','text-gray-700','border');
                button.classList.add('bg-blue-600','text-white');

                const filter = button.dataset.filter;

                if (filter === "custom") {
                    customRangePicker.classList.remove("hidden");
                    downloadWrapper.classList.add("hidden"); // hidden until Apply
                } else {
                    customRangePicker.classList.add("hidden");
                    downloadWrapper.classList.add("hidden"); // never for non-custom
                    updateDashboard(filter);
                }
            });
        });

        // Apply Custom Range
        document.getElementById("applyCustomRange").addEventListener("click", () => {
            selectedStart = document.getElementById("startDate").value;
            selectedEnd = document.getElementById("endDate").value;

            if (!selectedStart || !selectedEnd) {
                alert("Please select both start and end dates");
                return;
            }

            const days = daysBetweenInclusive(selectedStart, selectedEnd);

            // TODO: Replace with backend fetch to load real data for the chosen range.
            updateDashboard("custom", { days });

            // Show download button when custom is applied
            downloadWrapper.classList.remove("hidden");
        });

        // Download Report (only after custom Apply)
        downloadReport.addEventListener("click", () => {
            if (!selectedStart || !selectedEnd) {
                alert("Please apply a custom date range first!");
                return;
            }
            // Hook to backend export route:
            window.location.href = `/reports/export?start=${encodeURIComponent(selectedStart)}&end=${encodeURIComponent(selectedEnd)}`;
        });

        // Default load
        updateDashboard("today");
    </script>
@endsection
