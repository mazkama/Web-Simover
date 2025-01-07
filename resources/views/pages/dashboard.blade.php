@extends('layouts.app')
@section('title','Dashboard')
@section('content')

<div class="page-content">
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Dashboard {{ $device->device_name }}</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Perangkat
                </button>
                <div class="dropdown-menu overflow-auto" style="max-height: 200px;" aria-labelledby="dropdownMenuButton1">
                    @foreach ($devices as $deviceOption)
                    <a class="dropdown-item {{ request('device_id') == $deviceOption->id ? 'active' : '' }}" href="{{ route('dashboard', ['device_id' => $deviceOption->id]) }}">
                        {{ $deviceOption->device_name }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sensor Cards -->
        @foreach (['Suhu' => 'thermometer', 'Kelembapan' => 'cloud-rain', 'Level Asap' => 'wind', 'Gerakan' => 'activity'] as $label => $icon)
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">{{ $label }}</h6>
                    <h3 class="mt-2 fs-2"><i data-feather="{{ $icon }}"></i> <span id="{{ strtolower(str_replace(' ', '-', $label)) }}">0</span></h3>
                    <p class="text-primary mt-3"><span id="{{ strtolower(str_replace(' ', '-', $label)) }}-status">-</span></p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row">
        <!-- Line Chart -->
        <div class="col-lg-12 col-xl-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Line Chart</h6>
                    <div id="lineChart"></div>
                </div>
            </div>
        </div>
        <!-- Time Series Table -->
        <div class="col-lg-12 col-xl-6 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Time Series</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Suhu</th>
                                    <th>Kelembapan</th>
                                    <th>Level Asap</th>
                                    <th>Gerakan</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="5">No data available</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    const deviceId = "{{ $device->id }}";
    const endpointUrl = `{{ url('/api/sensor-histories/data') }}?device_id=${deviceId}`;

    let lineChart;

    function initLineChart() {
        const options = {
            chart: {
                type: 'line',
                height: 350
            },
            series: [
                { name: 'Suhu', data: [], color: '#ff3366' }, // Merah
                { name: 'Kelembapan', data: [], color: '#6571ff' }, // Kuning
                { name: 'Level Asap', data: [], color: '#fbbc06' }, // Ungu
            ],
            xaxis: {
                type: 'datetime'
            }
        };

        lineChart = new ApexCharts(document.querySelector("#lineChart"), options);
        lineChart.render();
    }

    function updateLineChart(data) {
        const suhuData = [];
        const kelembapanData = [];
        const levelAsapData = [];

        data.forEach(sensor => {
            const time = new Date(sensor.recorded_at).getTime();
            suhuData.push([time, sensor.temperature]);
            kelembapanData.push([time, sensor.humidity]);
            levelAsapData.push([time, sensor.smoke]);
        });

        lineChart.updateSeries([
            { name: 'Suhu', data: suhuData },
            { name: 'Kelembapan', data: kelembapanData },
            { name: 'Level Asap', data: levelAsapData },
        ]);
    }

    function fetchSensorData() {
        // Fetch sensor data for cards
        fetch(`https://simover-kominfo-default-rtdb.asia-southeast1.firebasedatabase.app/${deviceId}/sensors.json`)
            .then(response => response.json())
            .then(data => updateSensorData(data))
            .catch(error => console.error("Error fetching sensor data:", error));

        // Fetch time series data
        $.ajax({
            url: endpointUrl,
            method: 'GET',
            success: function(data) {
                let tableBody = '';
                data.forEach((sensor, index) => {
                    tableBody += `
                        <tr>
                            <td>${sensor.recorded_at || '-'}</td>
                            <td>${sensor.temperature || '-'}°C</td>
                            <td>${sensor.humidity || '-'}%</td>
                            <td>${sensor.smoke || '-'} ppm</td>
                            <td>${sensor.motion ? 'Terdeteksi' : 'Tidak Terdeteksi'}</td>
                        </tr>
                    `;
                });
                $('#tableBody').html(tableBody);
                updateLineChart(data);
            },
            error: function(error) {
                console.error("Error fetching time series data:", error);
            }
        });
    }

    function updateSensorData(data) {
        // Update cards
        $('#suhu').text(`${data.temperature || '0'} °C`);
        $('#suhu-status').text(data.temperature > 30 ? 'Panas' : 'Aman');
        $('#kelembapan').text(`${data.humidity || '0'} %`);
        $('#kelembapan-status').text(data.humidity > 60 ? 'Lembap' : 'Aman');
        $('#level-asap').text(`${data.smoke || '0'} ppm`);
        $('#level-asap-status').text(data.smoke > 250 ? 'Berbahaya' : 'Aman');
        $('#gerakan').text(data.motion ? 'Terdeteksi' : 'Tidak');
        $('#gerakan-status').text(data.motion ? 'Ada gerakan' : 'Tidak ada gerakan');
    }

    $(document).ready(function() {
        initLineChart();
        fetchSensorData();
        setInterval(fetchSensorData, 5000); // Refresh every 5 seconds
    });
</script>

@endsection
