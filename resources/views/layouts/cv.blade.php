<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Preview CV | CVRE GENERATE</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="{{asset('assets/icons/logo.svg')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{asset('css/preview.css')}}" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="{{asset('js/editor.js')}}" defer></script>
    <style>
        a {
            text-decoration: none;
        }
        [data-section="personal_detail"] .personal-info__item:not(:last-child)::after {
            content: '{{ $style['personal_detail']['info']['separator'] ?? "•" }}';
            color: {{ $style['personal_detail']['info']['separator_color'] ?? "#000" }};
            margin: 0 {{ $style['personal_detail']['info']['separator_margin'] ?? "6px" }};
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="cv-wrapper">
        <div class="cv-content">
            @yield('content')
        </div>
    </div>
</body>
</html>
