@extends('layouts.admin')
@section('title', 'Reports')
@section('content')

@php
    // make sure $reports exists to avoid "Undefined variable" error
    $reports = isset($reports) ? $reports : collect();
@endphp

<div class="container-fluid mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Reports</h5>
            <button id="makePdfBtn" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Make PDF
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="reportTable" class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>#</th>
                            <th>Visitor Name</th>
                            <th>Purpose</th>
                            <th>Establishment</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $index => $report)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $report->visitor_name ?? '-' }}</td>
                            <td>{{ $report->purpose ?? '-' }}</td>
                            <td>{{ $report->establishment ?? '-' }}</td>
                            <td>{{ $report->date ?? '-' }}</td>
                            <td>{{ $report->time_in ?? '-' }}</td>
                            <td>{{ $report->time_out ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No reports found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- jsPDF + AutoTable --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.1/jspdf.plugin.autotable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const { jsPDF } = window.jspdf;

    document.getElementById('makePdfBtn').addEventListener('click', () => {
        const doc = new jsPDF('p', 'pt', 'letter');

        // Title
        doc.setFontSize(16);
        doc.text('Visitor Reports', 40, 40);
        doc.setFontSize(10);
        doc.text('Generated on: ' + new Date().toLocaleString(), 40, 58);

        // Build columns & rows from the table in DOM (keeps it simple)
        const table = document.getElementById('reportTable');

        // If table has no rows (empty state) show message and save
        const tbody = table.querySelector('tbody');
        if (!tbody || tbody.rows.length === 0 || (tbody.rows.length === 1 && tbody.rows[0].cells.length === 1)) {
            doc.setFontSize(12);
            doc.text('No reports to export.', 40, 100);
            const filenameEmpty = `Visitor_Reports_${new Date().toISOString().slice(0,10)}.pdf`;
            doc.save(filenameEmpty);
            return;
        }

        // Use autoTable with html source
        doc.autoTable({ html: '#reportTable', startY: 80, styles: { fontSize: 10 } });

        // Save with date in filename
        const filename = `Visitor_Reports_${new Date().toISOString().slice(0,10)}.pdf`;
        doc.save(filename);
    });
});
</script>

@endsection
