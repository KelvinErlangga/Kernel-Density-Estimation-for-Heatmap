@extends('admin.master_admin')
@section('title', 'Ubah Template Cover Letter | CVRE GENERATE')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Ubah Template Cover Letter</h1>

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-4 mb-4 text-center">Ubah Template Cover Letter</h4>
                <form method="POST" action="{{route('admin.template_cover_letter.update', $templateCoverLetter)}}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="template_cover_letter_name" class="form-label">Nama Template</label>
                                <input type="text" class="form-control" name="template_cover_letter_name" value="{{$templateCoverLetter->template_cover_letter_name}}" placeholder="Masukkan Nama Template" autofocus required>
                            </div>
                        </div>
                        <img src="{{Storage::url($templateCoverLetter->thumbnail_cover_letter)}}" alt="{{$templateCoverLetter->template_cover_letter_name}}">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="thumbnail_cover_letter" class="form-label">Gambar Template</label>
                                <input type="file" class="form-control" name="thumbnail_cover_letter">
                            </div>
                        </div>
                    </div>

                    <button class="mt-4 btn btn-primary btn-lg" type="submit">Ubah Template</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection