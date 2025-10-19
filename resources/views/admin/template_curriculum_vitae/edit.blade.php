@extends('admin.master_admin')
@section('title', 'Ubah Template CV | CVRE GENERATE')

@section('content')
<div class="container-fluid">

  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">

        <h5 class="mb-4 text-center text-primary font-weight-bold">
          Form Ubah Template CV
        </h5>

        <form method="POST"
              action="{{ route('admin.template_curriculum_vitae.update', $templateCurriculumVitae->id) }}"
              enctype="multipart/form-data"
              id="templateForm">
          @csrf
          @method('PUT')

          <div class="row">
            <!-- Panel Kiri -->
            <div class="col-md-4">
              <h6 class="mb-3">Jenis Template CV</h6>
              <div class="form-group">
                <select class="form-control" name="template_type" id="template_type" required>
                  <option value="">-- Pilih Jenis Template --</option>
                  <option value="ats"     {{ strtolower($templateCurriculumVitae->template_type) == 'ats' ? 'selected' : '' }}>CV ATS</option>
                  <option value="kreatif" {{ strtolower($templateCurriculumVitae->template_type) == 'kreatif' ? 'selected' : '' }}>CV Kreatif</option>
                </select>
              </div>

              <h6 class="mt-4 mb-2">Komponen CV</h6>
              <small class="text-muted d-block mb-2">(Drag ke kanan untuk menyusun)</small>

              <!-- Komponen ATS -->
              <ul id="available-fields-ats" class="list-group template-fields d-none">
                <li class="list-group-item" data-key="personal_detail-ats">Detail Pribadi</li>
                <li class="list-group-item" data-key="experiences-ats">Pengalaman Kerja</li>
                <li class="list-group-item" data-key="educations-ats">Pendidikan</li>
                <li class="list-group-item" data-key="languages-ats">Bahasa</li>
                <li class="list-group-item" data-key="skills-ats">Keahlian</li>
                <li class="list-group-item" data-key="organizations-ats">Organisasi</li>
                <li class="list-group-item" data-key="achievements-ats">Prestasi</li>
                <li class="list-group-item" data-key="links-ats">Link Informasi</li>
                <li class="list-group-item" data-key="custom-ats">Section Bebas</li>
              </ul>

              <!-- Komponen Kreatif -->
              <ul id="available-fields-kreatif" class="list-group template-fields d-none">
                <li class="list-group-item" data-key="personal_detail-kreatif">Detail Pribadi</li>
                <li class="list-group-item" data-key="experiences-kreatif">Pengalaman Kerja</li>
                <li class="list-group-item" data-key="educations-kreatif">Pendidikan</li>
                <li class="list-group-item" data-key="languages-kreatif">Bahasa</li>
                <li class="list-group-item" data-key="skills-kreatif">Keahlian</li>
                <li class="list-group-item" data-key="organizations-kreatif">Organisasi</li>
                <li class="list-group-item" data-key="achievements-kreatif">Prestasi</li>
                <li class="list-group-item" data-key="links-kreatif">Link Informasi</li>
              </ul>

              <p class="small text-muted mt-3">
                <strong>Catatan:</strong> Komponen hanya bisa ditambahkan sekali. Hapus dari canvas untuk mengaktifkan kembali.
              </p>
            </div>

            <!-- Panel Kanan (Canvas) -->
            <div class="col-md-8">
              <h6 class="mb-3">Preview Template CV</h6>
              <div id="cv-canvas" class="bg-white p-4" style="min-height:600px; max-width:820px; margin:auto;">
                <div id="cv-canvas-inner">
                  <p class="empty-placeholder text-muted">
                    (Seret komponen dari kiri ke area ini)
                  </p>
                </div>
              </div>

              <!-- hidden inputs -->
              <input type="hidden" name="layout_json" id="layout_json"
                     value='@json(json_decode($templateCurriculumVitae->layout_json, true) ?? [])'>
              <input type="hidden" name="style_json" id="style_json"
                     value='@json(json_decode($templateCurriculumVitae->style_json, true) ?? [])'>
            </div>
          </div>

          <div class="row mt-4">
            <!-- Nama Template -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="template_curriculum_vitae_name">Nama Template</label>
                <input type="text" class="form-control" name="template_curriculum_vitae_name"
                       value="{{ old('template_curriculum_vitae_name', $templateCurriculumVitae->template_curriculum_vitae_name) }}" required>
              </div>
            </div>

            <!-- Thumbnail -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="thumbnail_curriculum_vitae">Thumbnail Template CV</label>
                <input type="file" class="form-control" name="thumbnail_curriculum_vitae">
                @if($templateCurriculumVitae->thumbnail_curriculum_vitae)
                  <div class="mt-2">
                    <img src="{{ Storage::url($templateCurriculumVitae->thumbnail_curriculum_vitae) }}" width="120" class="img-thumbnail">
                  </div>
                @endif
              </div>
            </div>
          </div>

          <div class="text-right">
            <button type="submit" class="btn btn-primary btn-lg mt-3">
              <i class="fas fa-save mr-1"></i> Update Template
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

{{-- semua komponen template --}}
@include('admin.template_curriculum_vitae.templates')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  // ====== State & element refs ======
  const typeSelect = document.getElementById('template_type');
  const canvas     = document.getElementById('cv-canvas-inner');
  const layoutInp  = document.getElementById('layout_json');
  const styleInp   = document.getElementById('style_json');
  const form       = document.getElementById('templateForm');

  const type = (typeSelect.value || '{{ strtolower($templateCurriculumVitae->template_type) }}').toLowerCase();


  let layoutData = [];
  try { layoutData = JSON.parse(layoutInp.value || '[]'); } catch(e){ layoutData = []; }

  // ====== Helpers ======
  function withSuffix(key, t) {
    if (!key) return '';
    return key.includes('-') ? key : `${key}-${t}`;
  }

  function showAvailableFields(t) {
    document.querySelectorAll('.template-fields').forEach(el => el.classList.add('d-none'));
    const list = document.getElementById('available-fields-' + t);
    if (list) list.classList.remove('d-none');

    // aktifkan Sortable di list yang kelihatan
    if (list && !list._sortable) {
      list._sortable = Sortable.create(list, {
        group: { name: 'cv-sections', pull: 'clone', put: false },
        sort: false,
        animation: 150
      });
    }
  }

  // disable/enable item kiri berdasarkan isi canvas
  function refreshLeftList(t) {
    // enable semua
    document.querySelectorAll('.template-fields .list-group-item').forEach(li => {
      li.classList.remove('disabled');
      li.style.pointerEvents = 'auto';
      li.style.color = '';
      li.style.backgroundColor = '';
    });

    // kumpulkan base key yang sedang dipakai di canvas
    const used = new Set();
    canvas.querySelectorAll('.cv-section').forEach(sec => {
      const baseKey = sec.dataset.key; // ex: experiences
      used.add(withSuffix(baseKey, t));
    });

    // disable yang dipakai
    used.forEach(fullKey => {
      const li = document.querySelector(`.template-fields [data-key="${fullKey}"]`);
      if (li) {
        li.classList.add('disabled');
        li.style.pointerEvents = 'none';
        li.style.color = '#b0b0b0';
        li.style.backgroundColor = '#f8f9fa';
      }
    });
  }

  function renderFromLayout(t) {
    if (!layoutData || layoutData.length === 0) {
      canvas.innerHTML = '<p class="empty-placeholder text-muted">(Seret komponen dari kiri ke area ini)</p>';
      refreshLeftList(t);
      return;
    }
    canvas.innerHTML = '';
    layoutData.forEach(it => {
      const fullKey = withSuffix(it.key, t);
      const tpl = document.getElementById('tpl-' + fullKey);
      if (!tpl) { console.warn('Template not found:', fullKey); return; }
      canvas.appendChild(tpl.content.cloneNode(true));
    });
    refreshLeftList(t);
  }

  function updateLayoutJSON() {
    const layout = [];
    canvas.querySelectorAll('.cv-section').forEach(sec => {
      layout.push({ key: sec.dataset.key }); // simpan base key saja
    });
    layoutInp.value = JSON.stringify(layout);

    // contoh style dasar (ambil dari input lama bila ada)
    let styles = {};
    try { styles = JSON.parse(styleInp.value || '{}'); } catch(e){ styles = {}; }
    // ...opsional: update styles di sini bila perlu...
    styleInp.value = JSON.stringify(styles);
  }

  function ensurePlaceholder() {
    if (!canvas.querySelector('.cv-section')) {
      canvas.innerHTML = '<p class="empty-placeholder text-muted">(Seret komponen dari kiri ke area ini)</p>';
    }
  }

  // ====== Init ======
  typeSelect.value = type;
  showAvailableFields(type);
  renderFromLayout(type);

  // ====== Sortable Canvas (drop target) ======
  Sortable.create(canvas, {
    group: { name: 'cv-sections', pull: false, put: true },
    animation: 150,
    onAdd: function(evt) {
      // yang ditambahkan ke canvas adalah clone dari <li>
      const li  = evt.item;            // <li> yang ter-clone ke canvas
      const key = li.dataset.key;      // contoh: "experiences-ats"
      li.remove();                     // buang <li> clone dari canvas

      const tpl = document.getElementById('tpl-' + key);
      if (!tpl) return;

      // cegah duplikasi berdasarkan base key di canvas
      const baseKey = key.split('-')[0];
      if (canvas.querySelector(`.cv-section[data-key="${baseKey}"]`)) {
        refreshLeftList(typeSelect.value.toLowerCase());
        updateLayoutJSON();
        return;
      }

      // hapus placeholder
      const ph = canvas.querySelector('.empty-placeholder');
      if (ph) ph.remove();

      // tempel ke canvas
      canvas.appendChild(tpl.content.cloneNode(true));

      // disable item kiri yang sesuai
      const leftLi = document.querySelector(`.template-fields [data-key="${key}"]`);
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

  // ====== Remove section (tombol X) ======
  document.body.addEventListener('click', function(e) {
    if (e.target && e.target.matches('.remove-section')) {
      const sec = e.target.closest('.cv-section');
      if (!sec) return;
      const baseKey = sec.dataset.key; // ex: "experiences"
      sec.remove();

      const t = typeSelect.value.toLowerCase();
      const fullKey = withSuffix(baseKey, t);

      // re-enable item kiri
      const li = document.querySelector(`.template-fields [data-key="${fullKey}"]`);
      if (li) {
        li.classList.remove('disabled');
        li.style.pointerEvents = 'auto';
        li.style.color = '';
        li.style.backgroundColor = '';
      }

      ensurePlaceholder();
      refreshLeftList(t);
      updateLayoutJSON();
    }
  });

  // ====== Ubah tipe template (ATS/Kreatif) ======
  typeSelect.addEventListener('change', function () {
    const t = this.value.toLowerCase();
    showAvailableFields(t);
    // render ulang dari layoutData (base key sama; tampilan mengikuti tipe)
    renderFromLayout(t);
    updateLayoutJSON();
  });

  // ====== Submit: pastikan hidden ter-update ======
  form.addEventListener('submit', function() { updateLayoutJSON(); });
});
</script>
@endpush
