@extends('admin.master_admin')
@section('title', 'Ubah Data Pekerjaan | CVRE GENERATE')

@section('content')
<div class="container-fluid">

    <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-body">
                <h4 class="mt-4 mb-4 text-center fw-bold text-primary">Ubah Data Pekerjaan</h4>

                <form method="POST" action="{{ route('admin.jobs.update', $job) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="job_name" class="form-label">Nama Pekerjaan</label>
                                <input type="text"
                                       class="form-control form-control-md"
                                       name="job_name"
                                       id="job_name"
                                       value="{{ $job->job_name }}"
                                       placeholder="Masukkan Nama Pekerjaan"
                                       autofocus required>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="mt-4 btn btn-primary btn-md px-3" type="submit">
                            <i class="fas fa-save me-2"></i> Ubah Pekerjaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
