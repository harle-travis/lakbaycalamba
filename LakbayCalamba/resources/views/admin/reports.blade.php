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

<!-- Live Controls -->
<div class="flex justify-end mb-6 no-print">
    <span id="datetime" class="text-gray-600"></span>
</div>

<div class="flex space-x-2 mb-4 no-print" id="filterButtons">
    <button data-filter="today" class="px-4 py-2 bg-blue-600 text-white rounded">Today</button>
    <button data-filter="week" class="px-4 py-2 bg-white border rounded">This Week</button>
    <button data-filter="month" class="px-4 py-2 bg-white border rounded">This Month</button>
    <button data-filter="custom" class="px-4 py-2 bg-white border rounded">Custom Range</button>
</div>

<div id="customRangePicker" class="hidden mb-6 no-print">
    <label class="block text-gray-700 font-medium">Select Date Range:</label>
    <div class="flex flex-wrap gap-3 mt-2">
        <input type="date" id="startDate" class="border p-2 rounded">
        <input type="date" id="endDate" class="border p-2 rounded">
        <button id="applyCustomRange" class="px-4 py-2 bg-blue-600 text-white rounded">Apply</button>
    </div>
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
    <button id="printReport" class="px-4 py-2 bg-purple-600 text-white rounded">Print Report</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
}
</style>

<script>
function updateDateTime(){
    const now=new Date();
    document.getElementById('datetime').textContent=now.toLocaleString('en-US');
}
setInterval(updateDateTime,1000);
updateDateTime();

const ctx=document.getElementById('visitorChart').getContext('2d');
let visitorChart=new Chart(ctx,{
    type:'line',
    data:{labels:[],datasets:[{label:'Visitors',data:[],borderColor:'rgb(37,99,235)',fill:true}]},
    options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});

function nf(n){return Number(n||0).toLocaleString();}
function formatDateISO(d){return d.toISOString().slice(0,10);}
function getTodayRange(){const d=new Date();const s=formatDateISO(d);return{start:s,end:s};}
function getWeekRange(){const d=new Date();const day=d.getDay();const diff=(day+6)%7;const monday=new Date(d);monday.setDate(d.getDate()-diff);const sunday=new Date(monday);sunday.setDate(monday.getDate()+6);return{start:formatDateISO(monday),end:formatDateISO(sunday)};}
function getMonthRange(){const d=new Date();const s=new Date(d.getFullYear(),d.getMonth(),1);const e=new Date(d.getFullYear(),d.getMonth()+1,0);return{start:formatDateISO(s),end:formatDateISO(e)};}

function updateTable(daily){
    const tbody=document.getElementById('visitorsTable');
    tbody.innerHTML='';
    daily.slice().reverse().forEach((row,i)=>{
        const tr=document.createElement('tr');
        tr.className=i%2?'bg-gray-50':'';
        tr.innerHTML=`<td class="p-3">${row.date}</td><td class="p-3">${nf(row.total)}</td>`;
        tbody.appendChild(tr);
    });
}

function updateSummaryCards(daily){
    const total=daily.reduce((a,d)=>a+(d.total||0),0);
    const avg=Math.round(total/Math.max(daily.length,1));
    let peak=0,peakLabel='—';
    daily.forEach(d=>{if((d.total||0)>peak){peak=d.total;peakLabel=d.date;}});
    document.getElementById('totalVisitors').textContent=nf(total);
    document.getElementById('avgDaily').textContent=`${nf(avg)}/day`;
    document.getElementById('peakDay').textContent=`${peakLabel} (${nf(peak)} visitors)`;
    document.getElementById('summaryCards').classList.remove('hidden');
    document.getElementById('downloadWrapper').classList.remove('hidden');
}

async function loadReport(start,end){
    const res=await fetch(`/admin/reports/data?start_date=${start}&end_date=${end}`);
    const json=await res.json();
    if(!json.success)return;
    visitorChart.data.labels=json.daily.map(d=>d.date);
    visitorChart.data.datasets[0].data=json.daily.map(d=>d.total);
    visitorChart.update();
    updateTable(json.daily);
    updateSummaryCards(json.daily);
}

// Filter buttons
document.querySelectorAll('#filterButtons button').forEach(btn=>{
    btn.addEventListener('click',()=>{
        document.querySelectorAll('#filterButtons button').forEach(b=>b.classList.remove('bg-blue-600','text-white'));
        btn.classList.add('bg-blue-600','text-white');
        const f=btn.dataset.filter;
        document.getElementById('customRangePicker').classList.toggle('hidden',f!=='custom');
        if(f==='today'){const r=getTodayRange();loadReport(r.start,r.end);}
        else if(f==='week'){const r=getWeekRange();loadReport(r.start,r.end);}
        else if(f==='month'){const r=getMonthRange();loadReport(r.start,r.end);}
    });
});

// Custom range apply
document.getElementById('applyCustomRange').addEventListener('click',()=>{
    const s=document.getElementById('startDate').value;
    const e=document.getElementById('endDate').value;
    if(!s||!e){alert('Please select both dates');return;}
    loadReport(s,e);
});

// CSV Download
document.getElementById('downloadReport').addEventListener('click',()=>{
    const today=getTodayRange();
    window.location.href=`/admin/reports/export?start_date=${today.start}&end_date=${today.end}`;
});

// Print button
document.getElementById('printReport').addEventListener('click',()=>{
    document.getElementById('printDateTime').textContent=new Date().toLocaleString('en-US');
    document.querySelector('#printVisitorsTable tbody').innerHTML=document.querySelector('#visitorsTable').innerHTML;
    createPrintChart();
    window.print();
});

function createPrintChart(){
    const ctx=document.getElementById('printVisitorChart').getContext('2d');
    new Chart(ctx,{type:'line',data:visitorChart.data,options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
}

// Initial load
const r=getTodayRange();
loadReport(r.start,r.end);
</script>

@endsection
