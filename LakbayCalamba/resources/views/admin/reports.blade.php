@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
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
                        @foreach ($reports as $index => $report)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $report->visitor_name }}</td>
                            <td>{{ $report->purpose }}</td>
                            <td>{{ $report->establishment }}</td>
                            <td>{{ $report->date }}</td>
                            <td>{{ $report->time_in }}</td>
                            <td>{{ $report->time_out }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- jsPDF for PDF export --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.1/jspdf.plugin.autotable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const { jsPDF } = window.jspdf;

    document.getElementById('makePdfBtn').addEventListener('click', () => {
        const doc = new jsPDF();

        // Title
        doc.setFontSize(16);
        doc.text('Visitor Reports', 14, 15);
        doc.setFontSize(10);
        doc.text('Generated on: ' + new Date().toLocaleString(), 14, 22);

        // Table
        const table = document.getElementById('reportTable');
        doc.autoTable({ html: table, startY: 28 });

        // Save the PDF
        doc.save('Visitor_Reports.pdf');
    });
});
</script>
@endsection
