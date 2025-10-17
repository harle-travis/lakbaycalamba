@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-xl sm:text-2xl font-bold mb-4">QR Codes for Establishments</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @foreach($establishments as $establishment)
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-200">
                <h5 class="font-semibold text-gray-800">{{ $establishment->establishment_name }}</h5>
            </div>
            <div class="p-4 text-center">
                @if($establishment->qr_code)
                    <div class="qr-code-container inline-block">
                        {!! $establishment->qr_code !!}
                    </div>
                    <p class="mt-2 text-sm text-gray-600">Scan this QR code to collect a stamp!</p>
                    <button class="mt-3 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded" onclick="testStamp({{ $establishment->id }})">
                        Test Stamp Collection
                    </button>
                @else
                    <p class="text-sm text-gray-600">No QR code generated yet.</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Your Stamps</h3>
        <div id="user-stamps">
            <button class="bg-teal-600 hover:bg-teal-700 text-white text-sm px-4 py-2 rounded" onclick="loadUserStamps()">Load My Stamps</button>
        </div>
    </div>
</div>

<script>
function testStamp(establishmentId) {
    fetch(`/stamp/process/${establishmentId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadUserStamps(); // Refresh stamps
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing the stamp.');
    });
}

function loadUserStamps() {
    fetch('/stamps')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let html = `<h4>Total Stamps: ${data.total_stamps}</h4>`;
            if (data.stamps.length > 0) {
                html += '<div class="row">';
                data.stamps.forEach(stamp => {
                    html += `
                        <div class="col-md-4 mb-2">
                            <div class="card">
                                <div class="card-body">
                                    <h6>${stamp.establishment.establishment_name}</h6>
                                    <small>Visited: ${new Date(stamp.visit_date).toLocaleDateString()}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            } else {
                html += '<p>No stamps collected yet.</p>';
            }
            document.getElementById('user-stamps').innerHTML = html;
        } else {
            alert('Error loading stamps: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while loading stamps.');
    });
}
</script>
@endsection
