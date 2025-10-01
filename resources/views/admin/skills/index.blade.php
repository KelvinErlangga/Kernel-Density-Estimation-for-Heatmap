@extends('admin.master_admin')
@section('title', 'Data Keahlian | CVRE GENERATE')

@push('style')
<link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
@endpush

@section('content')
<div class="container-fluid">

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="widget">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="mb-0">Daftar Data Keahlian</h5>
                        <a href="{{ route('admin.skills.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i> Tambah Data Keahlian
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table id="dataTable" class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Keahlian</th>
                                    <th>Kategori Keahlian</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($skills as $skill)
                                <tr>
                                    <td>{{ $skill->skill_name }}</td>
                                    <td>{{ $skill->category_skill }}</td>
                                    <td class="text-center">
                                        <div class="action-buttons justify-content-center">
                                            @if(is_null($skill->deleted_at))
                                                <a href="{{ route('admin.skills.edit', $skill->id) }}" class="btn btn-outline-primary">
                                                    <i class="far fa-edit"></i> Edit
                                                </a>
                                                <button class="btn btn-outline-danger btn-nonaktif" data-id="{{ $skill->id }}">
                                                    <i class="fas fa-ban"></i> Nonaktifkan
                                                </button>
                                            @else
                                                <button class="btn btn-outline-success btn-restore" data-id="{{ $skill->id }}">
                                                    <i class="fas fa-check"></i> Aktifkan Kembali
                                                </button>
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
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        }
    });

    // SweetAlert2 Notifikasi
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sukses!',
            text: "{{ session('success') }}",
            timer: 2500,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}",
            timer: 3500,
            showConfirmButton: false
        });
    @endif

    // Konfirmasi Nonaktifkan
    $('.btn-nonaktif').click(function(){
        let skillId = $(this).data('id');
        Swal.fire({
            title: 'Yakin ingin menonaktifkan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Nonaktifkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if(result.isConfirmed){
                $('<form action="/admin/skills/'+skillId+'" method="POST">@csrf @method("DELETE")</form>').appendTo('body').submit();
            }
        });
    });

    // Konfirmasi Restore
    $('.btn-restore').click(function(){
        let skillId = $(this).data('id');
        Swal.fire({
            title: 'Yakin ingin mengaktifkan kembali?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Aktifkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if(result.isConfirmed){
                $('<form action="/admin/skills/'+skillId+'/restore" method="POST">@csrf</form>').appendTo('body').submit();
            }
        });
    });

});
</script>
@endpush
