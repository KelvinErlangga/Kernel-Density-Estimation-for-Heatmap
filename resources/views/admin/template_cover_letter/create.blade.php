@extends('admin.master_admin')
@section('title', 'Tambah Template Cover Letter | CVRE GENERATE')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Tambah Template Cover Letter</h1>

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-4 mb-4 text-center">Tambah Template Cover Letter</h4>
                <form method="POST" action="{{route('admin.template_cover_letter.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="template_cover_letter_name" class="form-label">Nama Template</label>
                                <input type="text" class="form-control" name="template_cover_letter_name" value="{{old('template_cover_letter_name')}}" placeholder="Masukkan Nama Template" autofocus required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="thumbnail_cover_letter" class="form-label">Gambar Template</label>
                                <input type="file" class="form-control" name="thumbnail_cover_letter" required>
                            </div>
                        </div>
                    </div>

                    <button class="mt-4 btn btn-primary btn-lg" type="submit">Tambah Template</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection