@extends('admin.master_admin')
@section('title', 'Data Pengguna | CVRE GENERATE')

@push('style')
<link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
@endpush

@section('content')
<div class="container-fluid">

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="widget">

                    <!-- Header -->
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="mb-0">Daftar Data Pengguna</h5>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i> Tambah Data Pengguna
                        </a>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Pengguna</th>
                                    <th>Email Pengguna</th>
                                    <th>Role</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Tanggal Terverifikasi</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr id="row-{{ $user->id }}">
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                                    <td>{{ date('d-M-Y', strtotime($user->created_at)) }}</td>
                                    <td>
                                        @if($user->email_verified_at)
                                            {{ date('d-M-Y', strtotime($user->email_verified_at)) }}
                                        @else
                                            <span class="text-danger">Belum Terverifikasi</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons justify-content-center">
                                            @if(!$user->trashed())
                                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                                   class="btn btn-outline-primary">
                                                    <i class="far fa-edit"></i> Edit
                                                </a>
                                                <button class="btn btn-outline-danger btn-nonaktif" data-id="{{ $user->id }}">
                                                    <i class="fas fa-user-slash"></i> Nonaktifkan
                                                </button>
                                            @else
                                                <button class="btn btn-outline-success btn-restore" data-id="{{ $user->id }}">
                                                    <i class="fas fa-user-check"></i> Aktifkan
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
        let userId = $(this).data('id');
        Swal.fire({
            title: 'Yakin ingin menonaktifkan pengguna ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Nonaktifkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if(result.isConfirmed){
                $('<form action="/admin/users/'+userId+'" method="POST">@csrf @method("DELETE")</form>').appendTo('body').submit();
            }
        });
    });

    // Konfirmasi Restore
    $('.btn-restore').click(function(){
        let userId = $(this).data('id');
        Swal.fire({
            title: 'Yakin ingin mengaktifkan kembali pengguna ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Aktifkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if(result.isConfirmed){
                $('<form action="/admin/users/'+userId+'/restore" method="POST">@csrf</form>').appendTo('body').submit();
            }
        });
    });
});
</script>
@endpush
