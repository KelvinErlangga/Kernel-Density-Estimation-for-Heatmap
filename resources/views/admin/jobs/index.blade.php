@extends('admin.master_admin')
@section('title', 'Data Pekerjaan | CVRE GENERATE')

@push('style')
<link rel="stylesheet" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}">
@endpush

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Data Pekerjaan</h1>

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="widget">
                    <div class="table-responsive">
                        <a href="{{route('admin.jobs.create')}}" class="btn btn-primary mt-2 mb-1">Tambah Data Pekerjaan</a>

                        <table id="dataTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Pekerjaan</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobs as $job)
                                <tr>
                                    <td>{{$job->job_name}}</td>
                                    <td>
                                        <div class="row">
                                            <a href="{{route('admin.jobs.edit', $job->id)}}" class="btn btn-outline-primary mr-2">
                                                <i class="far fa-fw fa-edit"></i>
                                                Edit
                                            </a>
                                            <form action="{{route('admin.jobs.destroy', $job->id)}}" method="POST">
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