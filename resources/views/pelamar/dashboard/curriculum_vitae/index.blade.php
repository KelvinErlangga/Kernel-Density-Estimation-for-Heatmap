@extends('pelamar.dashboard.master_user')
@section('title', 'Curriculum Vitae | CVRE GENERATE')

@push('style')
<link href="{{asset('css/sb-admin-2.css')}}" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" rel="stylesheet" />

<style>
    a:hover {
        text-decoration: none;
        /* Menghilangkan garis bawah saat di-hover */
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h5 class="mb-0 font-weight-bold text-primary">Dashboard / Kelola / <span class="text-dark">Curiculum Vitae</span></h5>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-xl-12 col-lg-7">
            <div class="card shadow mb-4">
                <section class="p-8">
                    <h6 class="font-weight-bold text-dark">Mulai Buat Curiculum Vitae</h6>
                    <div class="d-flex align-items-start mt-3">
                        <a href="{{route('pelamar.curriculum_vitae.index')}}">
                            <div
                                class="swiper-slide text-center border border-dashed border-primary me-3 mr-3 bg-light d-flex flex-column align-items-center justify-content-center shadow-sm"
                                style="cursor: pointer; height: 275px; width: 200px; transition: all 0.3s ease"
                                onmouseover="this.classList.add('shadow-lg', 'text-primary')"
                                onmouseout="this.classList.remove('shadow-lg', 'text-primary')">
                                <i class="fas fa-plus fa-3x text-dark mb-2"></i>
                                <h6 class="fw-bold text-dark text-center m-2">Buat Curriculum Vitae</h6>
                            </div>
                        </a>

                        <!-- CV Slider -->
                        <div class="swiper mySwiper w-full">
                            <div class="swiper-wrapper">
                                <!-- Loop through images -->
                                @foreach($curriculumVitaes as $cv)
                                <div class="swiper-slide">
                                    <img src="{{Storage::url($cv->templateCV->thumbnail_curriculum_vitae)}}" alt="CV Preview 1" class="rounded-lg shadow-md w-100 h-auto" style="object-fit: cover" />
                                    <div class="overlay">
                                        <span>{{$cv->templateCV->template_curriculum_vitae_name}}</span>
                                    </div>
                                    <div class="hover-buttons">
                                        <form action="{{route('pelamar.curriculum_vitae.store')}}" method="POST">
                                            @csrf
                                            <input type="hidden" name="template_curriculum_vitae_id" id="template_curriculum_vitae_id" value="{{$cv->templateCV->id}}">

                                            <button class="btn btn-danger">
                                                Edit CV
                                            </button>
                                        </form>

                                        <form action="{{route('pelamar.dashboard.curriculum_vitae.delete', $cv->id)}}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger">
                                                Hapus CV
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

<!-- Swiper.js Initialization -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Call to Action CV Section Swiper
        const ctaSwiper = new Swiper(".mySwiper", {
            slidesPerView: 4,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 6000,
            },
        });
    });
</script>
@endpush