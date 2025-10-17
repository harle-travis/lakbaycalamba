@extends('layouts.admin')
@section('title', 'Reports')

@section('content')
<div class="printable-content no-print">
    <div class="print-header">
        <h1>Lakbay Calamba - Visitor Reports</h1>
        <p>Generated on: <span id="printDateTime"></span></p>
    </div>

    <div class="print-summary" id="printSummaryCards">
        <div class="print-summary-item">
            <div class="text-xs text-gray-500 mb-1">Total Visitors</div>
            <div id="printTotalVisitors" class="text-3xl font-bold">0</div>
        </div>
        <div class="print-summary-item">
            <div class="text-xs text-gray-500 mb-1">Avg Daily Visitors</div>
            <div id="printAvgDaily" class="text-3xl font-bold">0/day</div>
        </div>
        <div class="print-summary-item">
            <div class="text-xs text-gray-500 mb-1">Peak Day</div>
            <div id="printPeakDay" class="text-2xl font-bold">—</div>
        </div>
    </div>

    <div class="print-chart">
        <h3>Visitor Trends</h3>
        <canvas id="printVisitorChart" width="800" height="400"></canvas>
    </div>

    <table class="print-table" id="printVisitorsTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Visitors</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Top right clock -->
<div class="flex justify-end mb-6 no-print">
    <span id="datetime" class="text-gray-600"></span>
</div>

<!-- Filter Buttons -->
<div class="flex space-x-2 mb-4 no-print" id="filterButtons">
    <button data-filter="today" class="px-4 py-2 bg-blue-600 text-white rounded">Today</button>
    <button data-filter="week" class="px-4 py-2 bg-white border rounded">This Week</button>
    <button data-filter="month" class="px-4 py-2 bg-white border rounded">This Month</button>
    <button data-filter="custom" class="px-4 py-2 bg-white border rounded">Custom Range</button>
</div>

<!-- Custom Date Range -->
<div id="customRangePicker" class="hidden mb-6 no-print">
    <label class="block text-gray-700 font-medium">Select Date Range:</label>
    <div class="flex flex-wrap gap-3 mt-2">
        <input type="date" id="startDate" class="border p-2 rounded">
        <input type="date" id="endDate" class="border p-2 rounded">
        <button id="applyCustomRange" class="px-4 py-2 bg-blue-600 text-white rounded">Apply</button>
    </div>
</div>

<!-- Chart -->
<div class="bg-white p-6 shadow rounded-lg mb-6 no-print">
    <h3 class="text-sm font-semibold text-gray-700 border-b pb-2 mb-4">Visitor Trends</h3>
    <canvas id="visitorChart" class="h-64"></canvas>
</div>

<!-- Table -->
<div class="bg-white shadow rounded-lg mb-6 no-print">
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

<!-- Action Buttons -->
<div id="downloadWrapper" class="flex justify-center gap-4 mt-6 hidden">
    <button id="makePdf" class="px-4 py-2 bg-green-600 text-white rounded">Make PDF</button>
</div>

<!-- Libraries -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<!-- CSRF -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
@media print {
    body * { visibility: hidden; }
    .printable-content, .printable-content * { visibility: visible; }
    .printable-content { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
}
button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const ctx = document.getElementById('visitorChart').getContext('2d');
    const visitorChart = new Chart(ctx, {
        type: 'line',
        data: { labels: [], datasets: [{ label: 'Visitors', data: [], borderColor: 'rgb(37,99,235)', backgroundColor: 'rgba(37,99,235,0.2)', fill: true, tension: 0.4 }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    function updateDateTime() {
        const now = new Date();
        const options = { year:'numeric', month:'long', day:'numeric', hour:'numeric', minute:'numeric', hour12:true };
        document.getElementById('datetime').textContent = now.toLocaleDateString('en-US', options);
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    // Fetch reports
    async function loadReport(filter, start = null, end = null) {
        try {
            let url = `/admin/reports/data?filter=${filter}`;
            if (start && end) url += `&start=${start}&end=${end}`;
            const res = await fetch(url, {
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (!data.success) return;

            // Chart
            visitorChart.data.labels = data.labels;
            visitorChart.data.datasets[0].data = data.values;
            visitorChart.update();

            // Table
            const tbody = document.getElementById('visitorsTable');
            tbody.innerHTML = '';
            data.labels.forEach((date, i) => {
                tbody.innerHTML += `<tr class="${i%2?'bg-gray-50':''}">
                    <td class="p-3">${date}</td><td class="p-3">${data.values[i]}</td>
                </tr>`;
            });

            document.getElementById('downloadWrapper').classList.remove('hidden');
        } catch (e) {
            console.error('Error loading report:', e);
        }
    }

    // Filters
    document.querySelectorAll('#filterButtons button').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('#filterButtons button')
                .forEach(b => b.classList.remove('bg-blue-600','text-white'));
            btn.classList.add('bg-blue-600','text-white');

            const f = btn.dataset.filter;
            if (f === 'custom') {
                document.getElementById('customRangePicker').classList.remove('hidden');
            } else {
                document.getElementById('customRangePicker').classList.add('hidden');
                loadReport(f);
            }
        });
    });

    document.getElementById('applyCustomRange').addEventListener('click', () => {
        const s = document.getElementById('startDate').value;
        const e = document.getElementById('endDate').value;
        if (!s || !e) return alert('Select both start and end dates');
        loadReport('custom', s, e);
    });

    // PDF
    document.getElementById('makePdf').addEventListener('click', () => {
        const element = document.querySelector('.printable-content');
        const opt = {
            margin: 1,
            filename: `Visitor_Report_${new Date().toISOString().slice(0,10)}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    });

    // Load default
    loadReport('today');
});
</script>
@endsection
