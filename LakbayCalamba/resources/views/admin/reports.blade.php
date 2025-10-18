@extends('layouts.admin')

@section('title', 'Reports')

@section('styles')
<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Poppins', sans-serif;
    }

    .printable-content {
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .print-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .print-header h1 {
        font-size: 28px;
        font-weight: 600;
    }

    .print-header p {
        font-size: 14px;
        color: #555;
    }

    .filter-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .filter-buttons button {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.3s;
    }

    .filter-buttons button:hover {
        background-color: #0056b3;
    }

    .print-summary {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .print-summary-item {
        flex: 1 1 30%;
        background: #f1f3f5;
        border-radius: 8px;
        padding: 15px;
        margin: 10px;
        text-align: center;
    }

    .print-summary-item h3 {
        font-size: 18px;
        margin-bottom: 5px;
    }

    .print-summary-item p {
        font-size: 16px;
        font-weight: 500;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        padding: 10px;
        border: 1px solid #dee2e6;
        text-align: center;
    }

    th {
        background-color: #e9ecef;
        font-weight: 600;
    }

    .no-print {
        display: block;
    }

    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="printable-content no-print">

    <!-- Header -->
    <div class="print-header">
        <h1>Lakbay Calamba - Visitor Reports</h1>
        <p>Generated on: <span id="printDateTime"></span></p>
    </div>

    <!-- Summary Cards -->
    <div class="print-summary" id="printSummaryCards">
        <div class="print-summary-item">
            <h3>Total Visitors</h3>
            <p id="totalVisitors">0</p>
        </div>
        <div class="print-summary-item">
            <h3>Local Visitors</h3>
            <p id="localVisitors">0</p>
        </div>
        <div class="print-summary-item">
            <h3>Foreign Visitors</h3>
            <p id="foreignVisitors">0</p>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="filter-buttons">
        <button id="filterAll">All</button>
        <button id="filterToday">Today</button>
        <button id="filterWeek">This Week</button>
        <button id="filterMonth">This Month</button>
        <button id="filterYear">This Year</button>
        <button id="filterCustom">Custom</button>
    </div>

    <!-- Table -->
    <table id="visitorTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Visitor Name</th>
                <th>Origin</th>
                <th>Date</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data dynamically inserted here -->
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
    // Print Date
    document.getElementById('printDateTime').textContent = new Date().toLocaleString();

    // Example filter buttons functionality
    const filterButtons = document.querySelectorAll('.filter-buttons button');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            console.log('Filter:', btn.id);
        });
    });
</script>
@endsection
