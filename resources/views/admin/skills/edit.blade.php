@extends('admin.master_admin')
@section('title', 'Ubah Data Keahlian | CVRE GENERATE')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Ubah Data Keahlian</h1>

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-4 mb-4 text-center">Ubah Data Keahlian</h4>
                <form method="POST" action="{{route('admin.skills.update', $skill)}}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="skill_name" class="form-label">Nama Keahlian</label>
                                <input type="text" class="form-control" name="skill_name" value="{{$skill->skill_name}}" placeholder="Masukkan Nama Keahlian" autofocus required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="category_skill" class="form-label">Kategori Skill</label>
                                <select class="form-control" id="category_skill" name="category_skill" required>
                                    <option value="{{$skill->category_skill}}">{{$skill->category_skill}}</option>
                                    @if($skill->category_skill == 'Hard Skill')
                                    <option value="Soft Skill">Soft Skill</option>
                                    @else
                                    <option value="Hard Skill">Hard Skill</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <button class="mt-4 btn btn-primary btn-lg" type="submit">Ubah Keahlian</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection