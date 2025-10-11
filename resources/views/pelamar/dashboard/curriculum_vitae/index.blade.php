@extends('pelamar.dashboard.master_user')
@section('title', 'Curriculum Vitae | CVRE GENERATE')

@push('style')
<link href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" rel="stylesheet" />
<style>
    :root{
        --card-radius:16px;
        --soft-shadow:0 10px 24px rgba(19,26,53,.08);
        --soft-border:#e9edf4;
    }

    .card{
        border:1px solid var(--soft-border);
        border-radius:var(--card-radius);
        box-shadow:var(--soft-shadow);
    }

    /* Buat CV Baru */
    .cv-add{
        cursor:pointer;
        height:370px; width:220px;
        border:2px dashed #3b82f6;
        border-radius:var(--card-radius);
        background:#f9fafc;
        display:flex; flex-direction:column; align-items:center; justify-content:center;
        transition:.3s;
        margin-right: 20px;
    }
    .cv-add:hover{ background:#eff6ff; box-shadow:var(--soft-shadow); }
    .cv-add i{ font-size:2.5rem; margin-bottom:.5rem; color:#1f2937; }
    .cv-add h6{ font-weight:600; color:#1f2937; }

    /* CV Card */
    .cv-card{
        border:1px solid var(--soft-border);
        border-radius:var(--card-radius);
        overflow:hidden;
        background:#fff;
        transition:.2s;
        margin-right: 20px;
    }
    .cv-card img{ width:100%; height:auto; object-fit:cover; }
    .cv-card:hover{ box-shadow:var(--soft-shadow); transform:translateY(-2px); }

    .cv-meta{
        padding:.5rem .75rem;
        background:#f9fafc;
        font-size:.85rem;
        font-weight:600;
        text-align:center;
    }

    .cv-actions{
        display:flex;
        justify-content:center;
        gap:.5rem;
        padding:.6rem;
        background:#fff;
    }
    .cv-actions .btn{
        border-radius:8px;
        font-size:.85rem;
        padding:.3rem .8rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mt-4">

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h6 class="font-weight-bold text-dark mb-3">Mulai Buat Curriculum Vitae</h6>
            <div class="d-flex align-items-start gap-3 flex-wrap">

                <!-- Buat CV Baru -->
                <a href="{{route('pelamar.curriculum_vitae.index')}}" class="text-decoration-none">
                    <div class="cv-add">
                        <i class="fas fa-plus"></i>
                        <h6>Buat CV Baru</h6>
                    </div>
                </a>

                <!-- CV Preview -->
                @foreach($curriculumVitaes as $cv)
                <div class="cv-card" style="width:220px;">
                    <img src="{{Storage::url($cv->templateCV->thumbnail_curriculum_vitae)}}" alt="CV Preview">
                    <div class="cv-meta">
                        {{ $cv->templateCV->template_curriculum_vitae_name }}
                    </div>
                    <div class="cv-actions">
                        <form action="{{route('pelamar.curriculum_vitae.store')}}" method="POST">
                            @csrf
                            <input type="hidden" name="template_curriculum_vitae_id" value="{{$cv->templateCV->id}}">
                            <button class="btn btn-outline-primary btn-sm">Edit</button>
                        </form>
                        <form action="{{route('pelamar.dashboard.curriculum_vitae.delete', $cv->id)}}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm btn-delete">Hapus</button>
                        </form>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    // konfirmasi hapus
    document.querySelectorAll(".btn-delete").forEach(btn => {
        btn.addEventListener("click", e => {
            e.preventDefault();
            let form = btn.closest("form");
            Swal.fire({
                title: 'Yakin hapus CV ini?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil', text: "{{ session('success') }}", timer:2000, showConfirmButton:false });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Gagal', text: "{{ session('error') }}" });
    @endif
});
</script>
@endpush
