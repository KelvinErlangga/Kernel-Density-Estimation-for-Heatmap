@extends('admin.master_admin')
@section('title', 'Data Keahlian | CVRE GENERATE')

@push('style')
<link rel="stylesheet" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}">
@endpush

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Data Keahlian</h1>

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="widget">
                    <div class="table-responsive">
                        <a href="{{route('admin.skills.create')}}" class="btn btn-primary mt-2 mb-1">Tambah Data Keahlian</a>

                        <table id="dataTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Keahlian</th>
                                    <th>Kategori Keahlian</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($skills as $skill)
                                <tr>
                                    <td>{{$skill->skill_name}}</td>
                                    <td>{{$skill->category_skill}}</td>
                                    <td>
                                        <div class="row">
                                            <a href="{{route('admin.skills.edit', $skill->id)}}" class="btn btn-outline-primary mr-2">
                                                <i class="far fa-fw fa-edit"></i>
                                                Edit
                                            </a>
                                            <form action="{{route('admin.skills.destroy', $skill->id)}}" method="POST">
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