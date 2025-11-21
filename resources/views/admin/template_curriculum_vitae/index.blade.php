@extends('admin.master_admin')

@section('title', 'Template CV | CVRE GENERATE')

@push('style')
<link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
@endpush

@section('content')
<div class="container-fluid">

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="mb-0">Daftar Template CV</h5>
                    <a href="{{ route('admin.template_curriculum_vitae.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Tambah Template
                    </a>
                </div>

                <div class="table-responsive">
                    <table id="dataTable" class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" width="30%">Gambar Template</th>
                                <th class="text-center">Nama Template</th>
                                <th class="text-center" width="25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templateCurriculumVitaes as $templateCV)
                            <tr>
                                <td class="text-center">
                                    <img src="{{ Storage::url($templateCV->thumbnail_curriculum_vitae) }}"
                                         alt="{{ $templateCV->template_curriculum_vitae_name }}"
                                         class="img-fluid template-img">
                                </td>
                                <td class="align-middle text-center">
                                    <span class="font-weight-bold text-gray-800">
                                        {{ $templateCV->template_curriculum_vitae_name }}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
    <div class="action-buttons d-flex justify-content-center flex-wrap gap-2">

        {{-- ========== EDIT / EDIT VISUAL ========== --}}
        @php
            $type = strtolower($templateCV->template_type ?? '');
        @endphp

        @if($type === 'kreatif')
            {{-- KHUSUS TEMPLATE KREATIF: pakai builder visual --}}
            <a href="{{ route('admin.template_curriculum_vitae.edit_visual', $templateCV->id) }}"
               class="btn btn-primary btn-sm">
                <i class="far fa-edit"></i> Edit Visual
            </a>
        @else
            {{-- ATS & tipe lain: edit biasa seperti sebelumnya --}}
            <a href="{{ route('admin.template_curriculum_vitae.edit', $templateCV->id) }}"
               class="btn btn-outline-primary btn-sm">
                <i class="far fa-edit"></i> Edit
            </a>
        @endif

        {{-- ========== NONAKTIF / AKTIFKAN (TIDAK DIUBAH) ========== --}}
        @if(!$templateCV->trashed())
            <form action="{{ route('admin.template_curriculum_vitae.destroy', $templateCV->id) }}"
                  method="POST"
                  onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan template ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-ban"></i> Nonaktifkan
                </button>
            </form>
        @else
            <form action="{{ route('admin.template_curriculum_vitae.restore', $templateCV->id) }}"
                  method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-check"></i> Aktifkan
                </button>
            </form>
        @endif

    </div>
</td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> <!-- end table responsive -->

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "→",
                    "previous": "←"
                }
            },
            "ordering": true,
            "lengthChange": true,
            "pageLength": 10
        });
    });
</script>
@endpush
