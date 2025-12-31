@extends('layouts.cv')

@section('content')
@php
    // ===== Export Mode (dipakai oleh Browsershot) =====
    // PDFController kamu memanggil previewUrl dengan ?pdf=1
    $isExport = request()->boolean('pdf') || request()->boolean('export');
@endphp

<div class="main-container">
    {{-- ===== Left Editor Panel ===== --}}
    @if(!$isExport)
    <div class="editor-panel">
        <div data-i18n="editor.title"
            style="text-align:center;color:#3538cd;font-size:32px;font-family:Poppins;font-weight:400;line-height:41px;">
            Editor
        </div>

        <div class="editor-item font-dropdown">
            <img src="{{ asset('assets/images/font.svg') }}" alt="Font Icon" style="width:24px;height:24px;margin-right:8px;" />
            <div class="custom-select-wrapper">
                <select class="editor-btn" onchange="changeFont(this)">
                    <option value="Poppins, sans-serif">Poppins</option>
                    <option value="Arial, sans-serif">Arial</option>
                    <option value="'Times New Roman', serif">Times New Roman</option>
                    <option value="Roboto, sans-serif">Roboto</option>
                    <option value="Georgia, serif">Georgia</option>
                    <option value="'Courier New', monospace">Courier New</option>
                </select>
            </div>
        </div>

        {{-- ===== Language Switcher ===== --}}
        <div class="editor-item font-dropdown">
            <img src="{{ asset('assets/images/language.svg') }}" alt="Language Icon"
                style="width:24px;height:24px;margin-right:8px;" />
            <div class="custom-select-wrapper">
                <select id="langSelect" class="editor-btn" onchange="changeLanguage(this)">
                    <option value="id">Bahasa Indonesia</option>
                    <option value="en">English</option>
                </select>
            </div>
        </div>

        <div class="editor-item">
            <img src="{{ asset('assets/images/color.svg') }}" alt="Color Icon" />
            <input type="color" onchange="changeBackgroundColor(this.value)" class="color-picker" />
        </div>

        <div class="editor-item">
            <img src="{{ asset('assets/images/download.svg') }}" alt="Download Icon" style="width:24px;height:24px;margin-right:8px;" />
            <div style="display:flex;gap:8px;">
                <button class="editor-btn" onclick="downloadAsImage('png')">PNG</button>
                <button class="editor-btn" onclick="downloadAsImage('jpeg')">JPEG</button>
                <button type="button" id="btnDownloadPdf" class="editor-btn" onclick="downloadPdfText()">PDF</button>
            </div>
        </div>

        <div class="editor-item">
            <img src="{{ asset('assets/images/print.svg') }}" alt="Print Icon" style="width:24px;height:24px;margin-right:8px;" />
            <button class="editor-btn" onclick="printCV()">Print</button>
        </div>

        <div class="editor-item">
            <img src="{{ asset('assets/images/edit.svg') }}" alt="Edit Icon" style="width:24px;height:24px;margin-right:8px;" />
            <a href="{{ route('pelamar.curriculum_vitae.profile.index', $cv->id) }}" class="editor-btn" data-i18n="editor.editData">Edit Data</a>
        </div>

        <div class="editor-item">
            <img src="{{ asset('assets/images/download.svg') }}" alt="Save Icon" style="width:24px;height:24px;margin-right:8px;" />
            <button id="saveToDashboardBtn"
                    data-template-id="{{ $cv->template_curriculum_vitae_id ?? 1 }}"
                    data-i18n="editor.saveDashboard">Simpan ke Dashboard</button>
        </div>

        <div class="editor-item">
            <img src="{{ asset('assets/images/home.svg') }}" alt="Home Icon" style="width:24px;height:24px;margin-right:8px;" />
            <a href="{{ route('pelamar.dashboard.curriculum_vitae.index') }}"
               class="editor-btn"
               data-i18n="editor.backDashboard">Kembali ke Dashboard</a>
        </div>
    </div>
    @endif

    {{-- ===== Right Panel (Preview) ===== --}}
    <div class="container">

        {{-- CSS dari style_json (ATS & Kreatif) --}}
        @if(!empty($style['css'] ?? null))
            <style>
                {!! $style['css'] !!}
            </style>
        @endif

        <div id="content">
            @if(!empty($isGrapesTemplate) && $isGrapesTemplate && !empty($grapesRenderedHtml))
                {{-- ========== MODE KREATIF (GRAPESJS) ========== --}}
                {!! $grapesRenderedHtml !!}
            @else
                {{-- ========== MODE ATS (layout_json lama) ========== --}}

                {{-- Section bawaan template --}}
                @foreach($layout as $section)
                    @php
                        $key = is_array($section) ? ($section['key'] ?? null) : null;
                    @endphp
                    @continue(!$key)

                    <div class="section {{ $key }}" data-key="{{ $key }}">
                        <span class="drag-indicator"
                              style="display:none;position:absolute;left:8px;top:12px;font-size:20px;color:#3538cd;cursor:grab;z-index:10;">
                            &#x2630;
                        </span>
                        <span class="delete-indicator"
                              style="display:none;position:absolute;right:8px;top:12px;font-size:20px;color:#e74c3c;cursor:pointer;z-index:10;"
                              title="Hapus section">
                            &#128465;
                        </span>

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

                {{-- Add Section --}}
                @if(!$isExport)
                <div id="custom-add-wrapper" class="export-hidden" style="margin-top:16px;">
                    <div class="add-section-wrapper">
                        <button id="btn-add-section" type="button" class="add-section-toggle">
                            <span data-i18n="addSection.title">Add section</span>
                            <span id="add-section-chevron" class="add-section-chevron">▾</span>
                        </button>

                        <div id="add-section-menu" class="add-section-menu export-hidden">
                            <button class="add-section-item" data-type="custom_text">
                                <span class="add-section-plus">+</span>
                                <span data-i18n="addSection.text">Text section</span>
                            </button>

                            <button class="add-section-item" data-type="custom_date">
                                <span class="add-section-plus">+</span>
                                <span data-i18n="addSection.dated">Custom dated section</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            @endif
        </div>

        {{-- ==== STYLE UNTUK SECTION, ADD SECTION, DLL ==== --}}
        <style>
            /* ============================================================
   CV PREVIEW + EXPORT/PRINT (FULL CSS)
   Tujuan:
   1) UI editor (panel kiri, add section, indikator) tidak ikut ke PDF
   2) PDF hasil window.print tetap TEXT (bisa dicopy)
   3) Hilangkan SEMUA border/outline dashed / box highlight edit
      (tanggal, deskripsi pengalaman kerja, link-link atas yang kebentuk kotak)
   ============================================================ */

/* =========================
   A4 + PRINT SETUP
   ========================= */
@page {
  size: A4;
  margin: 12mm;
}

html, body {
  -webkit-print-color-adjust: exact;
  print-color-adjust: exact;
}

/* =========================
   BASE SECTION LAYOUT
   ========================= */
#content { position: relative; }
.section { position: relative; }

/* indikator drag/delete (UI editor) */
.drag-indicator,
.delete-indicator {
  transition: opacity .2s;
  pointer-events: auto;
  background: #fff;
  border-radius: 4px;
  box-shadow: 0 1px 4px rgba(0,0,0,.07);
  padding: 2px 6px;
}

/* hanya terlihat saat hover (di layar) */
.section:hover .drag-indicator,
.section:hover .delete-indicator {
  display: inline-block !important;
  opacity: 1;
}

/* =========================
   ADD SECTION UI
   ========================= */
.add-section-wrapper { max-width: 320px; }

.add-section-toggle {
  width: 100%;
  border-radius: 4px;
  border: 1px dashed #4f46e5;
  background: #e5f0ff;
  padding: 10px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-family: Poppins, sans-serif;
  font-size: 16px;
  line-height: 1.4;
  cursor: pointer;
}

.add-section-toggle.open {
  background: #eef2ff;
  border-style: solid;
}

.add-section-chevron { font-size: 16px; }

.add-section-menu {
  display: none;
  margin-top: 4px;
  width: 100%;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 0 0 4px 4px;
  box-shadow: 0 10px 20px rgba(15, 23, 42, 0.12);
  padding: 4px 0;
}

.add-section-item {
  width: 100%;
  border: none;
  background: transparent;
  padding: 8px 12px;
  display: flex;
  align-items: center;
  gap: 10px;
  font-family: Poppins, sans-serif;
  font-size: 14px;
  text-align: left;
  cursor: pointer;
}

.add-section-item:hover { background: #f3f4ff; }

.add-section-plus {
  width: 24px;
  height: 24px;
  border-radius: 9999px;
  border: 2px solid #4f46e5;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  line-height: 1;
}

/* ============================================================
   EXPORT MODE (body.export-mode)  -> dipakai sebelum window.print()
   ============================================================ */
body.export-mode {
  background: #fff !important;
}

/* 1) SEMUA UI editor disembunyikan */
body.export-mode .editor-panel,
body.export-mode .export-hidden,
body.export-mode .drag-indicator,
body.export-mode .delete-indicator,
body.export-mode #custom-add-wrapper,
body.export-mode .add-section-wrapper,
body.export-mode .add-section-toggle,
body.export-mode #add-section-menu,
body.export-mode .add-section-menu {
  display: none !important;
}

/* 2) Lebarkan preview jadi full saat export */
body.export-mode .main-container {
  display: block !important;
  padding: 0 !important;
  margin: 0 !important;
}

body.export-mode .container {
  width: 100% !important;
  max-width: none !important;
  margin: 0 auto !important;
  padding: 0 !important;
}

body.export-mode #content {
  margin: 0 !important;
  padding: 0 !important;
}

/* 3) FIX UTAMA: hilangkan SEMUA kotak putus-putus / highlight editor */
/*    (tanggal, deskripsi pengalaman kerja, link, dan semua span editable) */
body.export-mode [contenteditable="true"],
body.export-mode [contenteditable="true"] *,
body.export-mode .inline-edit,
body.export-mode .inline-edit *,
body.export-mode .editable,
body.export-mode .editable *,
body.export-mode .pill,
body.export-mode .pill *,
body.export-mode .custom-date-pill,
body.export-mode .custom-date-pill * {
  border: none !important;
  outline: none !important;
  box-shadow: none !important;
  background: transparent !important;
}

/* 4) Kalau ada class dashed/outline/border-dashed di template */
body.export-mode .dashed,
body.export-mode .outline-dashed,
body.export-mode .border-dashed,
body.export-mode .border--dashed,
body.export-mode .outline--dashed {
  border: none !important;
  outline: none !important;
  box-shadow: none !important;
  background: transparent !important;
}

/* 5) Kill-switch: kalau ada inline style "dashed" yang keburu nempel karena hover */
body.export-mode *[style*="dashed"],
body.export-mode *[style*="outline: 1px dashed"],
body.export-mode *[style*="border: 1px dashed"] {
  border: none !important;
  outline: none !important;
  box-shadow: none !important;
}

/* 6) Link: hilangkan kotak/underline custom export-mask (cv-link) */
body.export-mode a,
body.export-mode a:visited,
body.export-mode a:hover,
body.export-mode a:active,
body.export-mode a * {
  border: none !important;
  outline: none !important;
  box-shadow: none !important;
  background: transparent !important;
  text-decoration: none !important; /* kalau mau underline standar: ganti jadi underline */
}

/* matikan underline pseudo element (kalau ada) */
body.export-mode a::after,
body.export-mode .cv-link::after {
  content: none !important;
  display: none !important;
}

/* 7) Pastikan efek hover/selection tidak bikin border muncul */
body.export-mode .section:hover .drag-indicator,
body.export-mode .section:hover .delete-indicator {
  display: none !important;
}

/* Hilangkan border/box preview saat export/print */
body.export-mode .container,
body.export-mode #content,
body.export-mode .main-container,
body.export-mode .cv-paper,
body.export-mode .paper {
  border: none !important;
  outline: none !important;
  box-shadow: none !important;
  background: #fff !important;
}

@media print {
  .container,
  #content,
  .main-container,
  .cv-paper,
  .paper {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    background: #fff !important;
  }
}

/* ============================================================
   PRINT MODE (double safety)
   ============================================================ */
@media print {
  /* sembunyikan semua UI editor */
  .editor-panel,
  .export-hidden,
  .drag-indicator,
  .delete-indicator,
  #custom-add-wrapper,
  .add-section-wrapper,
  .add-section-toggle,
  #add-section-menu,
  .add-section-menu {
    display: none !important;
  }

  /* layout full */
  .main-container {
    display: block !important;
    padding: 0 !important;
    margin: 0 !important;
  }

  .container {
    width: 100% !important;
    max-width: none !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  #content {
    margin: 0 !important;
    padding: 0 !important;
  }

  body {
    background: #fff !important;
  }

  /* hilangkan efek edit / kotak dashed */
  [contenteditable="true"],
  [contenteditable="true"] *,
  .inline-edit,
  .inline-edit *,
  .editable,
  .editable *,
  .pill,
  .pill *,
  .custom-date-pill,
  .custom-date-pill * {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    background: transparent !important;
  }

  .dashed,
  .outline-dashed,
  .border-dashed,
  .border--dashed,
  .outline--dashed {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    background: transparent !important;
  }

  *[style*="dashed"],
  *[style*="outline: 1px dashed"],
  *[style*="border: 1px dashed"] {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
  }

  /* link bersih */
  a,
  a:visited,
  a:hover,
  a:active,
  a * {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    background: transparent !important;
    text-decoration: none !important;
  }

  a::after,
  .cv-link::after {
    content: none !important;
    display: none !important;
  }
}
        </style>

        {{-- ==== SCRIPTS (biarkan utuh) ==== --}}
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

        <script>
            // =========================
            // i18n (Language Switcher)
            // =========================
            const CV_I18N = {
                id: {
                    "section.skills": "KEAHLIAN",
                    "skill.cat.programming": "Bahasa Pemrograman",
                    "skill.cat.frameworks": "Framework & Library",
                    "skill.cat.database": "Database",
                    "skill.cat.tools": "Tools & Platform",
                    "skill.cat.testing": "Testing & Debugging",
                    "skill.cat.others_web": "Keahlian Web Lainnya",
                    "skill.cat.soft": "Soft Skills",
                    "editor.title": "Editor",
                },
                en: {
                    "section.skills": "SKILLS",
                    "skill.cat.programming": "Programming Language",
                    "skill.cat.frameworks": "Framework & Libraries",
                    "skill.cat.database": "Database",
                    "skill.cat.tools": "Tools & Platforms",
                    "skill.cat.testing": "Testing & Debugging",
                    "skill.cat.others_web": "Other Web Development Skills",
                    "skill.cat.soft": "Soft Skills",
                    "editor.title": "Editor",
                }
            };

            function getCvLang() {
                return localStorage.getItem("cv_lang") || "id";
            }
            function setCvLang(lang) {
                localStorage.setItem("cv_lang", lang);
            }

            function t(key, fallbackText = "") {
                const lang = getCvLang();
                const dict = CV_I18N[lang] || {};
                if (dict[key]) return dict[key];
                return fallbackText || key;
            }

            function applyLanguageToPreview(root = document) {
                root.querySelectorAll("[data-i18n]").forEach(el => {
                    const key = el.getAttribute("data-i18n");
                    if (!key) return;

                    if (el.isContentEditable || el.getAttribute("contenteditable") === "true") return;
                    if (el.closest('[contenteditable="true"]')) return;

                    if (!el.dataset.i18nDefault) {
                        el.dataset.i18nDefault = (el.textContent || "").trim();
                    }
                    el.textContent = t(key, el.dataset.i18nDefault);
                });

                root.querySelectorAll("[data-i18n-placeholder]").forEach(el => {
                    const key = el.getAttribute("data-i18n-placeholder");
                    if (!key) return;

                    if (el.isContentEditable || el.getAttribute("contenteditable") === "true") return;
                    if (el.closest('[contenteditable="true"]')) return;

                    if (!el.dataset.i18nPlaceholderDefault) {
                        el.dataset.i18nPlaceholderDefault = el.getAttribute("placeholder") || "";
                    }
                    el.setAttribute("placeholder", t(key, el.dataset.i18nPlaceholderDefault));
                });
            }

            function changeLanguage(selectEl) {
                const lang = selectEl.value;
                setCvLang(lang);
                applyLanguageToPreview(document);
            }

            // =========================
            // Helpers UI
            // =========================
            function hexToRgb(hex) {
                hex = hex.replace(/^#/, "");
                if (hex.length === 3) hex = hex.split("").map((x) => x + x).join("");
                const num = parseInt(hex, 16);
                return { r: (num >> 16) & 255, g: (num >> 8) & 255, b: num & 255 };
            }

            function changeFont(selectElement) {
                const content = document.getElementById("content");
                if (content) content.style.fontFamily = selectElement.value;
            }

            function changeBackgroundColor(color) {
                let panel = document.querySelector(".left-panel") || document.getElementById("content");
                if (!panel) return;

                panel.style.backgroundColor = color;

                const rgb = hexToRgb(color);
                const brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
                const textColor = brightness > 128 ? "#000000" : "#FFFFFF";

                panel.style.color = textColor;
                panel.querySelectorAll("p,h1,h2,h3,a,span,div,li").forEach((el) => {
                    el.style.color = textColor;
                });
            }

            // ===== Export mask (untuk PNG/JPEG) =====
            function applyExportMask(root) {
                if (!root) return () => {};

                const styleEl = document.createElement("style");
                styleEl.id = "export-mask-style";
                styleEl.textContent = `
                    #content.exporting .inline-edit:empty:before { content: "" !important; }
                    #content.exporting .export-hidden,
                    #content.exporting .drag-indicator,
                    #content.exporting .delete-indicator { display: none !important; }

                    #content.exporting .cv-link,
                    #content.exporting .cv-link:visited,
                    #content.exporting .cv-link:hover,
                    #content.exporting .cv-link:active{
                        color:#2563eb !important;
                        text-decoration:none !important;
                        position:relative !important;
                        display:inline-block !important;
                        padding-bottom:2px !important;
                    }

                    #content.exporting .cv-link::after{
                        content:"" !important;
                        position:absolute !important;
                        left:0 !important;
                        right:0 !important;
                        bottom:0 !important;
                        height:1px !important;
                        background:#2563eb !important;
                        border-radius:0 !important;
                    }

                    #content.exporting .cv-link *{
                        color:inherit !important;
                        text-decoration:none !important;
                    }
                `;

                document.head.appendChild(styleEl);
                root.classList.add("exporting");

                const hiddenEls = Array.from(
                    root.querySelectorAll(".export-hidden, .drag-indicator, .delete-indicator")
                );
                hiddenEls.forEach((el) => {
                    el.dataset._display = el.style.display;
                    el.style.display = "none";
                });

                return () => {
                    hiddenEls.forEach((el) => {
                        el.style.display = el.dataset._display || "";
                        delete el.dataset._display;
                    });
                    root.classList.remove("exporting");
                    styleEl.remove();
                };
            }

            // =========================
            // Export PNG/JPEG (canvas)
            // =========================
            async function downloadAsImage(type) {
                const content = document.getElementById("content");
                if (!content) return;

                const cleanup = applyExportMask(content);
                const canvas = await html2canvas(content, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: "#ffffff",
                    scrollY: -window.scrollY,
                    windowWidth: content.scrollWidth,
                    windowHeight: content.scrollHeight,
                });
                cleanup();

                const image = canvas.toDataURL(`image/${type}`);
                const link = document.createElement("a");
                link.href = image;
                link.download = `cv.${type}`;
                link.click();
            }

            function printCV() {
                window.print();
            }

            // =========================
            // Spinner Button Helpers
            // =========================
            function ensureSpinnerCss() {
                if (document.getElementById("btn-spinner-style")) return;
                const css = document.createElement("style");
                css.id = "btn-spinner-style";
                css.textContent = `
                    @keyframes spin360 { to { transform: rotate(360deg); } }
                    .btn-loading { pointer-events: none; opacity: .9; }
                    .btn-loading .spinner {
                        width: 16px; height: 16px; margin-right: 8px;
                        display: inline-block; vertical-align: -2px;
                    }
                    .btn-loading .spinner svg { width: 16px; height: 16px; animation: spin360 1s linear infinite; }
                `;
                document.head.appendChild(css);
            }

            function setBtnLoading(btn, isLoading, textWhenLoading = "Saving...") {
                if (!btn) return;
                ensureSpinnerCss();

                if (isLoading) {
                    if (!btn.dataset.originalHtml) btn.dataset.originalHtml = btn.innerHTML;
                    btn.classList.add("btn-loading");
                    btn.disabled = true;

                    const spinnerSvg = `
                        <span class="spinner" aria-hidden="true">
                            <svg viewBox="0 0 50 50" fill="none">
                                <circle cx="25" cy="25" r="20" stroke="currentColor" stroke-opacity=".2" stroke-width="6"/>
                                <path d="M45 25a20 20 0 0 1-20 20" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
                            </svg>
                        </span>`;
                    btn.innerHTML = `${spinnerSvg}<span>${textWhenLoading}</span>`;
                } else {
                    btn.classList.remove("btn-loading");
                    btn.disabled = false;
                    if (btn.dataset.originalHtml) {
                        btn.innerHTML = btn.dataset.originalHtml;
                        delete btn.dataset.originalHtml;
                    } else {
                        btn.textContent = "Simpan ke Dashboard";
                    }
                }
            }

            // =========================
            // Save to Dashboard (TEXT PDF - server)
            // =========================
            async function savePdfToDashboard() {
                const btn = document.getElementById("saveToDashboardBtn");
                if (!btn) return;

                setBtnLoading(btn, true, "Saving...");

                try {
                    const csrfToken =
                        document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

                    const templateId = btn.dataset.templateId || "";
                    const lang = getCvLang();

                    const url = "{{ route('pelamar.cv.saveTextPdf', $cv->id) }}" + "?lang=" + encodeURIComponent(lang);

                    const res = await fetch(url, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                        },
                        body: JSON.stringify({ template_id: templateId })
                    });

                    const data = await res.json();

                    if (data.success) {
                        const newId = (data.data && data.data.id) || data.new_cv_id;
                        if (newId) {
                            localStorage.setItem("cv_just_saved_id", String(newId));
                            localStorage.setItem("cv_just_saved_at", String(Date.now()));
                        }

                        if (window.Swal) {
                            await Swal.fire({
                                icon: "success",
                                title: "Berhasil",
                                text: data.message || "CV berhasil disimpan",
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            alert("CV berhasil disimpan");
                        }
                    } else {
                        const msg = data.message || "Gagal menyimpan CV";
                        if (window.Swal) Swal.fire({ icon: "error", title: "Gagal", text: msg });
                        else alert("Gagal menyimpan CV: " + msg);
                    }
                } catch (err) {
                    console.error(err);
                    alert("Terjadi error saat proses penyimpanan (lihat console).");
                } finally {
                    setBtnLoading(document.getElementById("saveToDashboardBtn"), false);
                }
            }

            // =========================
            // Existing inline edit logic (tetap)
            // =========================
            document.addEventListener('DOMContentLoaded', function () {

                // ✅ support ?export=1 atau ?pdf=1 (dipakai oleh Browsershot)
                const params = new URLSearchParams(window.location.search);
                const isExport = (params.get('export') === '1') || (params.get('pdf') === '1') || @json($isExport);

                if (params.get('export') === '1' || params.get('pdf') === '1') {
                   document.body.classList.add('export-mode');
                }

                const langSel = document.getElementById("langSelect");
                if (langSel) langSel.value = getCvLang();
                applyLanguageToPreview(document);

                const content    = document.getElementById('content');
                const addWrapper = document.getElementById('custom-add-wrapper');

                function bindGlobalInline(scope) {
                    const sel = "#content [contenteditable='true']";
                    (scope || document).querySelectorAll(sel).forEach(el => {
                        if (el.closest('.section.custom-text')) return;

                        el.setAttribute('spellcheck', 'false');
                        el.addEventListener('mouseenter', () => {
                            el.style.outline = '1px dashed #3538cd';
                            el.style.cursor = 'text';
                        });
                        el.addEventListener('mouseleave', () => {
                            el.style.outline = 'none';
                        });

                        el.addEventListener('blur', () => {
                            const section = el.dataset.section;
                            const field   = el.dataset.field;
                            const id      = el.dataset.id || null;
                            if (!section || !field) return;

                            const normalizeText = (txt) => (txt || "")
                                .replace(/\u00A0/g, " ")
                                .replace(/\u200B/g, "")
                                .replace(/\s+/g, " ")
                                .trim();

                            const normalizeHtml = (html) => (html || "")
                                .replace(/<br\s*\/?>/gi, "")
                                .replace(/&nbsp;/gi, " ")
                                .replace(/\u00A0/g, " ")
                                .replace(/\u200B/g, "")
                                .replace(/\s+/g, " ")
                                .trim();

                            const rawText = normalizeText(el.innerText);
                            const rawHtml = normalizeHtml(el.innerHTML);

                            const isEmpty = (rawText === "" && rawHtml === "");
                            const isSkillName = (section === "skills" && field === "skill_name" && id);

                            if (isSkillName && isEmpty) {
                                el.remove();
                                cleanupSkillsSection();
                            }

                            fetch("{{ route('pelamar.curriculum_vitae.updateInline') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    cv_id: "{{ $cv->id }}",
                                    section,
                                    field,
                                    value: isEmpty ? "" : rawText,
                                    id,
                                    delete: (isSkillName && isEmpty)
                                })
                            })
                            .then(r => r.ok ? r.json() : r.json().then(j => Promise.reject(j)))
                            .then((res) => {
                                if (isSkillName && res && res.action === "deleted") {
                                    cleanupSkillsSection();
                                }
                            })
                            .catch(err => console.warn("Inline edit failed/ignored:", err));
                        });
                    });
                }

                function bindCustomEditable(scope) {
                    const targets = (scope || document).querySelectorAll(".section.custom-text .editable[data-custom-id]");
                    targets.forEach(el => {
                        el.addEventListener('blur', () => {
                            const id    = el.dataset.customId;
                            const field = el.dataset.field;
                            if (!id || !field) return;

                            let value;
                            if (field === 'payload.body') value = el.innerHTML.trim();
                            else value = el.innerText.trim();

                            fetch(`{{ url('/curriculum-vitae/'.$cv->id.'/custom-sections') }}/${id}`, {
                                method: "PUT",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({ field, value })
                            })
                            .then(r => r.ok ? r.json() : r.json().then(j => Promise.reject(j)))
                            .catch(err => console.error('Custom save failed:', err));
                        });
                    });
                }

                function todayIso() {
                    const d = new Date();
                    const m = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');
                    return `${d.getFullYear()}-${m}-${day}`;
                }

                function formatDateDisplay(iso, isStart) {
                    if (!iso) return 'dd-mm-yyyy';
                    if (!isStart && iso === todayIso()) return 'Sekarang';
                    const parts = iso.split('-');
                    if (parts.length !== 3) return iso;
                    return `${parts[2]}-${parts[1]}-${parts[0]}`;
                }

                function bindCustomDates(scope) {
                    const root = scope || document;

                    root.querySelectorAll('.custom-date-pill').forEach(pill => {
                        if (pill.dataset.bound === '1') return;
                        pill.dataset.bound = '1';

                        pill.addEventListener('click', () => {
                            const section = pill.closest('.section.custom-text');
                            if (!section) return;
                            const startInput = section.querySelector('input.custom-date-input[data-field="payload.items.0.start_date"]');
                            if (startInput) {
                                if (startInput.showPicker) startInput.showPicker();
                                else startInput.focus();
                            }
                        });
                    });

                    root.querySelectorAll('input.custom-date-input').forEach(inp => {
                        if (inp.dataset.bound === '1') return;
                        inp.dataset.bound = '1';

                        inp.addEventListener('change', () => {
                            const id    = inp.dataset.customId;
                            const field = inp.dataset.field;
                            const value = inp.value;
                            if (!id || !field) return;

                            fetch(`{{ url('/curriculum-vitae/'.$cv->id.'/custom-sections') }}/${id}`, {
                                method: "PUT",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({ field, value })
                            })
                            .then(r => r.ok ? r.json() : r.json().then(j => Promise.reject(j)))
                            .then(() => {
                                const section = inp.closest('.section.custom-text');
                                if (!section) return;
                                const pill = section.querySelector('.custom-date-pill');
                                const startInput = section.querySelector('input.custom-date-input[data-field="payload.items.0.start_date"]');
                                const endInput   = section.querySelector('input.custom-date-input[data-field="payload.items.0.end_date"]');

                                if (pill && startInput && endInput) {
                                    pill.textContent =
                                        formatDateDisplay(startInput.value, true) + ' – ' +
                                        formatDateDisplay(endInput.value, false);
                                }

                                if (field === 'payload.items.0.start_date' && endInput) {
                                    if (endInput.showPicker) endInput.showPicker();
                                    else endInput.focus();
                                }
                            })
                            .catch(err => console.error('Save date failed:', err));
                        });
                    });
                }

                function bindCustomDelete(scope) {
                    const dels = (scope || document).querySelectorAll(".section.custom-text .custom-delete[data-custom-id]");
                    dels.forEach(btn => {
                        btn.addEventListener('click', () => {
                            const id = btn.dataset.customId;
                            fetch(`{{ url('/curriculum-vitae/'.$cv->id.'/custom-sections') }}/${id}`, {
                                method: "DELETE",
                                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                            }).then(() => {
                                const host = btn.closest('.section');
                                if (host) host.remove();
                                saveCustomOrder();
                            });
                        });
                    });
                }

                const btnAdd   = document.getElementById('btn-add-section');
                const menu     = document.getElementById('add-section-menu');
                const chevron  = document.getElementById('add-section-chevron');

                if (btnAdd && menu) {
                    btnAdd.addEventListener('click', () => {
                        const willOpen = (menu.style.display === 'none' || menu.style.display === '');
                        menu.style.display = willOpen ? 'block' : 'none';
                        btnAdd.classList.toggle('open', willOpen);
                        if (chevron) chevron.textContent = willOpen ? '▴' : '▾';
                    });

                    menu.querySelectorAll('button[data-type]').forEach(b => {
                        b.addEventListener('click', () => createCustomSection(b.dataset.type));
                    });
                }

                function createCustomSection(type) {
                    fetch("{{ route('pelamar.curriculum_vitae.custom.add', $cv->id) }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ type })
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (!res.success) return;
                        const wrapper = document.createElement('div');
                        wrapper.innerHTML = res.html;
                        const node = wrapper.firstElementChild;

                        if (addWrapper) content.insertBefore(node, addWrapper);
                        else content.appendChild(node);

                        bindGlobalInline(node);
                        bindCustomEditable(node);
                        bindCustomDates(node);
                        bindCustomDelete(node);

                        applyLanguageToPreview(node);

                        saveCustomOrder();

                        if (menu) menu.style.display = 'none';
                        if (btnAdd) btnAdd.classList.remove('open');
                        if (chevron) chevron.textContent = '▾';
                    });
                }

                if (typeof Sortable !== 'undefined' && content) {
                    Sortable.create(content, {
                        animation: 150,
                        handle: '.section',
                        draggable: '.section',
                        onEnd: saveCustomOrder
                    });
                }

                function saveCustomOrder() {
                    const ids = Array.from(
                        content.querySelectorAll('.section.custom-text[data-custom-id]')
                    ).map(el => el.dataset.customId);

                    if (!ids.length) return;

                    fetch("{{ route('pelamar.curriculum_vitae.custom.reorder', $cv->id) }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ ids })
                    });
                }

                function cleanupSkillsSection() {
                    const normalizeText = (txt) => (txt || "")
                        .replace(/\u00A0/g, " ")
                        .replace(/\u200B/g, "")
                        .replace(/\s+/g, " ")
                        .trim();

                    const normalizeHtml = (html) => (html || "")
                        .replace(/<br\s*\/?>/gi, "")
                        .replace(/&nbsp;/gi, " ")
                        .replace(/\u00A0/g, " ")
                        .replace(/\u200B/g, "")
                        .replace(/\s+/g, " ")
                        .trim();

                    const skillsRoot = document.querySelector('.section.skills') || document;

                    skillsRoot.querySelectorAll('li.skill-li').forEach(li => {
                        const wrap = li.querySelector('.skill-items');
                        if (!wrap) return;

                        const spans = Array.from(
                            wrap.querySelectorAll("span.inline-edit[data-section='skills'][data-field='skill_name']")
                        ).filter(sp => {
                            const t = normalizeText(sp.innerText);
                            const h = normalizeHtml(sp.innerHTML);
                            return !(t === "" && h === "");
                        });

                        if (spans.length === 0) {
                            li.remove();
                            return;
                        }

                        wrap.innerHTML = "";
                        spans.forEach((sp, i) => {
                            wrap.appendChild(sp);
                            if (i < spans.length - 1) wrap.appendChild(document.createTextNode(", "));
                        });
                    });
                }

                if (content) {
                    content.querySelectorAll('.section').forEach(sec => {
                        sec.addEventListener('mouseenter', () => {
                            const d = sec.querySelector('.drag-indicator');
                            const x = sec.querySelector('.delete-indicator');
                            if (d) d.style.display = 'inline-block';
                            if (x) x.style.display = 'inline-block';
                        });
                        sec.addEventListener('mouseleave', () => {
                            const d = sec.querySelector('.drag-indicator');
                            const x = sec.querySelector('.delete-indicator');
                            if (d) d.style.display = 'none';
                            if (x) x.style.display = 'none';
                        });
                    });
                }

                // initial bind
                bindGlobalInline();
                bindCustomEditable();
                bindCustomDates();
                bindCustomDelete();
                cleanupSkillsSection();

                // bind save
                const btnSave = document.getElementById("saveToDashboardBtn");
                if (btnSave) btnSave.addEventListener("click", savePdfToDashboard);
            });
        </script>
    </div>
</div>

{{-- ✅ Jika Blade sudah tahu ini export, langsung set class body juga (tanpa nunggu JS param) --}}
@if($isExport)
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.body.classList.add('export-mode');
    });
</script>
@endif

@endsection
