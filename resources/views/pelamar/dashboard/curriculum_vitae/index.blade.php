@extends('pelamar.dashboard.master_user')
@section('title', 'Curriculum Vitae | CVRE GENERATE')

@push('style')
<link href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" rel="stylesheet" />
<style>
  :root{
    --card-radius:16px;
    --soft-shadow:0 10px 24px rgba(19,26,53,.08);
    --soft-border:#e9edf4;
  }

  .card{
    border:1px solid var(--soft-border);
    border-radius:var(--card-radius);
    box-shadow:var(--soft-shadow);
  }

  .cv-grid{
    gap:24px;
    row-gap:32px;
  }

  @supports not (gap: 1rem){
    .cv-grid > .cv-card,
    .cv-grid > a .cv-add,
    .cv-grid > .cv-add{
      margin-right:24px;
      margin-bottom:32px;
    }
  }

  .cv-card{
    width:220px;
    border:1px solid var(--soft-border);
    border-radius:var(--card-radius);
    overflow:hidden;
    background:#fff;
    transition:.2s;
    display:flex;
    flex-direction:column;
  }
  .cv-card img{
    width:100%;
    height:311px;
    object-fit:contain;
    background:#fff;
    display:block;
    border-bottom:1px solid var(--soft-border);
  }
  .cv-card:hover{ box-shadow:var(--soft-shadow); transform:translateY(-10px); }

  .cv-meta{
    min-height:48px;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:.5rem .75rem;
    background:#f9fafc;
    font-size:.85rem;
    font-weight:600;
    text-align:center;
    overflow:hidden;
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
  }

  .cv-actions{
    display:flex;
    justify-content:center;
    gap:.5rem;
    padding:.6rem;
    background:#fff;
    min-height:50px;
  }
  .cv-actions .btn{
    border-radius:8px;
    font-size:.85rem;
    padding:.3rem .8rem;
  }

  .cv-add{
    cursor:pointer;
    height:409px;
    width:220px;
    border:2px dashed #3b82f6;
    border-radius:var(--card-radius);
    background:#f9fafc;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    transition:.3s;
  }
  .cv-add:hover{ background:#eff6ff; box-shadow:var(--soft-shadow); }
  .cv-add i{ font-size:2.5rem; margin-bottom:.5rem; color:#1f2937; }
  .cv-add h6{ font-weight:600; color:#1f2937; }

  .cv-card.celebrate {
    position: relative;
    animation: glowPulse 900ms ease-in-out infinite;
  }
  @keyframes glowPulse {
    0%, 100% {
      box-shadow: 0 0 0 0 rgba(53,56,205,.00);
      transform: translateY(-2px);
    }
    50% {
      box-shadow: 0 0 0 4px #a5b4fc, 0 0 18px rgba(53,56,205,.38);
      transform: translateY(-5px);
    }
  }

  .cv-congrats {
    text-align:center;
    margin-top:8px;
    color:#111827;
    font-weight:600;
    transition: opacity .6s ease;
  }

  /* Responsive kecil */
  @media (max-width: 575.98px){
    .cv-card, .cv-add{ width:180px; }
    .cv-card img{ height:254px; }
    .cv-grid{ gap:16px; row-gap:24px; }
  }
</style>
@endpush

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex align-items-start flex-wrap cv-grid">

        <a href="{{ route('pelamar.curriculum_vitae.index') }}" class="text-decoration-none">
          <div class="cv-add">
            <i class="fas fa-plus"></i>
            <h6>Buat CV Baru</h6>
          </div>
        </a>

        {{-- CV Preview --}}
        @foreach($curriculumVitaes as $cv)
          <div class="cv-card" data-cv-id="{{ $cv->id }}">
            <img src="{{ Storage::url($cv->templateCV->thumbnail_curriculum_vitae) }}" alt="CV Preview">
            <div class="cv-meta">
              {{ $cv->templateCV->template_curriculum_vitae_name }}
            </div>
            <div class="cv-actions">
              @php
                $routeName = 'pelamar.curriculum_vitae.preview.index';
                $previewUrl = \Illuminate\Support\Facades\Route::has($routeName)
                  ? route($routeName, $cv)
                  : url("/curriculum-vitae/{$cv->id}/preview");
              @endphp

              @if($cv->templateCV)
                <a href="{{ $previewUrl }}" class="btn btn-outline-primary btn-sm">Edit</a>
              @else
                <button class="btn btn-outline-secondary btn-sm" disabled title="Template CV belum tersedia">Edit</button>
              @endif

              <form action="{{ route('pelamar.dashboard.curriculum_vitae.delete', $cv->id) }}"
                    method="POST" class="delete-form">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-danger btn-sm btn-delete">Hapus</button>
              </form>
            </div>
          </div>
        @endforeach

      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  (function celebrateIfNeeded() {
    const savedId = localStorage.getItem('cv_just_saved_id');
    const savedAt = parseInt(localStorage.getItem('cv_just_saved_at') || '0', 10);
    const isFresh = !savedAt || (Date.now() - savedAt < 10 * 60 * 1000);

    if (!savedId || !isFresh) return;

    const selectorSafeId = (window.CSS && CSS.escape) ? CSS.escape(savedId) : savedId.replace(/"/g, '\\"');
    const card = document.querySelector(`.cv-card[data-cv-id="${selectorSafeId}"]`);
    if (!card) return;

    // 1) Highlight halus (stop setelah 7 detik)
    card.classList.add('celebrate');
    setTimeout(() => {
      card.classList.remove('celebrate');
    }, 4500);

    // 2) Pesan di bawah kartu (auto-hide 7 detik)
    const msg = document.createElement('div');
    msg.className = 'cv-congrats';
    msg.textContent = 'Selamat, CV baru kamu berhasil dibuat!';
    const actions = card.querySelector('.cv-actions');
    if (actions) actions.insertAdjacentElement('afterend', msg);
    else card.insertAdjacentElement('afterend', msg);

    setTimeout(() => {
      msg.style.opacity = '0';
      setTimeout(() => msg.remove(), 650);
    }, 8000);

    // 3) Konfeti “gugur dari atas” (tengah layar), singkat ~1.2s
    const duration = 1200;
    const end = Date.now() + duration;

    (function fall() {
      confetti({
        particleCount: 12,
        startVelocity: 15,  // pelan
        spread: 50,
        gravity: 1.2,       // jatuh
        drift: (Math.random() - 0.5) * 0.6, // goyang halus
        ticks: 200,
        scalar: 0.9,
        origin: { x: Math.random() * 0.8 + 0.1, y: 0 } // sekitar tengah atas
      });
      if (Date.now() < end) requestAnimationFrame(fall);
    })();

    // (opsional) toast kecil
    if (window.Swal) {
      Swal.fire({
        toast: true,
        icon: 'success',
        title: 'CV tersimpan di Dashboard',
        timer: 1400,
        position: 'top-end',
        showConfirmButton: false
      });
    }

    // bersihkan flag agar tidak mengulang
    localStorage.removeItem('cv_just_saved_id');
    localStorage.removeItem('cv_just_saved_at');

    // scroll ke card agar terlihat
    try { card.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch(e){}
  })();

  // ====== Konfirmasi + Hapus via AJAX (tanpa reload) ======
  document.querySelectorAll(".btn-delete").forEach(btn => {
    btn.addEventListener("click", async (e) => {
      e.preventDefault();
      const form = btn.closest("form");
      const card = btn.closest(".cv-card");
      const token = form.querySelector('input[name="_token"]')?.value || '';

      const confirm = await Swal.fire({
        title: 'Yakin hapus CV ini?',
        text: "Data tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
      });
      if (!confirm.isConfirmed) return;

      btn.disabled = true;
      try {
        const fd = new FormData(form);
        const res = await fetch(form.action, {
          method: 'POST', // spoofing DELETE Laravel
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          },
          body: fd
        });

        let msg = 'CV berhasil dihapus.';
        if (res.headers.get('content-type')?.includes('application/json')) {
          const data = await res.json().catch(() => ({}));
          if (data?.message) msg = data.message;
        }

        if (card) card.remove();

        await Swal.fire({ icon: 'success', title: 'Terhapus', text: msg, timer: 1800, showConfirmButton: false });
      } catch (err) {
        console.error(err);
        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat menghapus CV. Coba lagi atau muat ulang halaman.' });
      } finally {
        btn.disabled = false;
      }
    });
  });

  // Flash message dari session (jika ada)
  @if(session('success'))
    Swal.fire({ icon: 'success', title: 'Berhasil', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false });
  @endif
  @if(session('error'))
    Swal.fire({ icon: 'error', title: 'Gagal', text: "{{ session('error') }}" });
  @endif
});
</script>
@endpush
