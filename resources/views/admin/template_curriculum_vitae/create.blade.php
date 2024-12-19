@extends('admin.master_admin')
@section('title', 'Tambah Template CV | CVRE GENERATE')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Tambah Template Curriculum Vitae</h1>

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-4 mb-4 text-center">Tambah Template Curriculum Vitae</h4>
                <form method="POST" action="{{route('admin.template_curriculum_vitae.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="template_curriculum_vitae_name" class="form-label">Nama Template</label>
                                <input type="text" class="form-control" name="template_curriculum_vitae_name" value="{{old('template_curriculum_vitae_name')}}" placeholder="Masukkan Nama Template" autofocus required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="thumbnail_curriculum_vitae" class="form-label">Gambar Template CV</label>
                                <input type="file" class="form-control" name="thumbnail_curriculum_vitae" required>
                            </div>
                        </div>
                    </div>

                    <button class="mt-4 btn btn-primary btn-lg" type="submit">Tambah Template CV</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection