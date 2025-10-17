@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
    <!-- Printable content wrapper -->
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
        <p class="text-lg font-medium text-gray-700">Visitors Today : <span id="visitorsToday" class="text-2xl font-bold">0</span></p>
    </div>

    <div class="bg-white p-6 shadow rounded-lg mb-6 no-print">
        <h3 class="text-sm font-semibold text-gray-700 border-b pb-2 mb-4">Visitor Trends</h3>
        <div style="height: 16rem"><canvas id="visitorChart" class="h-64"></canvas></div>
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

    <div id="downloadWrapper" class="flex justify-center gap-4 mt-6 hidden">
        <button id="downloadReport" class="px-4 py-2 bg-blue-600 text-white rounded">Download CSV</button>
        <button id="exportPdf" class="px-4 py-2 bg-green-600 text-white rounded">Export PDF</button>
        <button id="printReport" class="px-4 py-2 bg-purple-600 text-white rounded">Print Report</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .printable-content, .printable-content * {
                visibility: visible;
            }
            .printable-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
            .print-header {
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }
            .print-summary {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                margin-bottom: 20px;
            }
            .print-summary-item {
                text-align: center;
                padding: 15px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
            .print-chart {
                margin: 20px 0;
                text-align: center;
            }
            .print-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            .print-table th,
            .print-table td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            .print-table th {
                background-color: #f5f5f5;
                font-weight: bold;
            }
        }
    </style>
    
    <script>
    function updateDateTime() {
        const now = new Date();
        const options = { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true };
        document.getElementById('datetime').textContent = now.toLocaleDateString('en-US', options);
    }
    setInterval(updateDateTime, 1000); updateDateTime();

    const ctx = document.getElementById('visitorChart').getContext('2d');
    let visitorChart = new Chart(ctx, {
        type: 'line',
        data: { labels: [], datasets: [{ label: 'Visitors', data: [], borderColor: 'rgb(37, 99, 235)', backgroundColor: 'rgba(37, 99, 235, 0.2)', fill: true, tension: 0.4, pointBackgroundColor: 'rgb(37, 99, 235)' }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    const nf = (n) => Number(n || 0).toLocaleString();

    function formatDateISO(d) { return d.toISOString().slice(0, 10); }
    function getTodayRange() { const d = new Date(); const s = formatDateISO(d); return { start: s, end: s }; }
    function getWeekRange() {
        const d = new Date(); const day = d.getDay(); const diffToMonday = (day + 6) % 7;
        const monday = new Date(d); monday.setDate(d.getDate() - diffToMonday);
        const sunday = new Date(monday); sunday.setDate(monday.getDate() + 6);
        return { start: formatDateISO(monday), end: formatDateISO(sunday) };
    }
    function getMonthRange() { const d = new Date(); const s = new Date(d.getFullYear(), d.getMonth(), 1); const e = new Date(d.getFullYear(), d.getMonth() + 1, 0); return { start: formatDateISO(s), end: formatDateISO(e) }; }
    function daysBetweenInclusive(start, end) { const s = new Date(start), e = new Date(end); return Math.floor((e - s) / 86400000) + 1; }

    function updateTable(daily) {
        const tbody = document.getElementById('visitorsTable');
        tbody.innerHTML = '';
        daily.slice().reverse().forEach((row, i) => {
            const tr = document.createElement('tr');
            tr.className = i % 2 === 1 ? 'bg-gray-50' : '';
            tr.innerHTML = `<td class="p-3">${row.date}</td><td class="p-3">${nf(row.total)}</td>`;
            tbody.appendChild(tr);
        });
    }

    function updateSummaryCards(daily, show) {
        const summary = document.getElementById('summaryCards');
        if (!show) { summary.classList.add('hidden'); return; }
        const total = daily.reduce((a, d) => a + (d.total || 0), 0);
        const avg = Math.round(total / Math.max(daily.length, 1));
        let peak = 0, peakLabel = '—';
        daily.forEach(d => { if ((d.total || 0) > peak) { peak = d.total; peakLabel = d.date; } });
        document.getElementById('totalVisitors').textContent = nf(total);
        document.getElementById('avgDaily').textContent = `${nf(avg)}/day`;
        document.getElementById('peakDay').textContent = `${peakLabel} (${nf(peak)} visitors)`;
        summary.classList.remove('hidden');
        
        // Update print summary cards
        document.getElementById('printTotalVisitors').textContent = nf(total);
        document.getElementById('printAvgDaily').textContent = `${nf(avg)}/day`;
        document.getElementById('printPeakDay').textContent = `${peakLabel} (${nf(peak)} visitors)`;
    }

    async function loadReport(start, end, showSummary) {
        const qs = `start_date=${encodeURIComponent(start)}&end_date=${encodeURIComponent(end)}`;
        const res = await fetch(`/admin/reports/data?${qs}`, { headers: { 'Accept': 'application/json' } });
        const json = await res.json();
        if (!json.success) return;

        const labels = json.daily.map(d => d.date);
        const totals = json.daily.map(d => d.total);

        visitorChart.data.labels = labels;
        visitorChart.data.datasets[0].data = totals;
        visitorChart.update();

        const today = new Date();
        const todayLabel = today.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
        const todayIndex = labels.findIndex(l => l === todayLabel);
        const todaysTotal = todayIndex >= 0 ? totals[todayIndex] : (totals[totals.length - 1] || 0);
        document.getElementById('visitorsToday').textContent = nf(todaysTotal);

        updateTable(json.daily);
        updateSummaryCards(json.daily, showSummary);

        const downloadWrapper = document.getElementById('downloadWrapper');
        const downloadReport = document.getElementById('downloadReport');
        const exportPdf = document.getElementById('exportPdf');
        const printReport = document.getElementById('printReport');
        
        // ✅ Always show buttons after loading
        downloadWrapper.classList.remove('hidden');

        downloadReport.onclick = () => { window.location.href = `/admin/reports/export?${qs}`; };
        exportPdf.onclick = () => { exportToPDF(); };
        printReport.onclick = () => { printReportPage(); };
    }

    const filterButtons = document.querySelectorAll('#filterButtons button');
    const customRangePicker = document.getElementById('customRangePicker');

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            filterButtons.forEach(btn => btn.classList.remove('bg-blue-600', 'text-white'));
            filterButtons.forEach(btn => btn.classList.add('bg-white', 'text-gray-700', 'border'));
            button.classList.remove('bg-white', 'text-gray-700', 'border');
            button.classList.add('bg-blue-600', 'text-white');

            const filter = button.dataset.filter;
            if (filter === 'custom') {
                customRangePicker.classList.remove('hidden');
                document.getElementById('summaryCards').classList.add('hidden');
            } else {
                customRangePicker.classList.add('hidden');
                if (filter === 'today') {
                    const r = getTodayRange();
                    loadReport(r.start, r.end, false);
                } else if (filter === 'week') {
                    const r = getWeekRange();
                    loadReport(r.start, r.end, true);
                } else if (filter === 'month') {
                    const r = getMonthRange();
                    loadReport(r.start, r.end, true);
                }
            }
        });
    });

    document.getElementById('applyCustomRange').addEventListener('click', () => {
        const start = document.getElementById('startDate').value;
        const end = document.getElementById('endDate').value;
        if (!start || !end) { alert('Please select both start and end dates'); return; }
        const days = daysBetweenInclusive(start, end);
        const showSummary = days > 1;
        loadReport(start, end, showSummary);
    });

    (function initialLoad(){ const r = getTodayRange(); loadReport(r.start, r.end, false); })();

    // ✅ Clone chart as image for printable version
    function cloneChartForPrint() {
        const existingCanvas = document.getElementById('visitorChart');
        const img = document.createElement('img');
        img.src = existingCanvas.toDataURL('image/png');
        img.alt = 'Visitor Trends Chart';
        img.style.width = '100%';
        img.style.marginTop = '20px';

        const printableContent = document.querySelector('.printable-content');
        const oldImg = printableContent.querySelector('img[alt="Visitor Trends Chart"]');
        if (oldImg) oldImg.remove(); // remove old one if exists
        printableContent.appendChild(img);
    }

    // ✅ PDF Export Function
    function exportToPDF() {
        const now = new Date();
        const options = { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true };
        document.getElementById('printDateTime').textContent = now.toLocaleDateString('en-US', options);
        updatePrintTable();
        cloneChartForPrint(); // use same chart image

        const element = document.querySelector('.printable-content');
        const opt = {
            margin: 1,
            filename: `lakbay-calamba-report-${new Date().toISOString().slice(0, 10)}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }

    // ✅ Print Function
    function printReportPage() {
        const now = new Date();
        const options = { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true };
        document.getElementById('printDateTime').textContent = now.toLocaleDateString('en-US', options);
        updatePrintTable();
        cloneChartForPrint(); // reuse same chart
        window.print();
    }

    // ✅ Update print table
    function updatePrintTable() {
        const tbody = document.querySelector('#printVisitorsTable tbody');
        const originalTbody = document.getElementById('visitorsTable');
        tbody.innerHTML = originalTbody.innerHTML;
    }
</script>

@endsection


