@extends('perusahaan.master_perusahaan')
@section('title', 'Perusahaan - Lowongan | CVRE GENERATE')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h5 class="mb-0 font-weight-bold text-primary">Dashboard / Kelola Data / <span class="text-dark">Lowongan</span></h5>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-body m-10">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <!-- <img src="{{asset('assets/akun/profil.png')}}" alt="Profile Picture" class="img-fluid rounded-circle mb-3 mx-auto d-block" style="width: 150px" /> -->
                            <h3 class="font-weight-bold mb-2">{{Auth::user()->name}}</h3>
                            <!-- <p>UI/UX Designer</p> -->
                            <ul class="nav flex-column mt-8">
                                <li class="nav-item">
                                    <a class="nav-link active text-primary font-weight-bold m-3" href="#">Posting Lowongan</a>
                                    <hr />
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-secondary m-3" href="#">Lowongan Aktif</a>
                                    <hr />
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-secondary m-3" href="#">Lainnya</a>
                                    <hr />
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-8">
                            <div class="form-container" style="border: 1px solid; padding: 50px; border-radius: 8px">
                                <h4 class="mb-4 text-center font-weight-bold" style="font-size:1.725rem; color: black;">Posting Lowongan</h4>
                                <form method="POST" action="{{route('perusahaan.lowongan.addLowongan')}}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="position_hiring">Posisi Lowongan</label>
                                        <input type="text" class="form-control" name="position_hiring" id="position_hiring" placeholder="Web Developer" value="{{old('position_hiring')}}" required />
                                    </div>

                                    <div class="form-group">
                                        <label for="address_hiring">Alamat Lowongan</label>
                                        <input type="text" class="form-control" name="address_hiring" id="address_hiring" placeholder="alamat" value="{{old('address_hiring')}}" required />
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="type_of_work">Jenis Pekerjaan</label>
                                            <input type="text" class="form-control" name="type_of_work" id="type_of_work" placeholder="Full Time" value="{{old('type_of_work')}}" required />
                                        </div>
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="work_system">Sistem Kerja</label>
                                            <input type="text" class="form-control" name="work_system" id="work_system" placeholder="Kontrak" value="{{old('work_system')}}" required />
                                        </div>
                                        <div style="flex: 1;">
                                            <label for="pola_kerja">Pola Kerja</label>
                                            <input type="text" class="form-control" name="pola_kerja" id="pola_kerja" placeholder="WFO" value="{{old('pola_kerja')}}" required />
                                        </div>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="gaji">Gaji Bulanan</label>
                                            <input type="number" class="form-control" name="gaji" id="gaji" placeholder="Rp 45.000.000" value="{{old('gaji')}}" required />
                                        </div>
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="deadline_hiring">Batas Lowongan</label>
                                            <input type="date" class="form-control" name="deadline_hiring" id="deadline_hiring" value="{{old('deadline_hiring')}}" required />
                                        </div>
                                        <div style="flex: 1;">
                                            <label for="education_hiring">Pendidikan</label>
                                            <input type="text" class="form-control" name="education_hiring" id="education_hiring" placeholder="S1 (Ekonomi)" value="{{old('education_hiring')}}" required />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="description_hiring">Deskripsi Pekerjaan & Persyaratan</label>
                                        <textarea class="form-control" name="description_hiring" id="description_hiring" rows="5" placeholder="Deskripsi pekerjaan dan persyaratan..." required>{{old('description_hiring')}}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary d-block mx-auto mt-10">Buat Lowongan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection