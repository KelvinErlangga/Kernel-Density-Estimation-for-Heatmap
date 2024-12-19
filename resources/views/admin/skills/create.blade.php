@extends('admin.master_admin')
@section('title', 'Tambah Data Keahlian | CVRE GENERATE')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Tambah Data Keahlian</h1>

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-4 mb-4 text-center">Tambah Data Keahlian</h4>
                <form method="POST" action="{{route('admin.skills.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="skill_name" class="form-label">Nama Keahlian</label>
                                <input type="text" class="form-control" name="skill_name" value="{{old('skill_name')}}" placeholder="Masukkan Nama Keahlian" autofocus required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="category_skill" class="form-label">Kategori Skill</label>
                                <select class="form-control" id="category_skill" name="category_skill" required>
                                    <option value="">Pilih</option>
                                    <option value="Hard Skill">Hard Skill</option>
                                    <option value="Soft Skill">Soft Skill</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button class="mt-4 btn btn-primary btn-lg" type="submit">Tambah Keahlian</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection