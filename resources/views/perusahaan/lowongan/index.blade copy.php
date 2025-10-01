@extends('perusahaan.master_perusahaan')
@section('title', 'Perusahaan - Lowongan | CVRE GENERATE')

@push('style')
<link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}">
<style>
    /* Placeholder abu muda untuk semua input di form-container */
    #form-container ::placeholder {
        color: #b0b0b0 !important;
        opacity: 1;
    }
</style>
@endpush

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
                        <div class="col-md-3 text-center">
                            <h3 class="font-weight-bold mb-2">{{ Auth::user()->name }}</h3>
                            <ul class="nav flex-column mt-8">
                                <li class="nav-item">
                                    <a id="btn-list" class="nav-link text-secondary m-3" href="#"
                                        onclick="showForm('list')">List Lowongan</a>
                                    <hr />
                                </li>
                                <li class="nav-item">
                                    <a id="btn-form" class="nav-link text-secondary m-3" href="#"
                                        onclick="showForm('form')">Posting Lowongan</a>
                                    <hr />
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-9">
                            <!-- Daftar Lowongan -->
                            <div id="list-container" class="row form-container" style="display: block;">
                                <div class="col-xl-12 col-lg-7">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3 text-center">
                                            <h6 class="m-0 font-weight-bold text-dark">Lowongan</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="dataTable" class="table table-striped table-bordered" width="100%" cellspacing="0">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th>Posisi</th>
                                                            <th>Sistem Kerja</th>
                                                            <th>Tanggal Dibuat</th>
                                                            <th>Batas Waktu</th>
                                                            <th class="text-center">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($hirings as $hiring)
                                                            <tr>
                                                                <td>{{ $hiring->position_hiring }}</td>
                                                                <td>{{ $hiring->work_system }}</td>
                                                                <td>{{ date('d M Y', strtotime($hiring->created_at)) }}</td>
                                                                <td>{{ date('d M Y', strtotime($hiring->deadline_hiring)) }}</td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-center gap-2">
                                                                        <!-- Edit -->
                                                                        <a href="{{ route('perusahaan.lowongan.editLowongan', $hiring->id) }}"
                                                                            class="btn btn-sm btn-outline-primary mr-2">
                                                                            <i class="far fa-fw fa-edit"></i> Edit
                                                                        </a>

                                                                        @if(is_null($hiring->deleted_at))
                                                                            <!-- Nonaktifkan -->
                                                                            <form id="delete-form-{{ $hiring->id }}"
                                                                                action="{{ route('perusahaan.lowongan.nonaktifkanLowongan', $hiring->id) }}"
                                                                                method="POST" style="display:inline;">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                                                    onclick="confirmNonaktifkan('delete-form-{{ $hiring->id }}')">
                                                                                    <i class="fas fa-fw fa-ban"></i> Nonaktifkan
                                                                                </button>
                                                                            </form>
                                                                        @else
                                                                            <!-- Aktifkan -->
                                                                            <form id="restore-form-{{ $hiring->id }}"
                                                                                action="{{ route('perusahaan.lowongan.restoreLowongan', $hiring->id) }}"
                                                                                method="POST" style="display:inline;">
                                                                                @csrf
                                                                                <button type="button" class="btn btn-sm btn-outline-success"
                                                                                    onclick="confirmAktifkan('restore-form-{{ $hiring->id }}')">
                                                                                    <i class="fas fa-fw fa-check"></i> Aktifkan
                                                                                </button>
                                                                            </form>
                                                                       @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Posting Lowongan -->
                            <div id="form-container" class="form-container"
                                style="border: 1px solid; padding: 50px; border-radius: 8px; display: none;">
                                <h4 class="mb-4 text-center font-weight-bold"
                                    style="font-size:1.725rem; color: black;">Posting Lowongan</h4>
                                <form method="POST" action="{{ route('perusahaan.lowongan.addLowongan') }}">
                                    @csrf

                                    <div class="form-group">
                                        <label for="position_hiring">Posisi Lowongan</label>
                                        <input type="text" class="form-control" name="position_hiring" id="position_hiring"
                                            placeholder="e.g. Web Developer" value="{{ old('position_hiring') }}" required />
                                    </div>

                                    <div class="form-group">
                                        <label for="address_hiring">Alamat Lengkap Lowongan</label>
                                        <input type="text" class="form-control" name="address_hiring" id="address_hiring"
                                            placeholder="e.g. Jl. Raya Darmo No.10, Surabaya" value="{{ old('address_hiring') }}" required />
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="work_system">Sistem Kerja</label>
                                            <input type="text" class="form-control" name="work_system" id="work_system"
                                                placeholder="e.g. Kontrak" value="{{ old('work_system') }}" required />
                                        </div>
                                        <div style="flex: 1;">
                                            <label for="pola_kerja">Pola Kerja</label>
                                            <input type="text" class="form-control" name="pola_kerja" id="pola_kerja"
                                                placeholder="e.g. WFO" value="{{ old('pola_kerja') }}" required />
                                        </div>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="jenis_pekerjaan">Jenis Pekerjaan</label>
                                            <input type="text" class="form-control" name="jenis_pekerjaan" id="jenis_pekerjaan"
                                                placeholder="e.g. Full-time" value="{{ old('jenis_pekerjaan') }}" required />
                                        </div>
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="gaji_min">Gaji Minimum</label>
                                            <input type="number" class="form-control" name="gaji_min" id="gaji_min"
                                                placeholder="e.g. 4500000" value="{{ old('gaji_min') }}" required />
                                        </div>
                                        <div style="flex: 1;">
                                            <label for="gaji_max">Gaji Maksimum</label>
                                            <input type="number" class="form-control" name="gaji_max" id="gaji_max"
                                                placeholder="e.g. 8000000" value="{{ old('gaji_max') }}" required />
                                        </div>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="ukuran_perusahaan">Ukuran Perusahaan</label>
                                            <input type="text" class="form-control" name="ukuran_perusahaan" id="ukuran_perusahaan"
                                                placeholder="e.g. Menengah" value="{{ old('ukuran_perusahaan') }}" required />
                                        </div>
                                        <div style="flex: 1;">
                                            <label for="sektor_industri">Sektor Industri</label>
                                            <input type="text" class="form-control" name="sektor_industri" id="sektor_industri"
                                                placeholder="e.g. Teknologi Informasi" value="{{ old('sektor_industri') }}" required />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="kualifikasi">Kualifikasi</label>
                                        <textarea class="form-control" name="kualifikasi" id="kualifikasi" rows="3"
                                                placeholder="e.g. S1 Ekonomi, memahami laporan keuangan" required>{{ old('kualifikasi') }}</textarea>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="pengalaman_minimal_tahun">Pengalaman Minimal (tahun)</label>
                                            <input type="number" class="form-control" name="pengalaman_minimal_tahun" id="pengalaman_minimal_tahun"
                                                placeholder="e.g. 2" value="{{ old('pengalaman_minimal_tahun') }}" required />
                                        </div>
                                        <div style="flex: 1;">
                                            <label for="usia_maksimal">Usia Maksimal</label>
                                            <input type="number" class="form-control" name="usia_maksimal" id="usia_maksimal"
                                                placeholder="e.g. 35" value="{{ old('usia_maksimal') }}" required />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="keterampilan_teknis">Keterampilan Teknis</label>
                                        <textarea class="form-control" name="keterampilan_teknis" id="keterampilan_teknis" rows="2"
                                                placeholder="e.g. Laravel, React.js, Database Management" required>{{ old('keterampilan_teknis') }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="keterampilan_non_teknis">Keterampilan Non-Teknis</label>
                                        <textarea class="form-control" name="keterampilan_non_teknis" id="keterampilan_non_teknis" rows="2"
                                                placeholder="e.g. Komunikasi, Manajemen Waktu" required>{{ old('keterampilan_non_teknis') }}</textarea>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="kota">Kota</label>
                                            <input type="text" class="form-control" name="kota" id="kota"
                                                placeholder="e.g. Surabaya" value="{{ old('kota') }}" required />
                                        </div>
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="provinsi">Provinsi</label>
                                            <input type="text" class="form-control" name="provinsi" id="provinsi"
                                                placeholder="e.g. Jawa Timur" value="{{ old('provinsi') }}" required />
                                        </div>
                                        <div style="flex: 1;">
                                            <label for="deadline_hiring">Batas Lowongan</label>
                                            <input type="date" class="form-control" name="deadline_hiring" id="deadline_hiring"
                                                value="{{ old('deadline_hiring') }}" required />
                                        </div>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <div style="flex: 1; margin-right: 10px;">
                                            <label for="latitude">Latitude</label>
                                            <input type="text" class="form-control" name="latitude" id="latitude"
                                                placeholder="e.g. -7.250445" value="{{ old('latitude') }}" required />
                                        </div>
                                        <div style="flex: 1;">
                                            <label for="longitude">Longitude</label>
                                            <input type="text" class="form-control" name="longitude" id="longitude"
                                                placeholder="e.g. 112.768845" value="{{ old('longitude') }}" required />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="education_hiring">Pendidikan</label>
                                        <input type="text" class="form-control" name="education_hiring" id="education_hiring"
                                            placeholder="e.g. S1 (Ekonomi)" value="{{ old('education_hiring') }}" required />
                                    </div>

                                    <div class="form-group">
                                        <label for="description_hiring">Deskripsi Pekerjaan & Persyaratan</label>
                                        <textarea class="form-control" name="description_hiring" id="description_hiring" rows="5"
                                                placeholder="Jelaskan detail pekerjaan, tanggung jawab, dan syarat..." required>{{ old('description_hiring') }}</textarea>
                                    </div>

                                    <button type="submit" id="btn-submit-lowongan" class="btn btn-primary d-block mx-auto mt-10">
                                        Buat Lowongan
                                    </button>
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

@push('scripts')
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "ordering": true,
            "lengthChange": true,
            "pageLength": 10
        });
    });

    function showForm(section) {
        const listContainer = document.getElementById('list-container');
        const formContainer = document.getElementById('form-container');
        const btnList = document.getElementById('btn-list');
        const btnForm = document.getElementById('btn-form');

        if (section === 'list') {
            formContainer.style.display = 'none';
            listContainer.style.display = 'block';

            // Ubah warna tombol
            btnList.classList.remove('text-secondary');
            btnList.classList.add('text-primary', 'font-weight-bold');
            btnForm.classList.remove('text-primary', 'font-weight-bold');
            btnForm.classList.add('text-secondary');
        } else if (section === 'form') {
            formContainer.style.display = 'block';
            listContainer.style.display = 'none';

            // Ubah warna tombol
            btnForm.classList.remove('text-secondary');
            btnForm.classList.add('text-primary', 'font-weight-bold');
            btnList.classList.remove('text-primary', 'font-weight-bold');
            btnList.classList.add('text-secondary');
        }
    }

    // Default buka List Lowongan
    document.addEventListener("DOMContentLoaded", function () {
        showForm('list');
    });

    function confirmNonaktifkan(formId) {
        Swal.fire({
            title: 'Yakin ingin menonaktifkan lowongan ini?',
            text: "Lowongan akan disembunyikan dari pelamar.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

    function confirmAktifkan(formId) {
        Swal.fire({
            title: 'Aktifkan kembali lowongan ini?',
            text: "Lowongan akan terlihat kembali oleh pelamar.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

    document.getElementById('btn-submit-lowongan').addEventListener('click', function (e) {
        e.preventDefault(); // cegah submit langsung

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Lowongan akan dipublikasikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, buat!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.closest('form').submit();
            }
        });
    });

    document.getElementById('btn-submit-lowongan').addEventListener('click', function (e) {
    e.preventDefault(); // cegah submit langsung

    // Ambil semua field wajib
    const fields = [
        { id: 'position_hiring', label: 'Posisi Lowongan' },
        { id: 'address_hiring', label: 'Alamat Lowongan' },
        { id: 'work_system', label: 'Sistem Kerja' },
        { id: 'pola_kerja', label: 'Pola Kerja' },
        { id: 'deadline_hiring', label: 'Batas Lowongan' },
        { id: 'education_hiring', label: 'Pendidikan' },
        { id: 'description_hiring', label: 'Deskripsi Pekerjaan & Persyaratan' }
    ];

    // Cek ada yang kosong?
    let emptyFields = [];
    fields.forEach(field => {
        const el = document.getElementById(field.id);
        if (!el.value.trim()) {
            emptyFields.push(field.label);
        }
    });

    if (emptyFields.length > 0) {
        Swal.fire({
            title: 'Form belum lengkap!',
            text: 'Harap isi semua kolom berikut: \n' + emptyFields.join(', '),
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return; // stop, jangan submit
    }

    // Kalau semua terisi → baru tampilkan konfirmasi submit
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Lowongan akan dipublikasikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, buat!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            this.closest('form').submit();
        }
    });
});

</script>

{{-- ✅ Notifikasi sukses sekali saja --}}
@if(session('success'))
<script>
    Swal.fire({
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonText: 'OK'
    });
</script>
@php
    session()->forget('success'); // hapus supaya tidak muncul lagi setelah refresh
@endphp
@endif
@endpush

