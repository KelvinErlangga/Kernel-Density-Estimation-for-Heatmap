@extends('perusahaan.master_perusahaan')
@section('title', 'Dashboard Perusahaan | CVRE GENERATE')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h5 class="mb-0 font-weight-bold text-primary">Dashboard</h5>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Content Column -->
        <div class="col-lg-8">

            <!-- Row for Cards -->
            <div class="row">

                <!-- Card Kandidat Pelamar -->
                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card shadow py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto pr-3">
                                    <img src="{{asset('assets/dashboard/diproses.svg')}}" alt="Kandidat Pelamar" class="img-fluid" style="width: 50px" />
                                </div>
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Kandidat Pelamar</div>
                                    <div class="h2 mb-0 font-weight-bold text-gray-800">{{$applicantCount}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Lowongan Aktif -->
                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card shadow py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto pr-3">
                                    <img src="{{asset('assets/dashboard/diproses.svg')}}" alt="Lowongan Aktif" class="img-fluid" style="width: 50px" />
                                </div>
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Lowongan Aktif</div>
                                    <div class="h2 mb-0 font-weight-bold text-gray-800">{{$hiring}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Color System -->
            <div class="row">
                <!-- Tabel Informasi Kandidat -->
                <div class="col-xl-12 col-lg-7">
                    <div class="card shadow mb-4">
                        <!-- Header Card -->
                        <div class="card-header py-3 text-center">
                            <h6 class="m-0 font-weight-bold text-dark">INFORMASI KANDIDAT</h6>
                        </div>
                        <!-- Body Card -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="dataTables" class="table table-bordered table-striped" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nama Kandidat</th>
                                            <th>Posisi Dilamar</th>
                                            <th>Jenis Kelamin</th>
                                            <th>email</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($applicants as $applicant)
                                        <tr>
                                            <td>{{$applicant->user->name}}</td>
                                            <td>{{$applicant->hiring->position_hiring}}</td>
                                            <td>{{$applicant->user->personalPelamar->gender}}</td>
                                            <td>{{$applicant->user->email}}</td>
                                            <td><button class="btn btn-primary btn-sm">Selengkapnya</button></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5">Tidak ada pelamar</td>
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

        <div class="col-lg-4 mb-4">

            <!-- Profil -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 text-center">
                    <h6 class="m-0 font-weight-bold text-dark">PROFILE</h6>
                </div>
                <div class="card-body text-center">
                    <!-- Logo Perusahaan -->
                    <!-- <img src="{{asset('assets/perusahaan/qarea.png')}}" alt="Logo PT QAREA INC" class="mb-3" style="width: 100px; height: auto;"> -->
                    <!-- Nama Perusahaan -->
                    <h5 class="font-weight-bold">{{$personalCompany->name_company}}</h5>
                    <p>{{$personalCompany->city_company}}</p>

                    <!-- Informasi Detail -->
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="font-weight-bold">1700</h4>
                            <p>Karyawan</p>
                        </div>
                        <div class="col-4">
                            <h4 class="font-weight-bold">20</h4>
                            <p>Devisi</p>
                        </div>
                        <div class="col-4">
                            <h4 class="font-weight-bold">2017</h4>
                            <p>Berdiri</p>
                        </div>
                    </div>

                    <!-- Deskripsi Perusahaan -->
                    <p class="mt-3">
                        Perusahaan yang bergerak di bidang fintech yang selalu berkembang setiap tahun dengan lebih dari 1000 lebih investor tetap.
                    </p>
                </div>
            </div>


            <!-- Pesan -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 text-center">
                    <h6 class="m-0 font-weight-bold text-dark">PESAN</h6>
                </div>
                <div class="card-body">
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Non dolorum, doloribus autem similique quam illum aspernatur possimus maiores expedita porro blanditiis qui dicta voluptatibus itaque reprehenderit voluptate? Quas, quasi voluptatibus.
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Praesentium neque reiciendis perspiciatis officiis tenetur, qui officia accusamus! Dolorum nisi aspernatur illo et. Dolore hic quo et exercitationem neque! Illo, quos!
                </div>
            </div>

        </div>
    </div>
</div>
@endsection