@extends('admin.master_admin')
@section('title', 'Editor Visual Template CV | CVRE GENERATE')

@push('styles')
<link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet"/>
<style>
    #gjs {
        border: 1px solid #ddd;
        height: 800px;
        background: #f5f5f5;
    }
    #blocks {
        padding: 10px;
        border-right: 1px solid #ddd;
        max-height: 800px;
        overflow-y: auto;
    }
    .cv-page {
        width: 800px;
        min-height: 1120px;
        margin: 20px auto;
        padding: 40px;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 0 0 1px #eee;
        font-family: Arial, sans-serif;
        font-size: 13px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Editor Visual - {{ $template->template_curriculum_vitae_name }}</h4>
        <a href="{{ route('admin.template_curriculum_vitae.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    <form id="gjsForm"
          method="POST"
          action="{{ route('admin.template_curriculum_vitae.updateDesigner', $template) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nama Template</label>
            <input type="text"
                   name="template_curriculum_vitae_name"
                   class="form-control"
                   value="{{ old('template_curriculum_vitae_name', $template->template_curriculum_vitae_name) }}"
                   required>
        </div>

        {{-- hidden untuk hasil GrapesJS --}}
        <input type="hidden" name="html" id="htmlInput">
        <input type="hidden" name="css" id="cssInput">

        <div class="row">
            <div class="col-md-2">
                <div id="blocks"></div>

                <div class="mt-4">
                    <h6>Placeholder Data</h6>
                    <small class="text-muted">
                        Ketik placeholder di text:
                    </small>
                    <ul class="small ps-3">
                        <li><code>[[nama]]</code></li>
                        <li><code>[[position]]</code></li>
                        <li><code>[[alamat]]</code></li>
                        <li><code>[[telepon]]</code></li>
                        <li><code>[[email]]</code></li>
                        <li><code>[[ringkasan]]</code></li>
                        {{-- bisa kamu tambah sendiri --}}
                    </ul>
                </div>
            </div>

            <div class="col-md-10">
                <div id="gjs">
                    @if($initialCss)
                        <style>{!! $initialCss !!}</style>
                    @endif

                    @if($initialHtml)
                        {!! $initialHtml !!}
                    @else
                        {{-- default layout mirip contoh Rick Tang: sidebar kiri + konten kanan --}}
                        <div class="cv-page">
                            <div style="display:flex; min-height:100%;">
                                {{-- sidebar kiri --}}
                                <div style="width:30%; background:#1e4d40; color:#fff; padding:32px 24px;">
                                    <div style="text-align:center; margin-bottom:24px;">
                                        <div style="width:110px;height:110px;border-radius:50%;overflow:hidden;margin:0 auto 12px;background:#ccc;"></div>
                                        <div style="font-size:22px;font-weight:bold;">[[nama]]</div>
                                        <div style="font-size:12px;opacity:.9;">[[position]]</div>
                                    </div>

                                    <h4 style="font-size:13px;letter-spacing:1px;margin-top:16px;">DETAILS</h4>
                                    <p style="font-size:11px;margin-bottom:4px;">Address</p>
                                    <p style="font-size:12px;">[[alamat]]</p>

                                    <p style="font-size:11px;margin-bottom:4px;margin-top:10px;">Phone</p>
                                    <p style="font-size:12px;">[[telepon]]</p>

                                    <p style="font-size:11px;margin-bottom:4px;margin-top:10px;">Email</p>
                                    <p style="font-size:12px;">[[email]]</p>

                                    <h4 style="font-size:13px;letter-spacing:1px;margin-top:24px;">SKILLS</h4>
                                    <p style="font-size:12px;">[[skills_singkat]]</p>
                                </div>

                                {{-- konten kanan --}}
                                <div style="width:70%; padding:32px 40px;">
                                    <h3 style="margin-top:0;">Profile</h3>
                                    <p>[[ringkasan]]</p>

                                    <h3 style="margin-top:24px;">Experience</h3>
                                    <p>[[pengalaman_list]]</p>

                                    <h3 style="margin-top:24px;">Education</h3>
                                    <p>[[pendidikan_list]]</p>

                                    <h3 style="margin-top:24px;">Languages</h3>
                                    <p>[[bahasa_list]]</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-3 text-end">
            <button type="button" id="btnSave" class="btn btn-primary">
                Simpan Template
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/grapesjs"></script>
<script>
    const editor = grapesjs.init({
        container: '#gjs',
        fromElement: true,
        height: '800px',
        storageManager: false,
        blockManager: { appendTo: '#blocks' },
    });

    // Beberapa block sederhana biar enak drag-drop
    editor.BlockManager.add('cv-heading', {
        label: 'Heading',
        category: 'CV',
        content: '<h3>Section Title</h3>',
    });

    editor.BlockManager.add('cv-paragraph', {
        label: 'Paragraf',
        category: 'CV',
        content: '<p>Tulis deskripsi di sini...</p>',
    });

    editor.BlockManager.add('cv-two-cols', {
        label: '2 Kolom',
        category: 'Layout',
        content: '<div style="display:flex;gap:16px;"><div style="flex:1;"><p>Kolom 1</p></div><div style="flex:1;"><p>Kolom 2</p></div></div>',
    });

    editor.BlockManager.add('cv-list', {
        label: 'List',
        category: 'CV',
        content: '<ul><li>Item 1</li><li>Item 2</li></ul>',
    });

    document.getElementById('btnSave').addEventListener('click', function () {
        document.getElementById('htmlInput').value = editor.getHtml();
        document.getElementById('cssInput').value  = editor.getCss();
        document.getElementById('gjsForm').submit();
    });
</script>
@endpush
