@extends('layouts.cv')

@section('content')
<div class="main-container">
  {{-- ===== Left Editor Panel ===== --}}
  <div class="editor-panel">
    <div style="text-align:center;color:#3538cd;font-size:32px;font-family:Poppins;font-weight:400;line-height:41px;">
      Editor
    </div>

    <div class="editor-item font-dropdown">
      <img src="{{asset('assets/images/font.svg')}}" alt="Font Icon" style="width:24px;height:24px;margin-right:8px;" />
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
      <img src="{{asset('assets/images/download.svg')}}" alt="Download Icon" style="width:24px;height:24px;margin-right:8px;" />
      <div style="display:flex;gap:8px;">
        <button class="editor-btn" onclick="downloadAsImage('png')">PNG</button>
        <button class="editor-btn" onclick="downloadAsImage('jpeg')">JPEG</button>
        <button class="editor-btn" onclick="downloadAsPDF()">PDF</button>
      </div>
    </div>

    <div class="editor-item">
      <img src="{{asset('assets/images/print.svg')}}" alt="Print Icon" style="width:24px;height:24px;margin-right:8px;" />
      <button class="editor-btn" onclick="printCV()">Print</button>
    </div>

    <div class="editor-item">
      <img src="{{asset('assets/images/edit.svg')}}" alt="Edit Icon" style="width:24px;height:24px;margin-right:8px;" />
      <a href="{{route('pelamar.curriculum_vitae.profile.index', $cv->id)}}" class="editor-btn">Edit Data</a>
    </div>

    <div class="editor-item">
      <img src="{{asset('assets/images/download.svg')}}" alt="Save Icon" style="width:24px;height:24px;margin-right:8px;" />
      <button id="saveToDashboardBtn" data-template-id="{{ $cv->template_curriculum_vitae_id ?? 1 }}">Save to Dashboard</button>
    </div>

    <div class="editor-item">
      <img src="{{asset('assets/images/home.svg')}}" alt="Home Icon" style="width:24px;height:24px;margin-right:8px;" />
      <a href="{{route('pelamar.dashboard.curriculum_vitae.index')}}" class="editor-btn">Return to Dashboard</a>
    </div>
  </div>

  {{-- ===== Right Panel (Preview) ===== --}}
  <div class="container">
    <div id="content">
      {{-- Section bawaan template --}}
      @foreach($layout as $section)
        <div class="section {{ $section['key'] }}" data-key="{{ $section['key'] }}">
          <span class="drag-indicator" style="display:none;position:absolute;left:8px;top:12px;font-size:20px;color:#3538cd;cursor:grab;z-index:10;">&#x2630;</span>
          <span class="delete-indicator" style="display:none;position:absolute;right:8px;top:12px;font-size:20px;color:#e74c3c;cursor:pointer;z-index:10;" title="Hapus section">&#128465;</span>

          @includeIf('pelamar.curriculum_vitae.preview.sections.' . $section['key'], ['cv' => $cv])
        </div>
      @endforeach

      {{-- Custom sections buatan pelamar --}}
      @foreach($cv->customSections as $custom)
            @includeIf('pelamar.curriculum_vitae.preview.sections.' . $custom->section_key, [
                'cv'      => $cv,
                'section' => $custom,
            ])
        @endforeach

      {{-- Add Section (baru, mirip screenshot) --}}
      <div id="custom-add-wrapper" class="export-hidden" style="margin-top:16px;">
        <div class="add-section-wrapper">
          <button id="btn-add-section" type="button" class="add-section-toggle">
            <span>Add section</span>
            <span id="add-section-chevron" class="add-section-chevron">▾</span>
          </button>

          <div id="add-section-menu" class="add-section-menu export-hidden">
            <button class="add-section-item" data-type="custom_text">
              <span class="add-section-plus">+</span>
              <span>Text section</span>
            </button>

            <button class="add-section-item" data-type="custom_date">
              <span class="add-section-plus">+</span>
              <span>Custom dated section</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <style>
      #content { position: relative; }
      .section { position: relative; }

      .drag-indicator, .delete-indicator {
        transition: opacity .2s;
        pointer-events:auto;
        background:#fff;
        border-radius:4px;
        box-shadow:0 1px 4px rgba(0,0,0,.07);
        padding:2px 6px;
      }

      .section:hover .drag-indicator,
      .section:hover .delete-indicator {
        display:inline-block !important;
        opacity:1;
      }

      /* ==== Add Section style baru ==== */
      .add-section-wrapper {
        max-width: 320px;
      }

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

      .add-section-chevron {
        font-size: 16px;
      }

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

      .add-section-item:hover {
        background: #f3f4ff;
      }

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

      @media print {
        .editor-panel,
        .export-hidden,
        .drag-indicator,
        .delete-indicator {
          display: none !important;
        }
      }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const content    = document.getElementById('content');
    const addWrapper = document.getElementById('custom-add-wrapper');

    // ===== 1) Inline edit global (skip custom text)
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

                const value = el.innerText.trim();

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
                        value,
                        id
                    })
                })
                .then(r => r.ok ? r.json() : r.json().then(j => Promise.reject(j)))
                .then(() => console.log('Saved inline'))
                .catch(err => console.warn('Inline edit failed/ignored:', err));
            });
        });
    }

    // ===== 2) Inline edit CUSTOM
    function bindCustomEditable(scope) {
        const targets = (scope || document).querySelectorAll(".section.custom-text .editable[data-custom-id]");
        targets.forEach(el => {
            el.addEventListener('blur', () => {
                const id    = el.dataset.customId;
                const field = el.dataset.field;
                if (!id || !field) return;

                let value;
                if (field === 'payload.body') {
                    value = el.innerHTML.trim();
                } else {
                    // untuk span/elemen biasa
                    value = el.innerText.trim();
                }

                fetch(`{{ url('/curriculum-vitae/'.$cv->id.'/custom-sections') }}/${id}`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ field, value })
                })
                .then(r => r.ok ? r.json() : r.json().then(j => Promise.reject(j)))
                .then(() => console.log('Saved custom'))
                .catch(err => console.error('Custom save failed:', err));
            });
        });
    }

    // ===== 3) Custom date (start_date & end_date dengan date picker)
    function todayIso() {
        const d = new Date();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${d.getFullYear()}-${m}-${day}`;
    }

    // isStart = true untuk start_date, false untuk end_date
    function formatDateDisplay(iso, isStart) {
        if (!iso) return 'dd-mm-yyyy';

        // khusus END DATE: kalau sama dengan hari ini → "Sekarang"
        if (!isStart && iso === todayIso()) {
            return 'Sekarang';
        }

        const parts = iso.split('-'); // [Y,M,D]
        if (parts.length !== 3) return iso;
        return `${parts[2]}-${parts[1]}-${parts[0]}`; // dd-mm-yyyy
    }

    function bindCustomDates(scope) {
        const root = scope || document;

        // klik chip → buka picker START DATE
        root.querySelectorAll('.custom-date-pill').forEach(pill => {
            if (pill.dataset.bound === '1') return;
            pill.dataset.bound = '1';

            pill.addEventListener('click', () => {
                const section = pill.closest('.section.custom-text');
                if (!section) return;
                const startInput = section.querySelector('input.custom-date-input[data-field="payload.items.0.start_date"]');
                if (startInput) {
                    if (startInput.showPicker) {
                        startInput.showPicker();
                    } else {
                        startInput.focus();
                    }
                }
            });
        });

        // perubahan tanggal → simpan + update chip
        root.querySelectorAll('input.custom-date-input').forEach(inp => {
            if (inp.dataset.bound === '1') return;
            inp.dataset.bound = '1';

            inp.addEventListener('change', () => {
                const id    = inp.dataset.customId;
                const field = inp.dataset.field;
                const value = inp.value; // YYYY-MM-DD
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

                    // === FLOW BARU: setelah set START DATE → otomatis buka END DATE ===
                    if (field === 'payload.items.0.start_date' && endInput) {
                        if (endInput.showPicker) {
                            endInput.showPicker();
                        } else {
                            endInput.focus();
                        }
                    }
                })
                .catch(err => console.error('Save date failed:', err));
            });
        });
    }

    // ===== 4) Delete CUSTOM
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

    // ===== 5) Add Section dropdown
    const btnAdd   = document.getElementById('btn-add-section');
    const menu     = document.getElementById('add-section-menu');
    const chevron  = document.getElementById('add-section-chevron');

    if (btnAdd && menu) {
        btnAdd.addEventListener('click', () => {
            const willOpen = (menu.style.display === 'none' || menu.style.display === '');
            menu.style.display = willOpen ? 'block' : 'none';
            btnAdd.classList.toggle('open', willOpen);
            if (chevron) {
                chevron.textContent = willOpen ? '▴' : '▾';
            }
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

            if (addWrapper) {
                content.insertBefore(node, addWrapper);
            } else {
                content.appendChild(node);
            }

            bindGlobalInline(node);
            bindCustomEditable(node);
            bindCustomDates(node);
            bindCustomDelete(node);

            saveCustomOrder();

            if (menu) menu.style.display = 'none';
            if (btnAdd) btnAdd.classList.remove('open');
            if (chevron) chevron.textContent = '▾';
        });
    }

    // ===== 6) Sortable
    Sortable.create(content, {
        animation: 150,
        handle: '.section',
        draggable: '.section',
        onEnd: saveCustomOrder
    });

    function saveCustomOrder() {
        const ids = Array.from(
            content.querySelectorAll('.section.custom-text[data-custom-id]')
        ).map(el => el.dataset.customId);

        fetch("{{ route('pelamar.curriculum_vitae.custom.reorder', $cv->id) }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ ids })
        });
    }

    // ===== 7) Hover indicators
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

    // initial bind
    bindGlobalInline();
    bindCustomEditable();
    bindCustomDates();
    bindCustomDelete();
});
</script>
  </div>
</div>
@endsection
