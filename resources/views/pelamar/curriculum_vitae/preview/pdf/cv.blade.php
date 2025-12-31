<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <style>
        /* ==== A4 margin ==== */
        @page { margin: 18mm 14mm; }
        body { margin: 0; padding: 0; }

        /* ==== Inject CSS template (ATS / Kreatif kalau memang dipakai) ==== */
        {!! $style['css'] ?? '' !!}

        /* ==== Paksa semua elemen editor hilang ==== */
        .editor-panel,
        .export-hidden,
        .drag-indicator,
        .delete-indicator,
        #custom-add-wrapper,
        .add-section-wrapper,
        .add-section-toggle,
        .add-section-menu,
        .custom-delete,
        .drag-indicator { display: none !important; }

        /* ==== Hilangkan border dashed + outline inline edit ==== */
        .inline-edit,
        [contenteditable="true"],
        .editable {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        /* ==== Normalisasi list agar rapi di PDF ==== */
        ul { margin: 0; padding-left: 18px; }
        li { margin: 0; }

        /* ==== Hilangkan placeholder pseudo element (kalau ada) ==== */
        .inline-edit:empty:before { content: "" !important; }
    </style>
</head>

<body>
    {{-- MODE KREATIF (GRAPESJS) --}}
    @if(!empty($isGrapesTemplate) && $isGrapesTemplate && !empty($grapesRenderedHtml))
        {!! $grapesRenderedHtml !!}

    @else
        {{-- MODE ATS (layout_json lama) --}}
        <div id="content">
            {{-- Section bawaan template --}}
            @foreach($layout as $section)
                @php
                    $key = is_array($section) ? ($section['key'] ?? null) : null;
                @endphp
                @continue(!$key)

                <div class="section {{ $key }}" data-key="{{ $key }}">
                    @includeIf('pelamar.curriculum_vitae.preview.sections.' . $key, ['cv' => $cv])
                </div>
            @endforeach

            {{-- Custom sections buatan pelamar --}}
            @foreach($cv->customSections as $custom)
                @includeIf('pelamar.curriculum_vitae.preview.sections.' . $custom->section_key, [
                    'cv'      => $cv,
                    'section' => $custom,
                ])
            @endforeach
        </div>
    @endif
</body>
</html>
