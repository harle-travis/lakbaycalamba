@extends('layouts.admin')
@section('title', 'Reports')
@section('content')

<!-- Printable content wrapper -->
<div class="printable-content">
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

<!-- Main content -->
<div class="relative z-10"> <!-- ✅ Added wrapper with z-index -->
    <div class="flex justify-end mb-6">
        <span id="datetime" class="text-gray-600"></span>
    </div>

    <div class="flex space-x-2 mb-4" id="filterButtons">
        <button data-filter="today" class="px-4 py-2 bg-blue-600 text-white rounded">Today</button>
        <button data-filter="week" class="px-4 py-2 bg-white border rounded">This Week</button>
        <button data-filter="month" class="px-4 py-2 bg-white border rounded">This Month</button>
        <button data-filter="custom" class="px-4 py-2 bg-white border rounded">Custom Range</button>
    </div>

    <div id="summaryCards" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 hidden">
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

    <div id="customRangePicker" class="hidden mb-6">
        <label class="block text-gray-700 font-medium">Select Date Range:</label>
        <div class="flex flex-wrap gap-3 mt-2">
            <input type="date" id="startDate" class="border p-2 rounded">
            <input type="date" id="endDate" class="border p-2 rounded">
            <button id="applyCustomRange" class="px-4 py-2 bg-blue-600 text-white rounded">Apply</button>
        </div>
    </div>

    <div class="mb-4">
        <p class="text-lg font-medium text-gray-700">
            Visitors Today : <span id="visitorsToday" class="text-2xl font-bold">0</span>
        </p>
    </div>

    <div class="bg-white p-6 shadow rounded-lg mb-6">
        <h3 class="text-sm font-semibold text-gray-700 border-b pb-2 mb-4">Visitor Trends</h3>
        <div style="height: 16rem"><canvas id="visitorChart" class="h-64"></canvas></div>
    </div>

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

    <div id="downloadWrapper" class="flex justify-center gap-4 mt-6 hidden">
        <button id="downloadReport" class="px-4 py-2 bg-blue-600 text-white rounded">Download CSV</button>
        <button id="exportPdf" class="px-4 py-2 bg-green-600 text-white rounded">Export PDF</button>
        <button id="printReport" class="px-4 py-2 bg-purple-600 text-white rounded">Print Report</button>
    </div>
</div>

<!-- JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
/* ✅ Fix click issues */
.printable-content {
    position: absolute;
    top: 0;
    left: 0;
    z-index: -1; /* <-- puts it behind buttons */
    opacity: 0;
    pointer-events: none;
}

@media print {
    body * {
        visibility: hidden;
    }
    .printable-content, .printable-content * {
        visibility: visible;
        opacity: 1;
        pointer-events: auto;
        z-index: 9999;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
}
</style>

<script>
function updateDateTime() {
    const now = new Date();
    document.getElementById('datetime').textContent = now.toLocaleString();
    document.getElementById('printDateTime').textContent = now.toLocaleString();
}
setInterval(updateDateTime, 1000);
updateDateTime();

// ✅ Chart
const ctx = document.getElementById('visitorChart').getContext('2d');
let visitorChart = new Chart(ctx, {
    type: 'line',
    data: { labels: [], datasets: [{ label: 'Visitors', data: [], borderColor: 'blue', backgroundColor: 'rgba(37,99,235,0.2)', fill: true }] },
    options: { responsive: true, plugins: { legend: { display: false } } }
});

// ✅ Example data load simulation
async function loadReport(start, end) {
    // Fake data example; replace with your actual fetch later
    const daily = [
        { date: start, total: Math.floor(Math.random() * 50) + 10 },
        { date: end, total: Math.floor(Math.random() * 50) + 10 },
    ];
    const labels = daily.map(d => d.date);
    const totals = daily.map(d => d.total);
    visitorChart.data.labels = labels;
    visitorChart.data.datasets[0].data = totals;
    visitorChart.update();
}

// ✅ Filters
const filterButtons = document.querySelectorAll('#filterButtons button');
const customRangePicker = document.getElementById('customRangePicker');
filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        filterButtons.forEach(b => b.classList.remove('bg-blue-600','text-white'));
        btn.classList.add('bg-blue-600','text-white');
        if (btn.dataset.filter === 'custom') {
            customRangePicker.classList.remove('hidden');
        } else {
            customRangePicker.classList.add('hidden');
            const today = new Date().toISOString().slice(0,10);
            loadReport(today, today);
        }
    });
});

// ✅ Export PDF
document.getElementById('exportPdf').addEventListener('click', () => {
    const el = document.querySelector('.printable-content');
    el.style.opacity = 1;
    el.style.pointerEvents = 'auto';
    const opt = {
        margin: 1,
        filename: `Lakbay-Calamba-Report_${new Date().toISOString().slice(0,10)}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(el).save().then(() => {
        el.style.opacity = 0;
        el.style.pointerEvents = 'none';
    });
});

// ✅ Print
document.getElementById('printReport').addEventListener('click', () => {
    window.print();
});

// Load today's report on start
const today = new Date().toISOString().slice(0,10);
loadReport(today, today);
</script>

@endsection
