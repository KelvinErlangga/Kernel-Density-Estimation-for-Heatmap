@extends('admin.master_admin')
@section('title', 'Ubah Template CV | CVRE GENERATE')

@section('content')
<div class="container-fluid">

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                <h5 class="mb-4 text-center text-primary font-weight-bold">
                    Form Ubah Template CV
                </h5>

                <form method="POST" action="{{ route('admin.template_curriculum_vitae.update', $templateCurriculumVitae->id) }}" enctype="multipart/form-data" id="templateForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Panel Kiri -->
                        <div class="col-md-4">
                            <h6 class="mb-3">Jenis Template CV</h6>
                            <div class="form-group">
                                <select class="form-control" name="template_type" id="template_type" required>
                                    <option value="">-- Pilih Jenis Template --</option>
                                    <option value="ats" {{ $templateCurriculumVitae->template_type == 'ats' ? 'selected' : '' }}>CV ATS</option>
                                    <option value="kreatif" {{ $templateCurriculumVitae->template_type == 'kreatif' ? 'selected' : '' }}>CV Kreatif</option>
                                </select>
                            </div>

                            <h6 class="mt-4 mb-2">Komponen CV</h6>
                            <small class="text-muted d-block mb-2">(Drag ke kanan untuk menyusun)</small>

                            <!-- Komponen ATS -->
                            <ul id="available-fields-ats" class="list-group template-fields d-none">
                                <li class="list-group-item" data-key="personal_detail-ats">Detail Pribadi</li>
                                <li class="list-group-item" data-key="experiences-ats">Pengalaman Kerja</li>
                                <li class="list-group-item" data-key="educations-ats">Pendidikan</li>
                                <li class="list-group-item" data-key="languages-ats">Bahasa</li>
                                <li class="list-group-item" data-key="skills-ats">Keahlian</li>
                                <li class="list-group-item" data-key="organizations-ats">Organisasi</li>
                                <li class="list-group-item" data-key="achievements-ats">Prestasi</li>
                                <li class="list-group-item" data-key="links-ats">Link Informasi</li>
                            </ul>

                            <!-- Komponen Kreatif -->
                            <ul id="available-fields-kreatif" class="list-group template-fields d-none">
                                <li class="list-group-item" data-key="personal_detail-kreatif">Detail Pribadi</li>
                                <li class="list-group-item" data-key="experiences-kreatif">Pengalaman Kerja</li>
                                <li class="list-group-item" data-key="educations-kreatif">Pendidikan</li>
                                <li class="list-group-item" data-key="languages-kreatif">Bahasa</li>
                                <li class="list-group-item" data-key="skills-kreatif">Keahlian</li>
                                <li class="list-group-item" data-key="organizations-kreatif">Organisasi</li>
                                <li class="list-group-item" data-key="achievements-kreatif">Prestasi</li>
                                <li class="list-group-item" data-key="links-kreatif">Link Informasi</li>
                            </ul>

                            <p class="small text-muted mt-3">
                                <strong>Catatan:</strong> Komponen hanya bisa ditambahkan sekali. Hapus dari canvas untuk mengaktifkan kembali.
                            </p>
                        </div>

                        <!-- Panel Kanan -->
                        <div class="col-md-8">
                            <h6 class="mb-3">Preview Template CV</h6>
                            <div id="cv-canvas" class="bg-white p-4" style="min-height:600px; max-width:820px; margin:auto;">
                                <div id="cv-canvas-inner">
                                    <p class="empty-placeholder text-muted">
                                        (Seret komponen dari kiri ke area ini)
                                    </p>
                                </div>
                            </div>

                            <input type="hidden" name="layout_json" id="layout_json" value="{{ $templateCurriculumVitae->layout_json }}">
                            <input type="hidden" name="style_json" id="style_json" value="{{ $templateCurriculumVitae->style_json }}">
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- Nama Template -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="template_curriculum_vitae_name">Nama Template</label>
                                <input type="text" class="form-control" name="template_curriculum_vitae_name"
                                       value="{{ old('template_curriculum_vitae_name', $templateCurriculumVitae->template_curriculum_vitae_name) }}" required>
                            </div>
                        </div>

                        <!-- Thumbnail -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="thumbnail_curriculum_vitae">Thumbnail Template CV</label>
                                <input type="file" class="form-control" name="thumbnail_curriculum_vitae">
                                @if($templateCurriculumVitae->thumbnail_curriculum_vitae)
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($templateCurriculumVitae->thumbnail_curriculum_vitae) }}" width="120" class="img-thumbnail">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary btn-lg mt-3">
                            <i class="fas fa-save mr-1"></i> Update Template
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

{{-- semua komponen template --}}
@include('admin.template_curriculum_vitae.templates')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const templateType = @json($templateCurriculumVitae->template_type);
    let layoutData = [];

    try {
        layoutData = JSON.parse(@json($templateCurriculumVitae->layout_json ?? '[]'));
    } catch (e) {
        layoutData = [];
    }

    const canvas = document.getElementById('cv-canvas-inner');
    const typeSelect = document.getElementById('template_type');

    // --- Tampilkan list kiri sesuai type
    function showAvailableFields(type) {
        document.querySelectorAll('.template-fields').forEach(el => el.classList.add('d-none'));
        const list = document.getElementById("available-fields-" + type);
        if (list) list.classList.remove("d-none");
    }

    // --- Render layout dari DB
    function renderLayout() {
        if (layoutData.length > 0) {
            canvas.innerHTML = '';
            layoutData.forEach(item => {
                const tpl = document.getElementById('tpl-' + item.key);
                if (tpl) {
                    const clone = tpl.content.cloneNode(true);
                    canvas.appendChild(clone);

                    // disable di kiri
                    const li = document.querySelector(`[data-key="${item.key}"]`);
                    if (li) {
                        li.classList.add('disabled');
                        li.style.pointerEvents = 'none';
                        li.style.color = '#b0b0b0';
                        li.style.backgroundColor = '#f8f9fa';
                    }
                }
            });
        } else {
            // kalau layout kosong (create baru)
            canvas.innerHTML = '<p class="empty-placeholder text-muted">(Seret komponen dari kiri ke area ini)</p>';
        }
    }

    // --- init saat pertama buka edit
    typeSelect.value = templateType;
    showAvailableFields(templateType);
    renderLayout();

    // --- handler kalau dropdown diubah manual ---
    typeSelect.addEventListener("change", function () {
        const selected = this.value;
        showAvailableFields(selected);

        // Hanya reset canvas kalau memang layout kosong (mode create baru)
        if (layoutData.length === 0) {
            canvas.innerHTML = '<p class="empty-placeholder text-muted">(Seret komponen dari kiri ke area ini)</p>';
        } else {
            // kalau edit, tetap render ulang layoutData
            renderLayout();
        }
    });
});
</script>
@endpush

