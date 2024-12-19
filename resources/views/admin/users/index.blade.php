@extends('admin.master_admin')
@section('title', 'Data Pengguna | CVRE GENERATE')

@push('style')
<link rel="stylesheet" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}">
@endpush

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Data Pengguna</h1>

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="widget">
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Pengguna</th>
                                    <th>Email Pengguna</th>
                                    <th>Tanggal Pengguna Dibuat</th>
                                    <th>Tanggal Pengguna Terverifikasi</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{$user->name}}</td>
                                    <td>{{$user->email}}</td>
                                    <td>{{date('d-M-Y', strtotime($user->created_at))}}</td>

                                    @if($user->email_verified_at)
                                    <td>{{date('d-M-Y', strtotime($user->email_verified_at))}}</td>
                                    @else
                                    <td>Belum Terverifikasi</td>
                                    @endif

                                    <td>
                                        <div class="row">
                                            <form action="{{route('admin.destroy-user-admin', $user->id)}}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger">
                                                    <i class="fas fa-fw fa-trash"></i>
                                                    Delete
                                                </button>
                                            </form>
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
</div>
@endsection

@push('scripts')
<script src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>

@endpush