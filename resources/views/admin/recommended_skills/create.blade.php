@extends('admin.master_admin')
@section('title', 'Tambah Rekomendasi Keahlian | CVRE GENERATE')

@section('content')
<div class="container-fluid">
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="mt-4 mb-4 text-center">Tambah Rekomendasi Keahlian</h4>

        <form method="POST" action="{{ route('admin.recommended_skills.store') }}" enctype="multipart/form-data">
          @csrf

          <div class="row g-3">
            {{-- Pilih Pekerjaan --}}
            <div class="col-12">
              <div class="form-group">
                <label for="job_id" class="form-label">Pekerjaan</label>
                <select class="form-control" id="job_id" name="job_id" required>
                  <option value="">Pilih</option>
                  @foreach($jobs as $job)
                    <option value="{{ $job->id }}">{{ $job->job_name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            {{-- Dynamic Skills --}}
            <div class="col-12">
              <label class="form-label">Rekomendasi Keahlian</label>

              <div id="skills-wrapper">
                {{-- Item awal --}}
                <div class="skill-item mb-3">
                  <select class="form-control" name="skill_id[]" required>
                    <option value="">Pilih</option>
                    @foreach($skills as $skill)
                      <option value="{{ $skill->id }}">{{ $skill->skill_name }}</option>
                    @endforeach
                  </select>
                  <div class="d-flex justify-content-center gap-8 mt-2">
                    <button type="button" class="btn btn-success" data-role="add" aria-label="Tambah keahlian">+</button>
                    <button type="button" class="btn btn-danger" data-role="remove" aria-label="Hapus keahlian">-</button>
                  </div>
                </div>
              </div>

              {{-- Template item untuk cloning --}}
              <template id="tpl-skill-item">
                <div class="skill-item mb-3">
                  <select class="form-control" name="skill_id[]" required>
                    <option value="">Pilih</option>
                    @foreach($skills as $skill)
                      <option value="{{ $skill->id }}">{{ $skill->skill_name }}</option>
                    @endforeach
                  </select>
                  <div class="d-flex justify-content-center gap-2 mt-2">
                    <button type="button" class="btn btn-success mr-2" data-role="add" aria-label="Tambah keahlian">+</button>
                    <button type="button" class="btn btn-danger" data-role="remove" aria-label="Hapus keahlian">-</button>
                  </div>
                </div>
              </template>
            </div>
          </div>

          <button class="mt-4 btn btn-primary btn-lg" type="submit">Tambah Rekomendasi Keahlian</button>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- JS inline agar pasti jalan --}}
<script>
(function(){
  const wrapper = document.getElementById('skills-wrapper');
  const tpl = document.getElementById('tpl-skill-item');

  function appendItem() {
    const node = tpl.content.firstElementChild.cloneNode(true);
    // Pastikan dropdown kosong saat ditambah
    const sel = node.querySelector('select');
    if (sel) sel.selectedIndex = 0;
    wrapper.appendChild(node);
    syncButtons();
  }

  function syncButtons() {
    const items = wrapper.querySelectorAll('.skill-item');
    const total = items.length;

    items.forEach((item, idx) => {
      const addBtn = item.querySelector('[data-role="add"]');
      const rmBtn  = item.querySelector('[data-role="remove"]');

      // tombol + hanya di item terakhir
      if (idx === total - 1) {
        addBtn.style.display = 'inline-block';
      } else {
        addBtn.style.display = 'none';
      }

      // minimal 1 item: sembunyikan - jika hanya 1
      if (total > 1) {
        rmBtn.style.display = 'inline-block';
      } else {
        rmBtn.style.display = 'none';
      }
    });
  }

  // Delegasi klik
  wrapper.addEventListener('click', function(e){
    const btn = e.target.closest('button[data-role]');
    if (!btn) return;
    e.preventDefault();

    if (btn.dataset.role === 'add') {
      appendItem();
    } else if (btn.dataset.role === 'remove') {
      const item = btn.closest('.skill-item');
      if (!item) return;
      // jaga-jaga: jangan hapus jika tinggal 1
      if (wrapper.querySelectorAll('.skill-item').length > 1) {
        item.remove();
        syncButtons();
      }
    }
  });

  // Inisialisasi state tombol saat pertama load
  syncButtons();
})();
</script>
@endsection
