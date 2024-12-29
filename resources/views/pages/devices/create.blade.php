@extends('layouts.app')
@section('title','Perangkat')
@section('content')

<div class="page-content">

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item active" aria-current="page">Perangkat</li>
        </ol>
    </nav>

    <div class="row">
        <form id="device-form" action="{{ route('device.store') }}" method="POST">
            @csrf
            <div class="col-12 grid-margin stretch-card">
                <div class="col-md-6 grid-margin stretch-card me-2">
                    <div class="card">
                        <div class="card-body">

                            <h5 class="mb-3 mb-md-2">Tambah Perangkat</h5>
                            <p class="text-muted mb-5">Tambah perangkat yang terhubung untuk proses monitoring dan penyimpanan riwayat.</p>

                            <div class="mb-3">
                                <label for="device_id" class="form-label me-2">ID Perangkat</label>
                                <div class="d-flex align-items-center">
                                    <input type="number" id="device_id" name="device_id" class="form-control" placeholder="Id Perangkat" required>
                                    <button type="button" id="check-id-btn" class="btn btn-primary mx-2">Check</button>
                                    <!-- Loading Spinner -->
                                    <div id="loading-spinner" style="display: none;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="additional-fields" style="display: none;">
                                <div class="mb-3">
                                    <label for="device_name" class="form-label">Nama Perangkat</label>
                                    <input type="text" id="device_name" name="device_name" placeholder="Nama Perangkat" class="form-control">
                                </div>
                            </div>

                            <button type="submit" id="submit-btn" class="btn btn-primary" style="display: none;">Submit</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 grid-margin stretch-card me-2" id="additional-thresholds" style="display: none;">
                    <div class="card">
                        <div class="card-body">

                            <h5 class="mb-3 mb-md-2">Batas Sensor</h5>
                            <p class="text-muted mb-5">Tambahkan batasan yang akan digunakan untuk memberikan notifikasi peringatan.</p>

                            <div class="mb-3">
                                <label for="sensor_temp" class="form-label">Sensor Suhu</label>
                                <input type="number" id="sensor_temp" name="sensor_temp" placeholder="Batas Suhu" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="sensor_humidity" class="form-label">Sensor Kelembapan</label>
                                <input type="number" id="sensor_humidity" name="sensor_humidity" placeholder="Batas Kelembapan" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="sensor_smoke" class="form-label">Sensor Asap</label>
                                <input type="number" id="sensor_smoke" name="sensor_smoke" placeholder="Batas Asap" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    document.getElementById('check-id-btn').addEventListener('click', function() {
        const deviceId = document.getElementById('device_id').value;
        const loadingSpinner = document.getElementById('loading-spinner');
        const additionalFields = document.getElementById('additional-fields');
        const additionalThresholds = document.getElementById('additional-thresholds');
        const submitButton = document.getElementById('submit-btn');
        const checkIdButton = document.getElementById('check-id-btn');
        const deviceIdInput = document.getElementById('device_id');

        // Show loading spinner and hide additional fields & submit button
        loadingSpinner.style.display = 'block';
        additionalFields.style.display = 'none';
        additionalThresholds.style.display = 'none';
        submitButton.style.display = 'none';

        // Perform the fetch request
        fetch("{{ route('device.cek') }}", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    device_id: deviceId
                })
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading spinner
                loadingSpinner.style.display = 'none';

                if (data.exists) {
                    // Disable input and hide "Check ID" button
                    deviceIdInput.setAttribute('readOnly', true);
                    checkIdButton.style.display = 'none';

                    // Show additional fields and submit button
                    additionalFields.style.display = 'block';
                    additionalThresholds.style.display = 'block';
                    submitButton.style.display = 'inline-block';
                } else {
                    alert('Device ID not found in Firebase!');
                }
            })
            .catch(error => {
                loadingSpinner.style.display = 'none';
                alert('An error occurred while checking the Device ID.');
                console.error(error);
            });
    });
</script>
@endsection
