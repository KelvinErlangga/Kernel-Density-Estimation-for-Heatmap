@extends('admin.master_admin')
@section('title', 'Data Pekerjaan | CVRE GENERATE')

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
                        <h5 class="mb-0">Daftar Data Pekerjaan</h5>
                        <a href="{{route('admin.jobs.create')}}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i> Tambah Data Pekerjaan
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table id="dataTable" class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Pekerjaan</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobs as $job)
                                <tr>
                                    <td>{{$job->job_name}}</td>
                                    <td class="text-center">
                                        <div class="action-buttons justify-content-center">
                                            @if(is_null($job->deleted_at))
                                                <a href="{{route('admin.jobs.edit', $job->id)}}" class="btn btn-outline-primary">
                                                    <i class="far fa-edit"></i> Edit
                                                </a>
                                                <form action="{{route('admin.jobs.destroy', $job->id)}}" method="POST" class="d-inline" onsubmit="return confirm('Yakin nonaktifkan data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-ban"></i> Nonaktifkan
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{route('admin.jobs.restore', $job->id)}}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success">
                                                        <i class="fas fa-check"></i> Aktifkan Kembali
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
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

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
    });
</script>
@endpush
