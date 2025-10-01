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
                            <h3 class="font-weight-bold mb-2">{{ Auth::user()->name }}</h3>
                            <ul class="nav flex-column mt-8">
                                <li class="nav-item">
                                    <a class="nav-link text-primary font-weight-bold m-3" href="{{ route('perusahaan.lowongan.index') }}">List Lowongan</a>
                                    <hr />
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-secondary m-3" href="{{ route('perusahaan.lowongan.index') }}">Posting Lowongan</a>
                                    <hr />
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-8">
                            <!-- Edit Posting Lowongan -->
                            <div id="edit-form-container" class="form-container" style="border: 1px solid; padding: 50px; border-radius: 8px; display: block;">
                                <h4 class="mb-4 text-center font-weight-bold" style="font-size:1.725rem; color: black;">Edit Posting Lowongan</h4>

                                <form method="POST" action="{{ route('perusahaan.lowongan.updateLowongan', $hiring->id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group">
                                        <label for="position_hiring">Posisi Lowongan</label>
                                        <input type="text" class="form-control" name="position_hiring" id="position_hiring" value="{{ $hiring->position_hiring }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="address_hiring">Alamat Lowongan</label>
                                        <input type="text" class="form-control" name="address_hiring" id="address_hiring" value="{{ $hiring->address_hiring }}" required>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="work_system">Sistem Kerja</label>
                                            <input type="text" class="form-control" name="work_system" id="work_system" value="{{ $hiring->work_system }}" required>
                                        </div>
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="pola_kerja">Pola Kerja</label>
                                            <input type="text" class="form-control" name="pola_kerja" id="pola_kerja" value="{{ $hiring->pola_kerja }}" required>
                                        </div>
                                        <div style="flex: 1;">
                                            <label for="jenis_pekerjaan">Jenis Pekerjaan</label>
                                            <input type="text" class="form-control" name="jenis_pekerjaan" id="jenis_pekerjaan" value="{{ $hiring->jenis_pekerjaan }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="gaji_min">Gaji Minimum</label>
                                            <input type="number" class="form-control" name="gaji_min" id="gaji_min" value="{{ $hiring->gaji_min }}" required>
                                        </div>
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="gaji_max">Gaji Maksimum</label>
                                            <input type="number" class="form-control" name="gaji_max" id="gaji_max" value="{{ $hiring->gaji_max }}" required>
                                        </div>
                                        <div style="flex: 1;">
                                            <label for="deadline_hiring">Batas Lowongan</label>
                                            <input type="date" class="form-control" name="deadline_hiring" id="deadline_hiring" value="{{ $hiring->deadline_hiring }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="education_hiring">Pendidikan</label>
                                            <input type="text" class="form-control" name="education_hiring" id="education_hiring" value="{{ $hiring->education_hiring }}" required>
                                        </div>
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="ukuran_perusahaan">Ukuran Perusahaan</label>
                                            <input type="text" class="form-control" name="ukuran_perusahaan" id="ukuran_perusahaan" value="{{ $hiring->ukuran_perusahaan }}" required>
                                        </div>
                                        <div style="flex: 1;">
                                            <label for="sektor_industri">Sektor Industri</label>
                                            <input type="text" class="form-control" name="sektor_industri" id="sektor_industri" value="{{ $hiring->sektor_industri }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="kualifikasi">Kualifikasi</label>
                                        <textarea class="form-control" name="kualifikasi" id="kualifikasi" rows="3" required>{{ $hiring->kualifikasi }}</textarea>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="pengalaman_minimal_tahun">Pengalaman Minimal (tahun)</label>
                                            <input type="number" class="form-control" name="pengalaman_minimal_tahun" id="pengalaman_minimal_tahun" value="{{ $hiring->pengalaman_minimal_tahun }}" required>
                                        </div>
                                        <div style="flex: 1;">
                                            <label for="usia_maksimal">Usia Maksimal</label>
                                            <input type="number" class="form-control" name="usia_maksimal" id="usia_maksimal" value="{{ $hiring->usia_maksimal }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="keterampilan_teknis">Keterampilan Teknis</label>
                                        <textarea class="form-control" name="keterampilan_teknis" id="keterampilan_teknis" rows="3" required>{{ $hiring->keterampilan_teknis }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="keterampilan_non_teknis">Keterampilan Non-Teknis</label>
                                        <textarea class="form-control" name="keterampilan_non_teknis" id="keterampilan_non_teknis" rows="3" required>{{ $hiring->keterampilan_non_teknis }}</textarea>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="kota">Kota</label>
                                            <input type="text" class="form-control" name="kota" id="kota" value="{{ $hiring->kota }}" required>
                                        </div>
                                        <div style="flex: 1;">
                                            <label for="provinsi">Provinsi</label>
                                            <input type="text" class="form-control" name="provinsi" id="provinsi" value="{{ $hiring->provinsi }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="latitude">Latitude</label>
                                            <input type="text" class="form-control" name="latitude" id="latitude" value="{{ $hiring->latitude }}" required>
                                        </div>
                                        <div style="flex: 1;">
                                            <label for="longitude">Longitude</label>
                                            <input type="text" class="form-control" name="longitude" id="longitude" value="{{ $hiring->longitude }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="description_hiring">Deskripsi Pekerjaan & Persyaratan</label>
                                        <textarea class="form-control" name="description_hiring" id="description_hiring" rows="5" required>{{ $hiring->description_hiring }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary d-block mx-auto mt-10">Simpan Perubahan</button>
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
