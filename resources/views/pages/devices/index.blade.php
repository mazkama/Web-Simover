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
        <div class="col-md-12">
            <h2 class="text-center mb-3 mt-4">Semua Perangkat</h2>
            <p class="text-muted text-center mb-4 pb-2">Perangkat yang terhubung dengan sistem, Untuk proses monitoring dan penyimpanan riwayat.</p>
            <div class="container">
                <div class="row">
                    @foreach($devices as $data)
                    <div class="col-md-4 mb-2">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="text-center mt-3 mb-1">{{ $data->id }}</h3>
                                <h2 class="text-center">{{ $data->device_name }}</h2>
                                <p class="text-muted text-center mb-2 mt-1 fw-light">Pembaruan terakhir, {{ $data->latestSensorHistory->recorded_at ?? '-'}}</p>
                                <a href="{{ route('dashboard') }}?device_id={{ $data->id }}">
                                    <i data-feather="monitor" class="text-primary icon-xxl d-block mx-auto mt-4"></i>
                                    <p class="text-primary text-center mb-4 mt-2">Lihat Dashboard</p>
                                </a>
                                <table class="mx-auto">
                                    <tr>
                                        <td><i data-feather="thermometer" class="icon-md text-dark me-2"></i></td>
                                        <td>
                                            <p>{{ $data->latestSensorHistory->temperature ?? '-'}} °C</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="cloud-rain" class="icon-md text-dark me-2"></i></td>
                                        <td>
                                            <p>{{ $data->latestSensorHistory->humidity ?? '-'}} %</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="wind" class="icon-md text-dark me-2"></i></td>
                                        <td>
                                            <p>{{ $data->latestSensorHistory->smoke ?? '-'}} ppm</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="activity" class="icon-md text-dark me-2"></i></td>
                                        <td>
                                            <p>
                                                @isset($data->latestSensorHistory)
                                                {{ $data->latestSensorHistory->motion === 1 ? 'Terdeteksi' : 'Tidak Terdeteksi' }}
                                                @else
                                                -
                                                @endisset
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                                <div class="d-grid mb-3">
                                    <a href="{{ route('device.edit', $data->id) }}" class="btn btn-primary mt-4">Ubah</a>
                                    <form action="{{ route('device.delete', $data->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus perangkat ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger w-100 mt-1">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <!-- <div class="col-md-4 stretch-card grid-margin grid-margin-md-0">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="text-center mt-3 mb-4">Business</h4>
                                <i data-feather="gift" class="text-success icon-xxl d-block mx-auto my-3"></i>
                                <h1 class="text-center">$70</h1>
                                <p class="text-muted text-center mb-4 fw-light">per month</p>
                                <h5 class="text-success text-center mb-4">Up to 75 units</h5>
                                <table class="mx-auto">
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td>
                                            <p>Accounting dashboard</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td>
                                            <p>Invoicing</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td>
                                            <p>Online payments</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td>
                                            <p>Branded website</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td>
                                            <p>Dedicated account manager</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="x" class="icon-md text-danger me-2"></i></td>
                                        <td>
                                            <p class="text-muted">Premium apps</p>
                                        </td>
                                    </tr>
                                </table>
                                <div class="d-grid">
                                    <button class="btn btn-success mt-4">Edit Perangkat</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="text-center mt-3 mb-4">Professional</h4>
                                <i data-feather="briefcase" class="text-primary icon-xxl d-block mx-auto my-3"></i>
                                <h1 class="text-center">$250</h1>
                                <p class="text-muted text-center mb-4 fw-light">per month</p>
                                <h5 class="text-primary text-center mb-4">Up to 300 units</h5>
                                <table class="mx-auto">
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td>
                                            <p>Accounting dashboard</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td>
                                            <p>Invoicing</p>
                                        </td>
                                    </tr>
                                    <tr>

                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td>
                                            <p>Online payments</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td>
                                            <p>Branded website</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td>
                                            <p>Dedicated account manager</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td>
                                            <p>Premium apps</p>
                                        </td>
                                    </tr>
                                </table>
                                <div class="d-grid">
                                    <button class="btn btn-primary mt-4">Start free trial</button>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
