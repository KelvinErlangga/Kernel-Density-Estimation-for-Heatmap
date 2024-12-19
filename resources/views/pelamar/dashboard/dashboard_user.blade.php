@extends('pelamar.dashboard.master_user')
@section('title', 'Dashboard | CVRE GENERATE')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h5 class="mb-0 font-weight-bold text-primary">Dashboard</h5>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Card Berkas Diproses -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <!-- Icon Sebelah Kiri -->
                        <div class="col-auto pr-3">
                            <img src="{{asset('assets/dashboard/diproses.svg')}}" alt="Berkas Diproses" class="img-fluid" style="width: 50px" />
                        </div>
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Berkas Diproses</div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{$pending}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Berkas Ditolak -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <!-- Icon Sebelah Kiri -->
                        <div class="col-auto pr-3">
                            <img src="{{asset('assets/dashboard/ditolak.svg')}}" alt="Berkas Ditolak" class="img-fluid" style="width: 50px" />
                        </div>
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Berkas Ditolak</div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{$ditolak}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Berkas Diterima -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <!-- Icon Sebelah Kiri -->
                        <div class="col-auto pr-3">
                            <img src="{{asset('assets/dashboard/diterima.png')}}" alt="Berkas Diterima" class="img-fluid" style="width: 50px" />
                        </div>
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Berkas Diterima</div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{$diterima}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->

    <div class="row">
        <!-- Tabel Informasi Lowongan -->
        <div class="col-xl-12 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Header Card -->
                <div class="card-header py-3 text-center">
                    <h6 class="m-0 font-weight-bold text-dark">INFORMASI LOWONGAN</h6>
                </div>
                <!-- Body Card -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTables" class="table table-bordered table-striped" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Nama Perusahaan</th>
                                    <th>Posisi Dibutuhkan</th>
                                    <th>Dibuat</th>
                                    <th>Gaji</th>
                                    <th>Batas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hirings as $hiring)
                                <tr>
                                    <td>{{$hiring->personalCompany->name_company}}</td>
                                    <td>{{$hiring->position_hiring}}</td>
                                    <td>{{date('d M Y', strtotime($hiring->created_at))}}</td>
                                    <td>Rp {{number_format($hiring->gaji, 0, ',', '.')}}</td>
                                    <td>{{date('d M Y', strtotime($hiring->deadline_hiring))}}</td>
                                    <td><a href="{{route('pelamar.dashboard.lowongan.index')}}" class="btn btn-primary btn-sm">Selengkapnya</a></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6">Tidak ada lowongan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection