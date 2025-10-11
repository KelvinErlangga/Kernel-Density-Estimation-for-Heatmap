@extends('layouts.cv')

@section('content')
<div class="main-container">
  {{-- ===== Left Editor Panel (punya kamu) ===== --}}
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
      <a href="{{route('home')}}" class="editor-btn">Back to Home</a>
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
        @include('pelamar.curriculum_vitae.preview.sections.custom_text', ['cv' => $cv, 'section' => $custom])
      @endforeach

      {{-- Add Section --}}
      <div id="custom-add-wrapper" class="export-hidden" style="margin-top:16px;">
        <div class="editor-item">
            <button id="btn-add-section" class="editor-btn">Add section ▾</button>
            <div id="add-section-menu" class="export-hidden"
                style="display:none; margin-top:8px; background:#fff; border:1px solid #ddd; border-radius:8px; padding:8px; width:260px;">
            <button class="editor-btn" data-type="custom_text" style="width:100%;">Text section</button>
            </div>
        </div>
    </div>

    <style>
      #content { position: relative; }
      .section { position: relative; }
      .drag-indicator, .delete-indicator{
        transition: opacity .2s; pointer-events:auto; background:#fff; border-radius:4px;
        box-shadow:0 1px 4px rgba(0,0,0,.07); padding:2px 6px;
      }
      .section:hover .drag-indicator, .section:hover .delete-indicator { display:inline-block !important; opacity:1; }
      @media print {
        .editor-panel,
        .export-hidden,
        .drag-indicator,
        .delete-indicator { display: none !important; }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
      const content = document.getElementById('content');

      // ===== 1) Inline edit global (skip custom text)
      function bindGlobalInline(scope) {
        const sel = "#content [contenteditable='true']";
        (scope || document).querySelectorAll(sel).forEach(el => {
          if (el.closest('.section.custom-text')) return;

          el.setAttribute('spellcheck', 'false');
          el.addEventListener('mouseenter', () => { el.style.outline = '1px dashed #3538cd'; el.style.cursor = 'text'; });
          el.addEventListener('mouseleave', () => { el.style.outline = 'none'; });

          el.addEventListener('blur', () => {
            const section = el.dataset.section;
            const field   = el.dataset.field;
            const id      = el.dataset.id || null;
            if (!section || !field) return;

            const value = el.innerText.trim();

            fetch("{{ route('pelamar.curriculum_vitae.updateInline') }}", {
              method: "POST",
              headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
              body: JSON.stringify({ cv_id: "{{ $cv->id }}", section, field, value, id })
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

            const value = (field === 'payload.body') ? el.innerHTML.trim() : el.innerText.trim();
            fetch(`{{ url('/curriculum-vitae/'.$cv->id.'/custom-sections') }}/${id}`, {
              method: "PUT",
              headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
              body: JSON.stringify({ field, value })
            })
            .then(r => r.ok ? r.json() : r.json().then(j => Promise.reject(j)))
            .then(() => console.log('Saved custom'))
            .catch(err => console.error('Custom save failed:', err));
          });
        });
      }

      // ===== 3) Delete CUSTOM
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

      // ===== 4) Add Section (CUSTOM TEXT)
      const btnAdd = document.getElementById('btn-add-section');
      const menu   = document.getElementById('add-section-menu');
      if (btnAdd && menu) {
        btnAdd.addEventListener('click', () => {
          menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
        });
        menu.querySelectorAll('button[data-type]').forEach(b => {
          b.addEventListener('click', () => createCustomSection(b.dataset.type));
        });
      }

      function createCustomSection(type) {
        fetch("{{ route('pelamar.curriculum_vitae.custom.add', $cv->id) }}", {
          method: "POST",
          headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
          body: JSON.stringify({ type })
        })
        .then(r => r.json())
        .then(res => {
          if (!res.success) return;
          const wrapper = document.createElement('div');
          wrapper.innerHTML = res.html;
          const node = wrapper.firstElementChild;
          content.appendChild(node);

          bindGlobalInline(node);
          bindCustomEditable(node);
          bindCustomDelete(node);

          saveCustomOrder();
          if (menu) menu.style.display = 'none';
        });
      }

      // ===== 5) Sortable (simpan urutan CUSTOM saja)
      Sortable.create(content, {
        animation: 150,
        handle: '.section',
        draggable: '.section',
        onEnd: saveCustomOrder
      });

      function saveCustomOrder() {
        const ids = Array.from(content.querySelectorAll('.section.custom-text[data-custom-id]'))
                         .map(el => el.dataset.customId);
        fetch("{{ route('pelamar.curriculum_vitae.custom.reorder', $cv->id) }}", {
          method: "POST",
          headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
          body: JSON.stringify({ ids })
        });
      }

      // ===== 6) Hover indicators
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
      bindCustomDelete();
    });
    </script>
  </div>
</div>
@endsection
