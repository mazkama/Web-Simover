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
                    @foreach ($devices as $device)
                    <a class="dropdown-item {{ request('device_id') == $device->id ? 'active' : '' }}" href="{{ route('dashboard', ['device_id' => $device->id]) }}">
                        {{ $device->device_name }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body bg">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Suhu</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12">
                                    <h3 class="mt-2 fs-2" id="temperature"> <i data-feather="thermometer"></i> 0.00 °C</h3>
                                </div>
                                <div class="d-flex align-items-baseline mt-3">
                                    <p class="text-primary">
                                        <span id="temperature-status">Kondisi Aman</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Kelembapan</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12">
                                    <h3 class="mt-2 fs-2" id="humidity"> <i data-feather="cloud-rain"></i> 0.00 %</h3>
                                </div>
                                <div class="d-flex align-items-baseline mt-3">
                                    <p class="text-primary">
                                        <span id="humidity-status">Kondisi Aman</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Level Asap</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12">
                                    <h3 class="mt-2 fs-2" id="smoke-level"> <i data-feather="wind"></i> 0.00 ppm</h3>
                                </div>
                                <div class="d-flex align-items-baseline mt-3">
                                    <p class="text-primary">
                                        <span id="smoke-level-status">Kondisi Aman</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Gerakan</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12">
                                    <h3 class="mt-2 fs-2" id="motion"> <i data-feather="activity"></i> -</h3>
                                </div>
                                <div class="d-flex align-items-baseline mt-3">
                                    <p class="text-primary">
                                        <span id="motion-status">Kondisi Aman</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-xl-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Line Chart</h6>
                    <div id="apexLine"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-6 stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <h6 class="card-title mb-0">Time Series</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="pt-0">#</th>
                                    <th class="pt-0">Suhu</th>
                                    <th class="pt-0">Kelembapan</th>
                                    <th class="pt-0">Level Asap</th>
                                    <th class="pt-0">Gerakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($historySensors as $sensor)
                                <tr>
                                    <td>26/04/2022 12:12:12</td>
                                    <td>{{ $sensor->temperature ?? '-' }} °C</td>
                                    <td>{{ $sensor->humidity ?? '-' }} %</td>
                                    <td>{{ $sensor->smoke ?? '-' }} %</td>
                                    <td>{{ $sensor->motion === 1 ? 'Terdeteksi' : 'Tidak Terdeteksi' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Fetching Data -->
<script>
    const endpointUrl = "https://simover-kominfo-default-rtdb.asia-southeast1.firebasedatabase.app/sensors.json"; // Pastikan menambahkan ".json" pada URL Firebase

    function fetchData() {
        fetch(endpointUrl)
            .then(response => response.json()) // Mengambil data dalam format JSON
            .then(data => {
                updateSensorData(data); // Memperbarui data sensor di halaman
            })
            .catch(error => {
                console.error("Error fetching data: ", error); // Menangani error jika ada masalah
            });
    }

    // Fungsi untuk memperbarui tampilan data
    function updateSensorData(data) {
        // Update suhu
        document.getElementById('temperature').textContent = `${data.temperature} °C`;
        const temperatureStatus = data.temperature > 30 ? 'Kondisi Panas' : 'Kondisi Aman';
        document.getElementById('temperature-status').textContent = temperatureStatus;

        // Update kelembapan
        document.getElementById('humidity').textContent = `${data.humidity} %`;
        const humidityStatus = data.humidity > 60 ? 'Kondisi Lembap' : 'Kondisi Aman';
        document.getElementById('humidity-status').textContent = humidityStatus;

        // Update kualitas udara
        document.getElementById('smoke-level').textContent = `${data.smoke} ppm`;
        const airQualityStatus = data.smoke > 250 ? 'Kualitas Udara Buruk' : 'Kondisi Aman';
        document.getElementById('smoke-level-status').textContent = airQualityStatus;

        // Update gerakan
        const motionStatus = data.motion ? 'Gerakan Terdeteksi' : 'Tidak Ada Gerakan';
        document.getElementById('motion').textContent = motionStatus;
        document.getElementById('motion-status').textContent = motionStatus;
    }

    // Fetch data every 10 seconds (10000 ms)
    setInterval(fetchData, 5000); // Update every 10 seconds
    fetchData(); // Initial fetch when page loads
</script>

@endsection
