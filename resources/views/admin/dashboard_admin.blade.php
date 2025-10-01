@extends('admin.master_admin')
@section('title', 'Dashboard | CVRE GENERATE')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Content Row -->
    <div class="row">
        <!-- Card Template CV -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <!-- Icon Sebelah Kiri -->
                        <div class="col-auto pr-3">
                            <i class="fas fa-clipboard-list fa-2x text-primary"></i>
                        </div>
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Template Curriculum Vitae</div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{$countTemplateCV}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Data Keahlian -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto pr-3">
                            <i class="fas fa-book fa-2x text-primary"></i>
                        </div>
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Data Keahlian</div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{$skills}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Data Pekerjaan -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto pr-3">
                            <i class="fas fa-id-badge fa-2x text-primary"></i>
                        </div>
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Data Pekerjaan</div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{$jobs}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Rekomendasi Keahlian -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto pr-3">
                            <i class="fas fa-users-cog fa-2x text-primary"></i>
                        </div>
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Rekomendasi Keahlian</div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{$recommendedSkills}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Data Pengguna -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto pr-3">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Data Pengguna</div>
                            <div class="h2 mb-0 font-weight-bold text-gray-800">{{$userPelamar}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
