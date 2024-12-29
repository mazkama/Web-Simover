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
        <form id="device-form" action="{{ route('device.update', $device->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="col-md-12 grid-margin stretch-card">
                <div class="col-md-6 grid-margin stretch-card me-2">
                    <div class="card">
                        <div class="card-body">

                            <h5 class="mb-3 mb-md-2">Ubah Perangkat</h5>
                            <p class="text-muted mb-5">Ubah perangkat yang terhubung untuk proses monitoring dan penyimpanan riwayat.</p>

                            <div class="mb-3">
                                <label for="device_id" class="form-label me-2">ID Perangkat</label>
                                <input type="number" id="device_id" name="device_id" class="form-control" placeholder="Id Perangkat" readonly value="{{ $device->id }}">
                            </div>

                            <div class="mb-3">
                                <label for="device_name" class="form-label">Nama Perangkat</label>
                                <input type="text" id="device_name" name="device_name" placeholder="Nama Perangkat" class="form-control" value="{{ $device->device_name }}">
                            </div>

                            <button type="submit" id="submit-btn" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 grid-margin stretch-card me-2">
                    <div class="card">
                        <div class="card-body">

                            <h5 class="mb-3 mb-md-2">Batas Sensor</h5>
                            <p class="text-muted mb-5">Tambahkan batasan yang akan digunakan untuk memberikan notifikasi peringatan.</p>

                            <div class="mb-3">
                                <label for="sensor_temp" class="form-label">Sensor Suhu</label>
                                <input type="number" id="sensor_temp" name="sensor_temp" placeholder="Batas Suhu" class="form-control" value="{{ $thresholds['asap']}}">
                            </div>
                            <div class="mb-3">
                                <label for="sensor_humidity" class="form-label">Sensor Kelembapan</label>
                                <input type="number" id="sensor_humidity" name="sensor_humidity" class="form-control" placeholder="Batas Kelembapan" value="{{ $thresholds['kelembapan'] }}">
                            </div>
                            <div class="mb-3">
                                <label for="sensor_smoke" class="form-label">Sensor Asap</label>
                                <input type="number" id="sensor_smoke" name="sensor_smoke" class="form-control" placeholder="Batas Asap" value="{{ $thresholds['asap'] }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
