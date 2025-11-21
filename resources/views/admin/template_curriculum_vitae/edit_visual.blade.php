@extends('admin.master_admin')

@section('title', 'Edit Visual Template CV | CVRE GENERATE')

@push('style')
    {{-- GrapesJS CSS --}}
    <link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet"/>

    <style>
        #gjs {
            border: 1px solid #ddd;
            min-height: 650px;
            background: #f7f7f7;
        }

        .gjs-pn-panel {
            z-index: 2000;
        }

        /* area blok di kiri */
        #blocks {
            border: 1px solid #ddd;
            min-height: 650px;
            background: #fafafa;
            overflow-y: auto;
            padding: 8px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <h5 class="mb-3">
                        Edit Visual Template (Kreatif):
                        <strong>{{ $template->template_curriculum_vitae_name }}</strong>
                    </h5>

                    <p class="text-muted">
                        Gunakan editor di bawah ini untuk menyusun layout CV kreatif.
                        Saat klik <strong>Simpan</strong>, HTML dan CSS akan disimpan ke
                        <code>layout_json</code> dan <code>style_json</code> dengan engine
                        <span class="text-danger">grapesjs</span>.
                    </p>

                    <form id="designerForm"
                          method="POST"
                          action="{{ route('admin.template_curriculum_vitae.updateDesigner', $template->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Nama Template --}}
                        <div class="form-group mb-3">
                            <label>Nama Template</label>
                            <input type="text"
                                   name="template_curriculum_vitae_name"
                                   class="form-control"
                                   value="{{ old('template_curriculum_vitae_name', $template->template_curriculum_vitae_name) }}"
                                   required>
                        </div>

                        {{-- ROW: blok di kiri, canvas di kanan --}}
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <h6 class="mb-2">Blok</h6>
                                <div id="blocks"></div>
                            </div>
                            <div class="col-md-9 mb-3">
                                <h6 class="mb-2">Canvas</h6>
                                <div id="gjs"></div>
                            </div>
                        </div>

                        {{-- Hidden field untuk kirim hasil HTML & CSS --}}
                        <input type="hidden" name="html" id="htmlInput">
                        <input type="hidden" name="css" id="cssInput">

                        <div class="mt-3 text-right">
                            <a href="{{ route('admin.template_curriculum_vitae.index') }}" class="btn btn-light">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/grapesjs/dist/grapes.min.js"></script>
<script src="https://unpkg.com/grapesjs-blocks-basic"></script>

<script>
    const editor = grapesjs.init({
        container: '#gjs',
        height: '650px',
        fromElement: false,
        storageManager: false,
        blockManager: { appendTo: '#blocks' },
        plugins: ['gjs-blocks-basic'],
        pluginsOpts: {
            'gjs-blocks-basic': { flexGrid: true },
        },
    });

    @php
        $layoutArr = json_decode($template->layout_json ?? '[]', true) ?: [];
        $styleArr  = json_decode($template->style_json ?? '[]', true) ?: [];
        $initialHtml = $layoutArr['html'] ?? '';
        $initialCss  = $styleArr['css'] ?? '';
    @endphp

    editor.setComponents(@json($initialHtml));
    editor.setStyle(@json($initialCss));

    // === BLOK BARU DENGAN PLACEHOLDER ===
    editor.BlockManager.add('cv-two-cols', {
        label: 'Layout CV 2 Kolom (Dinamis)',
        category: 'CV Layout',
        attributes: { class: 'gjs-fonts gjs-f-b1' },
        content: `
        <div class="cv-wrapper" data-cv-layout="kreatif"
            style="display:grid;grid-template-columns:260px 1fr;min-height:842px;">
            <aside class="cv-left"
                style="background:#153b35;color:#ffffff;padding:24px;">
                <!-- personal detail (foto, nama, kontak) -->
                <div data-section="personal_detail"></div>
            </aside>

            <main class="cv-right" style="padding:32px;">
                <div data-section="experiences"></div>
                <div data-section="educations"></div>
                <div data-section="languages"></div>
                <div data-section="skills"></div>
                <div data-section="achievements"></div>
                <div data-section="organizations"></div>
                <div data-section="links"></div>
            </main>
        </div>
        `,
    });

    const form = document.getElementById('designerForm');
    form.addEventListener('submit', function () {
        document.getElementById('htmlInput').value = editor.getHtml();
        document.getElementById('cssInput').value  = editor.getCss();
    });
</script>
@endpush
