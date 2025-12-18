@extends('admin.master_admin')
@section('title', 'Ubah Rekomendasi Keahlian | CVRE GENERATE')

@section('content')
<div class="container-fluid">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-4 mb-4 text-center">Ubah Rekomendasi Keahlian</h4>

                {{-- TAMPILKAN ERROR VALIDASI BIAR KETAHUAN KENAPA "DIAM" --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.recommended_skills.update', $recommendedSkill->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="job_id" class="form-label">Pekerjaan</label>
                                <select class="form-control" id="job_id" name="job_id" required>
                                    @foreach($jobs as $job)
                                        <option value="{{ $job->id }}"
                                            {{ old('job_id', $recommendedSkill->job_id) == $job->id ? 'selected' : '' }}>
                                            {{ $job->job_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label for="skill_id" class="form-label">Rekomendasi Keahlian</label>
                                <select class="form-control" id="skill_id" name="skill_id" required>
                                    @foreach($skills as $skill)
                                        <option value="{{ $skill->id }}"
                                            {{ old('skill_id', $recommendedSkill->skill_id) == $skill->id ? 'selected' : '' }}>
                                            {{ $skill->skill_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <button class="mt-4 btn btn-primary btn-lg" type="submit">
                        Ubah Rekomendasi Keahlian
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
