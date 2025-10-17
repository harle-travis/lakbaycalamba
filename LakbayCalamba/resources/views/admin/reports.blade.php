@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Visitor Reports</h2>

    <!-- Chart -->
    <canvas id="visitorChart" class="mb-6" height="120"></canvas>

    <!-- Filter Buttons -->
    <div class="mb-4 flex gap-2">
        <button id="todayBtn" class="bg-blue-600 text-white px-4 py-2 rounded">Today</button>
        <button id="weekBtn" class="border px-4 py-2 rounded">This Week</button>
        <button id="monthBtn" class="border px-4 py-2 rounded">This Month</button>
        <button id="customRangeBtn" class="border px-4 py-2 rounded">Custom Range</button>
    </div>

    <!-- Summary -->
    <div id="summary" class="mt-4 text-lg font-semibold">
        Visitors Today : <span id="todayVisitors">0</span>
    </div>

    <!-- Download & Print Buttons -->
    <div id="downloadWrapper" class="mt-4 flex gap-2">
        <button id="exportPdf" class="bg-green-600 text-white px-4 py-2 rounded">Export PDF</button>
        <button id="printReport" class="bg-gray-800 text-white px-4 py-2 rounded">Print Report</button>
    </div>

    <!-- Hidden Printable Section -->
    <div class="printable-content hidden">
        <h2 class="text-xl font-bold mb-2">Visitor Report</h2>
        <p id="printDateRange" class="mb-4"></p>
        <img id="printChartImage" class="w-full mb-4" alt="Visitor Chart">
        <table id="printTable" class="w-full border-collapse border">
            <thead>
                <tr class="bg-gray-100 border">
                    <th class="border px-4 py-2">Date</th>
                    <th class="border px-4 py-2">Visitors</th>
                </tr>
            </thead>
            <tbody id="printTableBody"></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // --- Chart Setup ---
    const ctx = document.getElementById('visitorChart').getContext('2d');
    const visitorChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Oct 17, 2025'],
            datasets: [{
                label: 'Visitors',
                data: [1],
                borderColor: '#2563EB',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } }
        }
    });

    const todayVisitors = document.getElementById("todayVisitors");
    const printableContent = document.querySelector(".printable-content");
    const printTableBody = document.getElementById("printTableBody");
    const printChartImage = document.getElementById("printChartImage");
    const printDateRange = document.getElementById("printDateRange");

    // --- Load Report Example ---
    function loadReport(period) {
        // Your AJAX or database-fetching logic goes here
        todayVisitors.textContent = 1; 
        document.getElementById("downloadWrapper").classList.remove("hidden");
    }

    // --- Filter Button Events ---
    document.getElementById("todayBtn").addEventListener("click", () => loadReport("today"));
    document.getElementById("weekBtn").addEventListener("click", () => loadReport("week"));
    document.getElementById("monthBtn").addEventListener("click", () => loadReport("month"));
    document.getElementById("customRangeBtn").addEventListener("click", () => loadReport("custom"));

    // --- Print Report ---
    document.getElementById("printReport").addEventListener("click", () => {
        updatePrintableContent();
        setTimeout(() => window.print(), 400);
    });

    // --- Export to PDF ---
    document.getElementById("exportPdf").addEventListener("click", () => {
        updatePrintableContent();
        const element = document.querySelector(".printable-content");
        const opt = {
            margin: 0.5,
            filename: 'Visitor_Report.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    });

    // --- Update Printable Section ---
    function updatePrintableContent() {
        const chartCanvas = document.getElementById("visitorChart");
        printChartImage.src = chartCanvas.toDataURL("image/png");
        printDateRange.textContent = "Date Range: Oct 17, 2025";
        printTableBody.innerHTML = `
            <tr>
                <td class="border px-4 py-2">Oct 17, 2025</td>
                <td class="border px-4 py-2 text-center">1</td>
            </tr>
        `;
    }

    // --- Hide Printable Section in Normal View ---
    const css = `
        @media screen {
            .printable-content { display: none !important; }
        }
        @media print {
            body * { visibility: hidden; }
            .printable-content, .printable-content * { visibility: visible; }
            .printable-content { position: absolute; left: 0; top: 0; width: 100%; padding: 20px; }
        }
    `;
    const style = document.createElement('style');
    style.appendChild(document.createTextNode(css));
    document.head.appendChild(style);
});
</script>
@endpush
