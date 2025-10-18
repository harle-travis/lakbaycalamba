@extends('layouts.admin')
@section('title', 'Reports')
@section('content')

<style>
/* ✅ Fix click issues and layout */
.no-print {
    position: relative;
    z-index: 10;
}

.printable-content {
    position: relative;
    z-index: 1;
    pointer-events: none; /* prevent blocking clicks */
}

.printable-content canvas,
.printable-content * {
    pointer-events: none;
}

@media print {
    .no-print {
        display: none !important;
    }
}
</style>

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

<div class="flex justify-end mb-6 no-print">
    <span id="datetime" class="text-gray-600"></span>
</div>

<div class="flex space-x-2 mb-4 no-print" id="filterButtons">
    <button data-filter="today" class="px-4 py-2 bg-blue-600 text-white rounded">Today</button>
    <button data-filter="week" class="px-4 py-2 bg-white border rounded">This Week</button>
    <button data-filter="month" class="px-4 py-2 bg-white border rounded">This Month</button>
    <button data-filter="custom" class="px-4 py-2 bg-white border rounded">Custom Range</button>
</div>

<div id="summaryCards" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 hidden no-print">
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

<div id="customRangePicker" class="hidden mb-6 no-print">
    <label class="block text-gray-700 font-medium">Select Date Range:</label>
    <div class="flex flex-wrap gap-3 mt-2">
        <input type="date" id="startDate" class="border p-2 rounded">
        <input type="date" id="endDate" class="border p-2 rounded">
        <button id="applyCustomRange" class="px-4 py-2 bg-blue-600 text-white rounded">Apply</button>
    </div>
</div>

<div class="mb-4 no-print">
    <p class="text-lg font-medium text-gray-700">Visitors Today : 
        <span id="visitorsToday" class="text-2xl font-bold">0</span>
    </p>
</div>

<div class="bg-white p-6 shadow rounded-lg mb-6 no-print">
    <h3 class="text-sm font-semibold text-gray-700 border-b pb-2 mb-4">Visitor Trends</h3>
    <div style="height: 16rem">
        <canvas id="visitorChart" class="h-64"></canvas>
    </div>
</div>

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

<div id="downloadWrapper" class="flex justify-center gap-4 mt-6 hidden no-print">
    <button id="downloadReport" class="px-4 py-2 bg-blue-600 text-white rounded">Download CSV</button>
    <button id="exportPdf" class="px-4 py-2 bg-green-600 text-white rounded">Make PDF</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const nf = (n) => Number(n || 0).toLocaleString();

    function updateDateTime() {
        const now = new Date();
        document.getElementById('datetime').textContent = now.toLocaleString();
        document.getElementById('printDateTime').textContent = now.toLocaleString();
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    const ctx = document.getElementById('visitorChart').getContext('2d');
    let visitorChart = new Chart(ctx, {
        type: 'line',
        data: { labels: [], datasets: [{ label: 'Visitors', data: [], borderColor: 'blue', backgroundColor: 'rgba(37,99,235,0.2)', fill: true }] },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    function updateTable(daily) {
        const tbody = document.getElementById('visitorsTable');
        const printBody = document.querySelector('#printVisitorsTable tbody');
        tbody.innerHTML = '';
        printBody.innerHTML = '';

        daily.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td class="p-3">${row.date}</td><td class="p-3">${nf(row.total)}</td>`;
            tbody.appendChild(tr);

            const printTr = document.createElement('tr');
            printTr.innerHTML = `<td>${row.date}</td><td>${nf(row.total)}</td>`;
            printBody.appendChild(printTr);
        });
    }

    async function loadReport(start, end) {
        const res = await fetch(`/admin/reports/data?start_date=${start}&end_date=${end}`);
        const json = await res.json();
        if (!json.success) return;

        const labels = json.daily.map(d => d.date);
        const totals = json.daily.map(d => d.total);

        // Update main chart
        visitorChart.data.labels = labels;
        visitorChart.data.datasets[0].data = totals;
        visitorChart.update();

        // Update print chart
        const printCtx = document.getElementById('printVisitorChart').getContext('2d');
        new Chart(printCtx, {
            type: 'line',
            data: { labels, datasets: [{ label: 'Visitors', data: totals, borderColor: 'blue', backgroundColor: 'rgba(37,99,235,0.2)', fill: true }] },
            options: { plugins: { legend: { display: false } }, responsive: false }
        });

        updateTable(json.daily);
        document.getElementById('downloadWrapper').classList.remove('hidden');
    }

    // ✅ Download CSV
    document.getElementById('downloadReport').addEventListener('click', async () => {
        const res = await fetch(`/admin/reports/data`);
        const json = await res.json();
        if (!json.success) return alert('Failed to fetch data.');

        const rows = [['Date', 'Visitors']];
        json.daily.forEach(r => rows.push([r.date, r.total]));
        const csv = rows.map(r => r.join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `Lakbay-Calamba-Report_${new Date().toISOString().slice(0,10)}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    // ✅ Export PDF
    document.getElementById('exportPdf').addEventListener('click', () => {
        const element = document.querySelector('.printable-content');
        element.style.pointerEvents = 'auto';
        const opt = {
            margin: 0.5,
            filename: `Lakbay-Calamba-Report_${new Date().toISOString().slice(0,10)}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save().then(() => {
            element.style.pointerEvents = 'none';
        });
    });

    // ✅ Default load today
    const today = new Date().toISOString().slice(0,10);
    loadReport(today, today);
});
</script>
@endsection
