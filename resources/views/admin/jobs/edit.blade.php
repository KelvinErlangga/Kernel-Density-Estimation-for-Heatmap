@extends('admin.master_admin')
@section('title', 'Ubah Data Pekerjaan | CVRE GENERATE')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Ubah Data Pekerjaan</h1>

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-4 mb-4 text-center">Ubah Data Pekerjaan</h4>
                <form method="POST" action="{{route('admin.jobs.update', $job)}}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="job_name" class="form-label">Nama Pekerjaan</label>
                                <input type="text" class="form-control" name="job_name" value="{{$job->job_name}}" placeholder="Masukkan Nama Pekerjaan" autofocus required>
                            </div>
                        </div>
                    </div>

                    <button class="mt-4 btn btn-primary btn-lg" type="submit">Ubah Pekerjaan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection