@extends('layouts.app')
@section('title','Riwayat')
@section('content')

<div class="page-content">

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item active" aria-current="page">Riwayat Sensor</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
                        <div>
                            <h5 class="mb-3 mb-md-2">Data Riwayat Sensor</h5>
                            <p class="text-muted mb-3">Riwayat Sensor dari perangkat beberapa waktu terakhir.</p>
                        </div>
                        <div>
                            <form action="{{ route('sensorHistory.index') }}" method="GET">
                                <div class="row align-items-center">
                                    <!-- Start Date Picker -->
                                    <div class="col-md-4 mb-3">
                                        <label for="startDatePicker" class="form-label">Tanggal Awal</label>
                                        <div class="input-group flatpickr" id="startDatePicker">
                                            <span class="input-group-text input-group-addon bg-transparent border-primary">
                                                <i data-feather="calendar" class="text-primary"></i>
                                            </span>
                                            <input type="text" name="start_date" class="form-control bg-transparent border-primary"
                                                placeholder="Pilih Tanggal Awal" data-input
                                                value="{{ request('start_date', $start_date) }}">
                                        </div>
                                    </div>

                                    <!-- End Date Picker -->
                                    <div class="col-md-4 mb-3">
                                        <label for="endDatePicker" class="form-label">Tanggal Akhir</label>
                                        <div class="input-group flatpickr" id="endDatePicker">
                                            <span class="input-group-text input-group-addon bg-transparent border-primary">
                                                <i data-feather="calendar" class="text-primary"></i>
                                            </span>
                                            <input type="text" name="end_date" class="form-control bg-transparent border-primary"
                                                placeholder="Pilih Tanggal Akhir" data-input
                                                value="{{ request('end_date', $end_date) }}">
                                        </div>
                                    </div>

                                    <!-- Device Selector -->
                                    <div class="col-md-4 mb-3">
                                        <label for="deviceSelector" class="form-label">Perangkat</label>
                                        <div class="input-group">
                                            <select class="form-select" name="device_id" id="deviceSelector">
                                                <option value="">Semua Perangkat</option>
                                                @foreach($devices as $device)
                                                <option value="{{ $device->id }}" {{ request('device_id') == $device->id ? 'selected' : '' }}>
                                                    {{ $device->device_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>


                    </div>

                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>Device</th>
                                    <th>Waktu</th>
                                    <th>Suhu</th>
                                    <th>Kelembapan</th>
                                    <th>Level Asap</th>
                                    <th>Gerakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sensorHistory as $data)
                                <tr>
                                    <td>{{ $data->device_id }}</td>
                                    <td>{{ $data->recorded_at }}</td>
                                    <td>{{ $data->temperature }}°C</td>
                                    <td>{{ $data->humidity }}%</td>
                                    <td>{{ $data->smoke }}ppm</td>
                                    <td>{{ $data->motion ? 'Ada' : 'Tidak' }}</td>
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
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr("#startDatePicker input", {
            dateFormat: "Y-m-d",
            defaultDate: "{{ $start_date }}",
            allowInput: true
        });

        flatpickr("#endDatePicker input", {
            dateFormat: "Y-m-d",
            defaultDate: "{{ $end_date }}",
            allowInput: true
        });
    });
</script>
