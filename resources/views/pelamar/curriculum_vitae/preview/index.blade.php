@extends('layouts.cv')

@section('content')
<div class="main-container">
    <!-- Panel Kiri (Editor Panel) -->
    <div class="editor-panel">
        <div style="text-align: center; color: #3538cd; font-size: 32px; font-family: Poppins; font-weight: 400; line-height: 41px;">
            Editor
        </div>

        <div class="editor-item font-dropdown">
            <img src="{{asset('assets/images/font.svg')}}" alt="Font Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
            <div class="custom-select-wrapper">
                <select class="editor-btn" onchange="changeFont(this)">
                    <option value="Poppins, sans-serif">Poppins</option>
                    <option value="Arial, sans-serif">Arial</option>
                    <option value="Times New Roman, serif">Times New Roman</option>
                    <option value="Roboto, sans-serif">Roboto</option>
                    <option value="Georgia, serif">Georgia</option>
                    <option value="Courier New, monospace">Courier New</option>
                </select>
            </div>
        </div>

        <div class="editor-item">
            <img src="{{asset('assets/images/color.svg')}}" alt="Color Icon" />
            <input type="color" onchange="changeBackgroundColor(this.value)" class="color-picker" />
        </div>

        <div class="editor-item">
            <img src="{{asset('assets/images/download.svg')}}" alt="Download Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
            <div style="display: flex; gap: 8px;">
                <button class="editor-btn" onclick="downloadAsImage('png')">PNG</button>
                <button class="editor-btn" onclick="downloadAsImage('jpeg')">JPEG</button>
                <button class="editor-btn" onclick="downloadAsPDF()">PDF</button>
            </div>
        </div>

        <div class="editor-item">
            <img src="{{asset('assets/images/print.svg')}}" alt="Print Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
            <button class="editor-btn" onclick="printCV()">Print</button>
        </div>

        <div class="editor-item">
            <img src="{{asset('assets/images/edit.svg')}}" alt="Edit Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
            <a href="{{route('pelamar.curriculum_vitae.profile.index', $cv->id)}}" class="editor-btn">Edit Data</a>
        </div>

        <div class="editor-item">
            <img src="{{asset('assets/images/download.svg')}}" alt="Save Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
            <button id="saveToDashboardBtn" data-template-id="{{ $cv->template_curriculum_vitae_id ?? 1 }}">Save to Dashboard</button>
        </div>

        <div class="editor-item">
            <img src="{{asset('assets/images/home.svg')}}" alt="Home Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
            <a href="{{route('home')}}" class="editor-btn">Back to Home</a>
        </div>
    </div>

    <!-- Panel Kanan (Preview CV) -->
    <div class="container">
        <div id="content">
            @foreach($layout as $section)
                <div class="section {{ $section['key'] }}" data-key="{{ $section['key'] }}">
                    <span class="drag-indicator" style="display:none;position:absolute;left:8px;top:12px;font-size:20px;color:#3538cd;cursor:grab;z-index:10;">
                        &#x2630;
                    </span>
                    <span class="delete-indicator" style="display:none;position:absolute;right:8px;top:12px;font-size:20px;color:#e74c3c;cursor:pointer;z-index:10;" title="Hapus section">
                        &#128465;
                    </span>
                    @includeIf('pelamar.curriculum_vitae.preview.sections.' . $section['key'], [
                        'cv' => $cv
                    ])
                </div>
            @endforeach
        </div>
        <style>
        #content { position: relative; }
        .section { position: relative; }
        .drag-indicator, .delete-indicator {
            transition: opacity 0.2s;
            pointer-events: auto;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
            padding: 2px 6px;
        }
        .section:hover .drag-indicator,
        .section:hover .delete-indicator {
            display: inline-block !important;
            opacity: 1;
        }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var content = document.getElementById('content');
            Sortable.create(content, {
                animation: 150,
                handle: '.section',
                draggable: '.section',
                onEnd: function(evt) {
                    // Ambil urutan baru
                    var newOrder = Array.from(content.querySelectorAll('.section')).map(function(sec) {
                        return sec.getAttribute('data-key');
                    });
                    // Kirim ke backend via AJAX (contoh, sesuaikan endpoint dan data)
                    fetch('/cv/update-section-order', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            cv_id: '{{ $cv->id }}',
                            order: newOrder
                        })
                    }).then(res => res.json()).then(data => {
                        // Optional: tampilkan notifikasi sukses
                    });
                }
            });
            // Tampilkan drag indicator saat hover
            content.querySelectorAll('.section').forEach(function(sec) {
                sec.addEventListener('mouseenter', function() {
                    var dragIndicator = sec.querySelector('.drag-indicator');
                    var deleteIndicator = sec.querySelector('.delete-indicator');
                    if (dragIndicator) dragIndicator.style.display = 'inline-block';
                    if (deleteIndicator) deleteIndicator.style.display = 'inline-block';
                });
                sec.addEventListener('mouseleave', function() {
                    var dragIndicator = sec.querySelector('.drag-indicator');
                    var deleteIndicator = sec.querySelector('.delete-indicator');
                    if (dragIndicator) dragIndicator.style.display = 'none';
                    if (deleteIndicator) deleteIndicator.style.display = 'none';
                });
                // Hapus section saat icon sampah diklik
                var deleteIndicator = sec.querySelector('.delete-indicator');
                if (deleteIndicator) {
                    deleteIndicator.addEventListener('click', function(e) {
                        e.stopPropagation();
                        sec.remove();
                        // Optional: update urutan ke backend
                        var newOrder = Array.from(content.querySelectorAll('.section')).map(function(s) {
                            return s.getAttribute('data-key');
                        });
                        fetch('/cv/update-section-order', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                cv_id: '{{ $cv->id }}',
                                order: newOrder
                            })
                        });
                    });
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("#content [contenteditable='true']").forEach(el => {
        el.setAttribute("spellcheck", "false");

        // highlight saat hover
        el.addEventListener("mouseenter", () => {
            el.style.outline = "1px dashed #3538cd";
            el.style.cursor = "text";
        });
        el.addEventListener("mouseleave", () => {
            el.style.outline = "none";
        });

        // simpan otomatis ke backend saat selesai edit
        el.addEventListener("blur", () => {
            const section = el.dataset.section;
            const field   = el.dataset.field;
            const value   = el.innerText.trim();

            fetch("{{ route('pelamar.curriculum_vitae.updateInline') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    cv_id: "{{ $cv->id }}",
                    section: section,
                    field: field,
                    value: value
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    console.log("Saved:", data.message);
                } else {
                    console.warn("Save failed:", data.message);
                }
            })
            .catch(err => console.error("Error saving inline edit:", err));
        });
    });
});
        </script>
    </div>
</div>
@endsection
