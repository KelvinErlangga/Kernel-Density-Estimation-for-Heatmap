@extends('admin.master_admin')
@section('title', 'Tambah Template CV | CVRE GENERATE')

@section('content')
<div class="container-fluid">

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                <h5 class="mb-4 text-center text-primary font-weight-bold">
                    Form Tambah Template CV
                </h5>

                <form method="POST" action="{{ route('admin.template_curriculum_vitae.store') }}" enctype="multipart/form-data" id="templateForm">
                    @csrf

                    <div class="row">
                        <!-- Panel Kiri -->
                        <div class="col-md-4">
                            <h6 class="mb-3">Pilih Jenis Template CV</h6>
                            <div class="form-group">
                                <select class="form-control" name="template_type" id="template_type" required>
                                    <option value="">-- Pilih Jenis Template --</option>
                                    <option value="ats">CV ATS</option>
                                    <option value="kreatif">CV Kreatif</option>
                                </select>
                            </div>

                            {{-- Kontrol builder (hanya untuk ATS) --}}
                            <div id="builderControls" class="mt-4 d-none">
                                <h6 class="mb-2">Komponen CV</h6>
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
                                    <li class="list-group-item" data-key="custom-ats">Section Bebas</li>
                                </ul>

                                <!-- Komponen Kreatif (TIDAK dipakai di halaman ini, dibiarkan saja) -->
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
                                    <strong>Catatan:</strong> Komponen hanya bisa ditambahkan sekali (kecuali Section Bebas).
                                    Hapus dari canvas untuk mengaktifkan kembali.
                                </p>
                            </div>
                        </div>

                        <!-- Panel Kanan -->
                        <div class="col-md-8">
                            {{-- Wrapper builder canvas (hanya untuk ATS) --}}
                            <div id="builderCanvasWrapper" class="d-none">
                                <h6 class="mb-3">Preview Template CV</h6>
                                <div id="cv-canvas" class="bg-white p-4" style="min-height:600px; max-width:820px; margin:auto;">
                                    <div id="cv-canvas-inner">
                                        <p class="empty-placeholder text-muted">
                                            (Seret komponen dari kiri ke area ini)
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Hidden input layout & style --}}
                            <input type="hidden" name="layout_json" id="layout_json">
                            <input type="hidden" name="style_json" id="style_json">
                        </div>
                    </div>

                    {{-- Info khusus untuk template kreatif --}}
                    <div class="alert alert-info mt-3 d-none" id="kreatifInfo">
                        Untuk <strong>CV Kreatif</strong>, tata letak dan desain visual akan diatur
                        menggunakan <strong>Editor Visual</strong> setelah template disimpan.
                        Setelah klik <em>Simpan Template</em>, buka menu
                        <strong>Daftar Template CV</strong> lalu klik tombol <em>Edit Visual</em>.
                    </div>

                    <div class="row mt-4">
                        <!-- Nama Template -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="template_curriculum_vitae_name">Nama Template</label>
                                <input type="text" class="form-control" name="template_curriculum_vitae_name"
                                       value="{{ old('template_curriculum_vitae_name') }}" placeholder="Masukkan Nama Template" required>
                            </div>
                        </div>

                        <!-- Thumbnail -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="thumbnail_curriculum_vitae">Thumbnail Template CV</label>
                                <input type="file" class="form-control" name="thumbnail_curriculum_vitae" required>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary btn-lg mt-3">
                            <i class="fas fa-save mr-1"></i> Simpan Template
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- Templates CV ATS (sample HTML untuk preview) -->
<template id="tpl-personal_detail-ats">
    <div class="cv-section" data-key="personal_detail" style="position:relative; padding:12px 0;">
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
        <h1 style="margin:0; font-size:22px; text-align:center;">ANINDITA PUTRI</h1>
        <p style="margin:6px 0 0 0; font-size:13px; text-align:center; color:#555;">
            Padang, Sumatra Barat, Indonesia • anindita.pu@kitalulus.com • +62812-3456-7890
        </p>
        <hr style="margin-top:12px;">
    </div>
</template>

<template id="tpl-experiences-ats">
    <div class="cv-section" data-key="experiences" style="position:relative; padding:8px 0;">
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
        <h4 style="margin:0 0 6px 0; border-bottom:2px solid #111; padding-bottom:6px;">PENGALAMAN KERJA</h4>
        <div style="font-size:14px;">
            <strong>PT. BANK RAKYAT PADANG</strong>
            <div><em>Back Office Assistant</em> <span style="float:right;">Padang, 2021</span></div>
            <ul style="margin-top:6px;">
                <li>Memonitoring dan melakukan pengecekan terhadap invoice serta pemasaran</li>
                <li>Bertanggung jawab terhadap pengelolaan laporan keuangan harian</li>
                <li>Mengendalikan prosedur pembayaran</li>
            </ul>
        </div>
        <hr>
    </div>
</template>

<template id="tpl-educations-ats">
    <div class="cv-section" data-key="educations" style="position:relative; padding:8px 0;">
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
        <h4 style="margin:0 0 6px 0; border-bottom:2px solid #111; padding-bottom:6px;">EDUCATION</h4>
        <div style="font-size:14px;">
            <strong>SMKN PEMBANGUNAN PELITA</strong>
            <div style="font-size:12px; color:#666; float:right;">Padang, Sumatra Barat — 2016–2019</div>
            <p style="margin-top:6px;">Administrasi Perkantoran</p>
            <ul>
                <li>Lulusan terbaik tingkat kota</li>
                <li>Juara 1 Lomba Kompetensi Siswa</li>
            </ul>
        </div>
        <div style="clear:both"></div>
        <hr>
    </div>
</template>

<template id="tpl-languages-ats">
    <div class="cv-section" data-key="languages" style="position:relative; padding:8px 0;">
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
        <h4 style="margin:0 0 6px 0; border-bottom:2px solid #111; padding-bottom:6px;">BAHASA</h4>
        <ul style="font-size:14px; margin-top:6px;">
            <li>Bahasa Indonesia — Native</li>
            <li>English — Intermediate</li>
        </ul>
        <hr>
    </div>
</template>

<template id="tpl-skills-ats">
    <div class="cv-section" data-key="skills" style="position:relative; padding:8px 0;">
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
        <h4 style="margin:0 0 6px 0; border-bottom:2px solid #111; padding-bottom:6px;">KEAHLIAN</h4>
        <ul style="font-size:14px; margin-top:6px; columns:2;">
            <li>Public Speaking</li>
            <li>MS Office</li>
            <li>MS Excel</li>
            <li>MS PowerPoint</li>
            <li>Problem Solving</li>
            <li>Time Management</li>
        </ul>
        <hr>
    </div>
</template>

<template id="tpl-organizations-ats">
    <div class="cv-section" data-key="organizations" style="position:relative; padding:8px 0;">
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
        <h4 style="margin:0 0 6px 0; border-bottom:2px solid #111; padding-bottom:6px;">PENGALAMAN ORGANISASI</h4>
        <div style="font-size:14px;">
            <strong>PADANG FEST</strong>
            <div><em>Wakil Ketua Umum</em> <span style="float:right;">Padang, 2020</span></div>
            <ul style="margin-top:6px;">
                <li>Pengawasan kinerja panitia divisi Administrasi</li>
                <li>Mengkoordinasi kegiatan</li>
            </ul>
        </div>
        <div style="clear:both"></div>
        <hr>
    </div>
</template>

<template id="tpl-achievements-ats">
    <div class="cv-section" data-key="achievements" style="position:relative; padding:8px 0;">
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
        <h4 style="margin:0 0 6px 0; border-bottom:2px solid #111; padding-bottom:6px;">PRESTASI</h4>
        <ul style="font-size:14px;">
            <li>Juara 1 Lomba Karya Tulis Ilmiah — 2019</li>
            <li>Beasiswa Prestasi — 2020</li>
        </ul>
        <hr>
    </div>
</template>

<template id="tpl-links-ats">
    <div class="cv-section" data-key="links" style="position:relative; padding:8px 0;">
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
        <h4 style="margin:0 0 6px 0; border-bottom:2px solid #111; padding-bottom:6px;">LINK INFORMASI</h4>
        <ul style="font-size:14px;">
            <li><a href="#" target="_blank">LinkedIn / Portfolio</a></li>
            <li><a href="#" target="_blank">GitHub</a></li>
        </ul>
        <hr>
    </div>
</template>

<!-- Section Bebas (ATS) -->
<template id="tpl-custom-ats">
  <div class="cv-section" data-key="custom" style="position:relative; padding:8px 0;">
    <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section"
            style="position:absolute; right:0; top:0;">×</button>

    <!-- Judul bebas -->
    <h4 contenteditable="true"
        class="editable"
        data-section="customs"
        data-field="title_custom"
        style="margin:0 0 6px 0; border-bottom:2px solid #111; padding-bottom:6px;">
      JUDUL SECTION (klik untuk ubah)
    </h4>

    <!-- Isi bebas -->
    <div contenteditable="true"
         class="editable"
         data-section="customs"
         data-field="content_custom"
         style="font-size:14px; line-height:1.5; text-align:justify;">
      Tulis apa saja di sini. Tekan Enter untuk baris baru.
    </div>

    <hr style="margin-top:8px;">
  </div>
</template>
<!-- End of Templates CV ATS -->

<!-- Templates CV Kreatif (tidak dipakai di halaman ini, hanya disimpan) -->
<template id="tpl-personal_detail-kreatif">
    <div class="cv-section" data-key="personal_detail" style="position:relative;">
        <div class="profile-section">
            <div class="profile-img">
                <img src="https://via.placeholder.com/55x55.png?text=Foto" alt="foto">
            </div>
            <div class="profile-info">
                <p class="name" style="color:#000 !important; background:none !important; font-weight:bold !important;">Rick Tang</p>
                <p class="role" style="color:#000 !important; background:none !important; font-weight:bold !important;">Product Designer</p>
            </div>
        </div>
        <div class="details-section">
            <p class="details-title" style="color:#000 !important; background:none !important; font-weight:bold !important;">Details</p>
            <div class="detail-item">
                <p class="sub-menu" style="color:#000 !important; background:none !important; font-weight:bold !important;">Address</p>
                <p style="color:#000 !important; background:none !important; font-weight:bold !important;">San Francisco, California</p>
            </div>
            <div class="detail-item">
                <p class="sub-menu" style="color:#000 !important; background:none !important; font-weight:bold !important;">Phone</p>
                <p style="color:#000 !important; background:none !important; font-weight:bold !important;">(315) 802-8179</p>
            </div>
            <div class="detail-item">
                <p class="sub-menu" style="color:#000 !important; background:none !important; font-weight:bold !important;">Email</p>
                <p style="color:#000 !important; background:none !important; font-weight:bold !important;">ricktang@gmail.com</p>
            </div>
        </div>
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
    </div>
</template>

<template id="tpl-links-kreatif">
    <div class="cv-section" data-key="links" style="position:relative;">
        <div class="links-section">
            <p class="links-title" style="color:#000 !important; background:none !important; font-weight:bold !important;">Links</p>
            <div class="link-item">
                <a href="#"><p class="sub-menu" style="color:#000 !important; background:none !important; font-weight:bold !important;">LinkedIn</p></a>
                <a href="#"><p class="sub-menu" style="color:#000 !important; background:none !important; font-weight:bold !important;">Dribbble</p></a>
                <a href="#"><p class="sub-menu" style="color:#000 !important; background:none !important; font-weight:bold !important;">Behance</p></a>
            </div>
        </div>
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
    </div>
</template>

<template id="tpl-skills-kreatif">
    <div class="cv-section" data-key="skills" style="position:relative;">
        <div class="skills-section">
            <p class="skills-title" style="color:#000 !important; background:none !important; font-weight:bold !important;">Skills</p>
            <div class="skill-item"><p class="sub-menu" style="color:#000 !important; background:none !important; font-weight:bold !important;">Figma</p><div class="skill-divider"></div></div>
            <div class="skill-item"><p class="sub-menu" style="color:#000 !important; background:none !important; font-weight:bold !important;">Sketch</p><div class="skill-divider"></div></div>
            <div class="skill-item"><p class="sub-menu" style="color:#000 !important; background:none !important; font-weight:bold !important;">Adobe Photoshop</p><div class="skill-divider"></div></div>
            <div class="skill-item"><p class="sub-menu" style="color:#000 !important; background:none !important; font-weight:bold !important;">Adobe Illustrator</p><div class="skill-divider"></div></div>
            <div class="skill-item"><p class="sub-menu" style="color:#000 !important; background:none !important; font-weight:bold !important;">Principle</p><div class="skill-divider"></div></div>
            <div class="skill-item"><p class="sub-menu" style="color:#000 !important; background:none !important; font-weight:bold !important;">Adobe XD</p><div class="skill-divider"></div></div>
        </div>
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
    </div>
</template>

<template id="tpl-profile-kreatif">
    <div class="cv-section" data-key="profile" style="position:relative;">
        <h1>Profile</h1>
        <p>
            I'm a product designer focused on ensuring great user experience and meeting business needs of designed products. I'm also experienced in implementing marketing strategies and developing both on and offline campaigns. My philosophy is to make products understandable, useful and long-lasting at the same time recognizing they're never finished and constantly changing. I'm always excited to face new challenges and problems.
        </p>
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
    </div>
</template>

<template id="tpl-experiences-kreatif">
    <div class="cv-section" data-key="experiences" style="position:relative;">
        <h1>Experience</h1>
        <div>
            <h2>Uber</h2>
            <h3>Product Designer</h3>
            <p>Mar 2015 - Present</p>
            <ul>
                <li>Designed safety-focused experiences for Riders and Drivers</li>
                <li>Physical space problem solving and it's interaction with the digital</li>
                <li>Navigated organization to achieve operational improvements</li>
            </ul>
        </div>
        <div>
            <h2>IFTTT</h2>
            <h3>Product Designer</h3>
            <p>Dec 2013 - Mar 2015</p>
            <ul>
                <li>Product and system design for a complex product</li>
                <li>Designed both consumer and developer products for IFTTT</li>
                <li>Responsible for maintaining design across iOS, Android, and web</li>
            </ul>
        </div>
        <div>
            <h2>Facebook</h2>
            <h3>Product Designer</h3>
            <p>June 2013 - Sep 2013</p>
            <ul>
                <li>Designer and prototyped internal tools</li>
                <li>Worked with Privacy team to build assets and features</li>
                <li>Redesigned Newsfeed curation experience for mobile</li>
            </ul>
        </div>
        <div>
            <h2>Google Maps</h2>
            <h3>UX/UI Design Intern</h3>
            <p>June 2012 - Sep 2013</p>
            <ul>
                <li>Contributed to Maps on iOS wireframe ans user experience</li>
                <li>Designed and prototyped onboarding experience</li>
                <li>Asset and feature design for Maps on Android</li>
            </ul>
        </div>
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
    </div>
</template>

<template id="tpl-educations-kreatif">
    <div class="cv-section" data-key="educations" style="position:relative;">
        <h1>Education</h1>
        <div>
            <h2>Brown University</h2>
            <p>Interdisciplinary studies, Sep 2010 - May 2013</p>
        </div>
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
    </div>
</template>

<template id="tpl-languages-kreatif">
    <div class="cv-section" data-key="languages" style="position:relative;">
        <h1>Languages</h1>
        <ul>
            <li>English</li>
            <li>Italian</li>
        </ul>
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
    </div>
</template>

<template id="tpl-organizations-kreatif">
    <div class="cv-section" data-key="organizations" style="position:relative;">
        <h1>Organizations</h1>
        <div>
            <h2>PADANG FEST</h2>
            <h3>Wakil Ketua Umum</h3>
            <p>Padang, 2020</p>
            <ul>
                <li>Pengawasan kinerja panitia divisi Administrasi</li>
                <li>Mengkoordinasi kegiatan</li>
            </ul>
        </div>
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
    </div>
</template>

<template id="tpl-achievements-kreatif">
    <div class="cv-section" data-key="achievements" style="position:relative;">
        <h1>Achievements</h1>
        <ul>
            <li>Juara 1 Lomba Karya Tulis Ilmiah — 2019</li>
            <li>Beasiswa Prestasi — 2020</li>
        </ul>
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
    </div>
</template>
<!-- End Templates CV Kreatif -->

@endsection

@push('styles')
<style>
    /* disabled state kiri */
    #available-fields-ats .list-group-item.disabled {
        cursor: not-allowed !important;
        opacity: 0.6;
        color: #9aa0a6 !important;
        background-color: #f7f7f7;
    }

    /* canvas section spacing */
    #cv-canvas-inner .cv-section {
        margin-bottom: 12px;
    }

    /* placeholder */
    #cv-canvas-inner .empty-placeholder {
        color: #9aa0a6;
        font-size: 14px;
    }

    .remove-section {
        line-height: 0.8;
        padding: 0 6px;
        border-radius: 3px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const templateTypeSelect   = document.getElementById("template_type");
    const canvas               = document.getElementById('cv-canvas-inner');
    const layoutInput          = document.getElementById('layout_json');
    const styleInput           = document.getElementById('style_json');
    const form                 = document.getElementById('templateForm');
    const builderControls      = document.getElementById('builderControls');
    const builderCanvasWrapper = document.getElementById('builderCanvasWrapper');
    const kreatifInfo          = document.getElementById('kreatifInfo');

    const baseFrom  = (fullKey) => (fullKey || '').split('-')[0];   // "custom-ats" -> "custom"
    const isCustom  = (fullKeyOrBase) => baseFrom(fullKeyOrBase) === 'custom';

    // Sortable untuk canvas (drop target)
    Sortable.create(canvas, {
        group: { name: 'cv-sections', pull: false, put: true },
        animation: 150,
        onAdd: function(evt) {
            const li  = evt.item;
            const key = li.dataset.key;   // contoh: "experiences-ats" / "custom-ats"
            li.remove();

            const base = baseFrom(key);
            // cegah duplikasi hanya untuk NON-custom
            if (!isCustom(base) && canvas.querySelector(`.cv-section[data-key="${base}"]`)) {
                updateLayoutJSON();
                return;
            }

            const tpl = document.getElementById('tpl-' + key);
            if (!tpl) {
                console.warn('Template not found for', key);
                return;
            }

            const placeholder = canvas.querySelector('.empty-placeholder');
            if (placeholder) placeholder.remove();

            const clone = tpl.content.cloneNode(true);
            canvas.appendChild(clone);

            // disable item di kiri (ATS) hanya untuk NON-custom
            if (!isCustom(base)) {
                const listAts = document.getElementById('available-fields-ats');
                if (listAts) {
                    const leftLi = listAts.querySelector(`.list-group-item[data-key="${key}"]`);
                    if (leftLi) {
                        leftLi.classList.add('disabled');
                        leftLi.style.pointerEvents  = 'none';
                        leftLi.style.color          = '#b0b0b0';
                        leftLi.style.backgroundColor = '#f8f9fa';
                    }
                }
            }

            updateLayoutJSON();
        },
        onSort: updateLayoutJSON
    });

    // Handler dropdown tipe template
    templateTypeSelect.addEventListener("change", function () {
        const selected = this.value;

        // Reset state dasar
        document.querySelectorAll(".template-fields").forEach(el => el.classList.add("d-none"));
        canvas.innerHTML   = '';
        layoutInput.value  = '';
        styleInput.value   = '';

        builderControls.classList.add('d-none');
        builderCanvasWrapper.classList.add('d-none');
        kreatifInfo.classList.add('d-none');

        if (!selected) return;

        // MODE KREATIF: tidak pakai builder di halaman ini
        if (selected === 'kreatif') {
            kreatifInfo.classList.remove('d-none');
            return;
        }

        // MODE ATS: builder aktif
        if (selected === 'ats') {
            builderControls.classList.remove('d-none');
            builderCanvasWrapper.classList.remove('d-none');

            // Placeholder awal
            const p = document.createElement('p');
            p.className = 'empty-placeholder text-muted';
            p.textContent = '(Seret komponen dari kiri ke area ini untuk menyusun template CV)';
            canvas.appendChild(p);

            const listAts = document.getElementById("available-fields-ats");
            if (listAts) {
                listAts.classList.remove("d-none");

                // Inisialisasi sortable kiri (clone) satu kali saja
                if (!listAts.dataset.sortableInitialized) {
                    Sortable.create(listAts, {
                        group: { name: 'cv-sections', pull: 'clone', put: false },
                        sort: false
                    });
                    listAts.dataset.sortableInitialized = '1';
                }

                // Reset disabled state
                listAts.querySelectorAll('.list-group-item').forEach(li => {
                    li.classList.remove('disabled');
                    li.style.pointerEvents  = 'auto';
                    li.style.color          = '';
                    li.style.backgroundColor = '';
                });
            }
        }
    });

    // Hapus section di canvas
    document.body.addEventListener('click', function(e) {
        if (e.target && e.target.matches('.remove-section')) {
            const sec = e.target.closest('.cv-section');
            if (!sec) return;

            const keyBase = sec.dataset.key;  // contoh: "experiences" / "custom"
            sec.remove();

            // re-enable item kiri hanya untuk NON-custom (ATS)
            if (!isCustom(keyBase)) {
                const fullKey = `${keyBase}-ats`;
                const listAts = document.getElementById('available-fields-ats');
                if (listAts) {
                    const leftLi = listAts.querySelector(`.list-group-item[data-key="${fullKey}"]`);
                    if (leftLi) {
                        leftLi.classList.remove('disabled');
                        leftLi.style.pointerEvents  = 'auto';
                        leftLi.style.color          = '';
                        leftLi.style.backgroundColor = '';
                    }
                }
            }

            updateLayoutJSON();
        }
    });

    // Update layout_json + style_json (khusus ATS)
    function updateLayoutJSON() {
        if (templateTypeSelect.value !== 'ats') {
            layoutInput.value = '';
            styleInput.value  = '';
            return;
        }

        const layout = [];

        const pickCustomPayload = (sectionEl) => {
            const titleEl = sectionEl.querySelector('h4.editable, h3.editable');
            const bodyEl  = sectionEl.querySelector('div.editable');
            return {
                title:  titleEl ? titleEl.textContent.trim() : 'Custom Section',
                payload:{ body: bodyEl ? bodyEl.innerHTML.trim() : 'Tulis deskripsi di sini…' }
            };
        };

        canvas.querySelectorAll('.cv-section').forEach(sec => {
            const keyBase = sec.dataset.key;
            const item = { key: keyBase };
            if (isCustom(keyBase)) Object.assign(item, pickCustomPayload(sec));
            layout.push(item);
        });

        layoutInput.value = JSON.stringify(layout);

        // ===== Style default (sama seperti sebelumnya) =====
        const baseStyles = {
          "page": {
            "width": "100%",
            "margin": "0 auto",
            "padding": "0 48px",
            "max-width": "800px",
            "box-sizing": "border-box"
          },

          "links": {
            "h3": {
              "color": "#111111",
              "margin": "0 0 6px 0",
              "font-size": "18px",
              "text-align": "center",
              "border-bottom": "1px solid #111",
              "padding-bottom": "6px"
            },
            "hr": { "margin-top": "6px" },
            "ul": { "color": "#000000", "font-size": "14px" }
          },

          "skills": {
            "h3": {
              "color": "#111111",
              "margin": "0 0 6px 0",
              "font-size": "18px",
              "text-align": "center",
              "border-bottom": "1px solid #111",
              "padding-bottom": "6px"
            },
            "hr": { "margin-top": "6px" },
            "ul": {
              "color": "#000000",
              "columns": "2",
              "font-size": "14px",
              "margin-top": "6px"
            }
          },

          "languages": {
            "h3": {
              "color": "#111111",
              "margin": "0 0 6px 0",
              "font-size": "18px",
              "text-align": "center",
              "border-bottom": "1px solid #111",
              "padding-bottom": "6px"
            },
            "hr": { "margin-top": "6px" },
            "ul": {
              "color": "#000000",
              "font-size": "14px",
              "margin-top": "6px"
            }
          },

          "educations": {
            "h3": {
              "color": "#111111",
              "margin": "0 0 6px 0",
              "font-size": "18px",
              "text-align": "center",
              "border-bottom": "1px solid #111",
              "padding-bottom": "6px"
            },
            "hr": { "margin-top": "6px" },
            "ul": { "margin-top": "6px" },
            "div": { "color": "#000000", "font-size": "14px" },
            "date": { "color": "#666666", "float": "right", "font-size": "12px" },
            "field": { "margin-top": "6px" }
          },

          "experiences": {
            "h3": {
              "color": "#111111",
              "margin": "0 0 6px 0",
              "font-size": "18px",
              "text-align": "center",
              "border-bottom": "1px solid #111",
              "padding-bottom": "6px"
            },
            "hr": { "margin-top": "6px" },
            "li": { "text-align": "justify", "margin-bottom": "6px" },
            "ul": {
              "color": "#000000",
              "font-size": "14px",
              "margin-top": "8px",
              "line-height": "1.5",
              "padding-left": "20px",
              "margin-bottom": "8px",
              "list-style-type": "disc"
            },
            "div": { "color": "#000000", "font-size": "14px", "margin-bottom": "10px" },
            "date": { "color": "#555555", "float": "right", "font-size": "12px" },
            "meta": { "overflow": "hidden", "margin-bottom": "4px" },
            "position": { "float": "left", "font-style": "italic" }
          },

          "achievements": {
            "h3": {
              "color": "#111111",
              "margin": "0 0 6px 0",
              "font-size": "18px",
              "text-align": "center",
              "border-bottom": "1px solid #111",
              "padding-bottom": "6px"
            },
            "hr": { "margin-top": "6px" },
            "ul": { "color": "#000000", "font-size": "14px" }
          },

          "organizations": {
            "h3": {
              "color": "#111111",
              "margin": "0 0 6px 0",
              "font-size": "18px",
              "text-align": "center",
              "border-bottom": "1px solid #111",
              "padding-bottom": "6px"
            },
            "hr": { "margin-top": "6px" },
            "ul": { "margin-top": "6px" },
            "div": { "color": "#000000", "font-size": "14px" }
          },

          "personal_detail": {
            "hr": {
              "width": "80%",
              "border": "0",
              "margin": "12px auto",
              "border-top": "1px solid #ddd"
            },
            "row": {
              "display": "flex",
              "flex-wrap": "wrap",
              "text-align": "center",
              "align-items": "center",
              "margin-bottom": "8px",
              "justify-content": "center"
            },
            "info": {
              "color": "#000000",
              "display": "inline-block",
              "font-size": "12px",
              "text-align": "center",
              "line-height": "1.4",
              "margin-bottom": "4px"
            },
            "name": {
              "color": "#000000",
              "margin": "0 0 8px 0",
              "font-size": "28px",
              "text-align": "center",
              "font-weight": "700",
              "line-height": "1.05",
              "text-transform": "uppercase"
            },
            "summary": {
              "color": "#333333",
              "margin": "12px 0",
              "font-size": "13px",
              "text-align": "justify"
            },
            "container": {
              "width": "100%",
              "margin": "0 auto",
              "display": "block",
              "padding": "0 12px",
              "max-width": "760px",
              "box-sizing": "border-box",
              "text-align": "center"
            }
          },

          // Section Bebas
          "custom": {
            "h3": {
              "color": "#111111",
              "margin": "0 0 6px 0",
              "font-size": "18px",
              "text-align": "center",
              "border-bottom": "1px solid #111",
              "padding-bottom": "6px"
            },
            "content": {
              "font-size": "14px",
              "line-height": "1.5",
              "text-align": "justify"
            },
            "container": {}
          }
        };

        styleInput.value = JSON.stringify(baseStyles);
    }

    // Sebelum submit, pastikan layout ter-update
    form.addEventListener('submit', function(e) {
        updateLayoutJSON();
    });
});
</script>
@endpush
