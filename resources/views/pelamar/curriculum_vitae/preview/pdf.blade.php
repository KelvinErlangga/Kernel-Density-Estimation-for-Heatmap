<!doctype html>
<html lang="{{ $lang ?? 'id' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Font (samakan dengan preview) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    {{-- CSS dari style_json (ATS & Kreatif) --}}
    @if(!empty($style['css'] ?? null))
        <style>{!! $style['css'] !!}</style>
    @endif

    <style>
        /* ===== KUNCI BIAR PDF MIRIP PREVIEW ===== */
        html, body { margin: 0; padding: 0; font-family: Poppins, sans-serif; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

        /* paper */
        @page { size: A4; margin: 12mm; }

        /* area kerja */
        .pdf-page {
            width: 210mm;
            box-sizing: border-box;
        }

        /* konten CV kamu biasanya ada #content, kita pertahankan */
        #content { position: relative; }

        /* HILANGKAN SEMUA YANG KHUSUS INTERAKTIF */
        .editor-panel,
        .export-hidden,
        .drag-indicator,
        .delete-indicator,
        [contenteditable="true"] {
            outline: none !important;
            box-shadow: none !important;
        }

        /* kalau ada border hover/garis debug dari preview */
        .section:hover .drag-indicator,
        .section:hover .delete-indicator { display: none !important; }
    </style>
</head>
<body>
    <div class="pdf-page">
        <div id="content">
            @if(!empty($isGrapesTemplate) && $isGrapesTemplate && !empty($grapesRenderedHtml))
                {{-- MODE KREATIF (GRAPESJS) --}}
                {!! $grapesRenderedHtml !!}
            @else
                {{-- MODE ATS --}}
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
            @endif
        </div>
    </div>
</body>
</html>
