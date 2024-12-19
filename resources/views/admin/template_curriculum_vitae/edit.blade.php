@extends('admin.master_admin')
@section('title', 'Ubah Template CV | CVRE GENERATE')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Ubah Template Curriculum Vitae</h1>

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-4 mb-4 text-center">Ubah Template Curriculum Vitae</h4>
                <form method="POST" action="{{route('admin.template_curriculum_vitae.update', $templateCurriculumVitae)}}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="template_curriculum_vitae_name" class="form-label">Nama Template</label>
                                <input type="text" class="form-control" name="template_curriculum_vitae_name" value="{{$templateCurriculumVitae->template_curriculum_vitae_name}}" placeholder="Masukkan Nama Template" autofocus required>
                            </div>
                        </div>
                        <img src="{{Storage::url($templateCurriculumVitae->thumbnail_curriculum_vitae)}}" alt="{{$templateCurriculumVitae->template_curriculum_vitae_name}}">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="thumbnail_curriculum_vitae" class="form-label">Gambar Template CV</label>
                                <input type="file" class="form-control" name="thumbnail_curriculum_vitae">
                            </div>
                        </div>
                    </div>

                    <button class="mt-4 btn btn-primary btn-lg" type="submit">Ubah Template CV</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection