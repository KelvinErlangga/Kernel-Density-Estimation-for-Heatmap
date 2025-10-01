@extends('admin.master_admin')
@section('title', 'Tambah Template CV | CVRE GENERATE')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tambah Template Curriculum Vitae</h1>

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-4 mb-4 text-center">Tambah Template Curriculum Vitae</h4>

                <form method="POST" action="{{ route('admin.template_curriculum_vitae.store') }}" enctype="multipart/form-data" id="templateForm">
                    @csrf
                    <div class="row">
                        <!-- Panel Kiri: Komponen -->
                        <div class="col-md-4">
                            <h5>Komponen CV (Drag ke kanan)</h5>

                            <ul id="available-fields" class="list-group">
                                <li class="list-group-item" data-key="personal_detail">Detail Pribadi (personal_detail)</li>
                                <li class="list-group-item" data-key="experiences">Pengalaman Kerja (experiences)</li>
                                <li class="list-group-item" data-key="educations">Pendidikan (educations)</li>
                                <li class="list-group-item" data-key="languages">Bahasa (languages)</li>
                                <li class="list-group-item" data-key="skills">Keahlian (skills)</li>
                                <li class="list-group-item" data-key="organizations">Pengalaman Organisasi (organizations)</li>
                                <li class="list-group-item" data-key="achievements">Prestasi (achievements)</li>
                                <li class="list-group-item" data-key="links">Link Informasi (links)</li>
                            </ul>

                            <p class="small text-muted mt-3">
                                <strong>Catatan:</strong> Setelah sebuah komponen dimasukkan ke canvas, komponen tersebut akan
                                dinonaktifkan agar tidak dapat ditambahkan dua kali. Untuk menghapus dari canvas dan
                                mengaktifkan kembali, klik tombol "hapus" (×) pada setiap section di canvas.
                            </p>
                        </div>

                        <!-- Panel Kanan: Preview CV -->
                        <div class="col-md-8">
                            <h5>Preview Template CV</h5>
                            <div id="cv-canvas" style="min-height:600px; border:1px solid #ddd; padding:24px; background:#fff; font-family:Arial, Helvetica, sans-serif; max-width:820px; margin:auto;">
                                <div id="cv-canvas-inner">
                                    <p class="empty-placeholder text-muted">(Seret komponen dari kiri ke area ini untuk menyusun template CV)</p>
                                </div>
                            </div>

                            <input type="hidden" name="layout_json" id="layout_json">
                            <input type="hidden" name="style_json" id="style_json">
                        </div>
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

                        <!-- Thumbnail Template -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="thumbnail_curriculum_vitae">Gambar Thumbnail Template CV</label>
                                <input type="file" class="form-control" name="thumbnail_curriculum_vitae" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg mt-3">Tambah Template CV</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Templates CV Sections (sample HTML untuk preview) -->
<template id="tpl-personal_detail">
    <div class="cv-section" data-key="personal_detail" style="position:relative; padding:12px 0;">
        <button type="button" class="remove-section btn btn-sm btn-danger" title="Hapus section" style="position:absolute; right:0; top:0;">×</button>
        <h1 style="margin:0; font-size:22px; text-align:center;">ANINDITA PUTRI</h1>
        <p style="margin:6px 0 0 0; font-size:13px; text-align:center; color:#555;">
            Padang, Sumatra Barat, Indonesia • anindita.pu@kitalulus.com • +62812-3456-7890
        </p>
        <hr style="margin-top:12px;">
    </div>
</template>

<template id="tpl-experiences">
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

<template id="tpl-educations">
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

<template id="tpl-languages">
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

<template id="tpl-skills">
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

<template id="tpl-organizations">
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

<template id="tpl-achievements">
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

<template id="tpl-links">
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

@endsection

@push('styles')
<style>
    /* disabled state kiri */
    #available-fields .list-group-item.disabled {
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
document.addEventListener("DOMContentLoaded", function() {
    const available = document.getElementById('available-fields');
    const canvas = document.getElementById('cv-canvas-inner');
    const layoutInput = document.getElementById('layout_json');
    const form = document.getElementById('templateForm');

    // make left list draggable (clone)
    Sortable.create(available, {
        group: { name: 'cv-sections', pull: 'clone', put: false },
        sort: false
    });

    // make canvas accept drops
    Sortable.create(canvas, {
        group: { name: 'cv-sections', pull: false, put: true },
        animation: 150,
        onAdd: function(evt) {
            const li = evt.item;
            const key = li.dataset.key;
            // remove placeholder li that Sortable inserted
            li.remove();

            // prevent duplicates
            if (canvas.querySelector(`[data-key="${key}"]`)) {
                updateLayoutJSON();
                return;
            }

            const tpl = document.getElementById('tpl-' + key);
            if (!tpl) {
                console.warn('Template not found for', key);
                return;
            }

            // remove empty placeholder text
            const placeholder = canvas.querySelector('.empty-placeholder');
            if (placeholder) placeholder.remove();

            // append cloned template
            const clone = tpl.content.cloneNode(true);
            canvas.appendChild(clone);

            // disable left item
            const leftLi = available.querySelector(`[data-key="${key}"]`);
            if (leftLi) {
                leftLi.classList.add('disabled');
                leftLi.style.pointerEvents = 'none';
                leftLi.style.color = '#b0b0b0';
                leftLi.style.backgroundColor = '#f8f9fa';
            }

            updateLayoutJSON();
        },
        onSort: updateLayoutJSON
    });

    // remove section (event delegation)
    document.body.addEventListener('click', function(e) {
        if (e.target && e.target.matches('.remove-section')) {
            const sec = e.target.closest('.cv-section');
            if (!sec) return;
            const key = sec.dataset.key;
            sec.remove();

            // re-enable left li
            const leftLi = available.querySelector(`[data-key="${key}"]`);
            if (leftLi) {
                leftLi.classList.remove('disabled');
                leftLi.style.pointerEvents = 'auto';
                leftLi.style.color = '';
                leftLi.style.backgroundColor = '';
            }

            // if empty, add placeholder
            if (!canvas.querySelector('.cv-section')) {
                const p = document.createElement('p');
                p.className = 'empty-placeholder text-muted';
                p.textContent = '(Seret komponen dari kiri ke area ini untuk menyusun template CV)';
                canvas.appendChild(p);
            }

            updateLayoutJSON();
        }
    });

    function updateLayoutJSON() {
        const layout = [];

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
                "date": { "color": "#666", "float": "right", "font-size": "12px" },
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
                "date": { "color": "555555", "float": "right", "font-size": "12px" },
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
            }
        };

        // loop semua section di canvas
        canvas.querySelectorAll('.cv-section').forEach(sec => {
            const key = sec.dataset.key;
            layout.push({ key });
        });

        // simpan ke hidden input
        layoutInput.value = JSON.stringify(layout);
        document.getElementById("style_json").value = JSON.stringify(baseStyles);
    }

    // ensure layout updated before submit
    form.addEventListener('submit', function(e) {
        updateLayoutJSON();
        // optional validation example
        // if (!layoutInput.value || layoutInput.value === '[]') {
        //     if (!confirm('Layout template kosong. Lanjutkan menyimpan tanpa section?')) {
        //         e.preventDefault();
        //         return false;
        //     }
        // }
    });

    // initial placeholder (already in HTML but ensure)
    if (!canvas.querySelector('.cv-section') && !canvas.querySelector('.empty-placeholder')) {
        const p = document.createElement('p');
        p.className = 'empty-placeholder text-muted';
        p.textContent = '(Seret komponen dari kiri ke area ini untuk menyusun template CV)';
        canvas.appendChild(p);
    }
});
</script>
@endpush
