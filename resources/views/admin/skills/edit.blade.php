@extends('admin.master_admin')
@section('title', 'Ubah Data Keahlian | CVRE GENERATE')

@section('content')
<div class="container-fluid">

    <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-body">
                <h4 class="mt-4 mb-4 text-center fw-bold text-primary">Ubah Data Keahlian</h4>

                <form method="POST" action="{{ route('admin.skills.update', $skill) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="skill_name" class="form-label">Nama Keahlian</label>
                                <input type="text"
                                       class="form-control form-control-md"
                                       name="skill_name"
                                       id="skill_name"
                                       value="{{ $skill->skill_name }}"
                                       placeholder="Masukkan Nama Keahlian"
                                       autofocus required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="category_skill" class="form-label">Kategori Skill</label>
                                <select class="form-control form-control-md"
                                        id="category_skill"
                                        name="category_skill"
                                        required>
                                    <option value="{{ $skill->category_skill }}">{{ $skill->category_skill }}</option>
                                    @if($skill->category_skill == 'Hard Skill')
                                        <option value="Soft Skill">Soft Skill</option>
                                    @else
                                        <option value="Hard Skill">Hard Skill</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="mt-4 btn btn-primary btn-md px-3" type="submit">
                            <i class="fas fa-save me-2"></i> Ubah Keahlian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
