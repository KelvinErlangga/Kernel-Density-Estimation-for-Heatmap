@extends('pelamar.dashboard.master_user')
@section('title', 'Peta Lowongan | CVRE GENERATE')

@push('style')
<style>
    :root{
        --card-radius:16px;
        --soft-shadow:0 10px 24px rgba(19, 26, 53, .08);
        --soft-border:#e9edf4;
        --muted:#6b7280;
        --chip-bg:#f3f6ff;
    }
    #map{height:500px;width:100%}
    .job-card:hover{background:#f9f9f9}
    .scroll-target{scroll-margin-top:30px}

    /* ===== Toolbar (search + mode) ===== */
    .toolbar{
        background:#fff;
        border:1px solid var(--soft-border);
        border-radius:var(--card-radius);
        padding:.75rem;
        box-shadow:var(--soft-shadow);
    }
    .search-group .form-control{
        background:#f7f8fb;
        border:1px solid var(--soft-border);
        height:46px;
        border-radius:12px;
    }
    .search-group .btn{
        height:46px;
        border-radius:12px;
        display:inline-flex;
        align-items:center;
        gap:.35rem;
        padding:0 14px;
    }
    .mode-wrap{
        background:#f9fafc;
        border:1px solid var(--soft-border);
        border-radius:12px;
        padding:.35rem .6rem;
        display:flex; align-items:center; gap:.6rem;
        white-space:nowrap;
        margin-top: 10px;
    }
    .mode-wrap label{margin:0;color:var(--muted);font-size:.9rem}
    .mode-wrap .form-select{
        border:0;background:transparent;box-shadow:none;
        padding-left:.25rem;padding-right:1.5rem;
        height:36px;border-radius:10px;font-weight:600;
    }
    /* chip domisili */
    .chip{
        background:var(--chip-bg);
        border:1px solid #dee6ff;
        color:#2b4acb;
        font-weight:600;
        padding:.25rem .6rem;
        border-radius:999px;
        display:inline-flex; align-items:center; gap:.45rem;
        white-space:nowrap;
        margin: 10px;
    }
    .chip .dot{
        width:8px;height:8px;border-radius:50%;
        background:#22c55e; /* hijau = koordinat ada */
    }
    .chip.missing .dot{ background:#ef4444 } /* merah jika koordinat belum ada */

    /* ===== Radius control ===== */
    .radius-wrap{
        background:#fff;
        border:1px dashed var(--soft-border);
        border-radius:12px;
        padding:.35rem .6rem;
        display:flex; align-items:center; gap:.6rem;
        margin-top: 10px;
    }
    .radius-wrap .radius-range{ width:160px; }
    .radius-wrap .radius-badge{
        background:#eef2ff;
        border:1px solid #c7d2fe;
        color:#1d4ed8;
        padding:.2rem .5rem;
        border-radius:999px;
        font-weight:700;
        font-size:.9rem;
        min-width:64px;
        text-align:center;
    }
    .radius-wrap[aria-disabled="true"]{
        opacity:.55;
        pointer-events:none;
    }

    /* ===== Card tweaks ===== */
    .card{border:1px solid var(--soft-border);border-radius:var(--card-radius)}
    .card-header{
        background:#fff;border-bottom:1px solid var(--soft-border);
        border-top-left-radius:var(--card-radius);border-top-right-radius:var(--card-radius)
    }
    .card-header h6{letter-spacing:.02em}

    /* ===== Hint Banner ===== */
    .hint-banner{
    background: rgba(250, 204, 21, .18); /* kuning transparan */
    border: 1px solid #facc15;           /* amber-400 */
    color: #78350f;                      /* amber-900 */
    border-radius: 12px;
    padding: .65rem .85rem;
    display: flex; align-items: center; gap: .5rem;
    box-shadow: var(--soft-shadow);
    }
    .hint-banner a{ color:#1d4ed8; font-weight:700; text-decoration: underline; }
    .hint-banner .icon{ width:18px; text-align:center; }

    /* ==== Fade effect untuk hint ==== */
    .hint-banner { position: relative; overflow: hidden; }
    .hint-banner .icon { width:18px; text-align:center; }

    /* kontainer isi yang dianimasikan */
    .hint-inner{
    display:inline-block;
    opacity:1;
    transform: translateY(0);
    transition: opacity .35s ease, transform .35s ease;
    }

    .hint-inner.fade-out{
    opacity:0;
    transform: translateY(6px);
    }

    .hint-inner.fade-in{
    opacity:1;
    transform: translateY(0);
    }

    /* ===== Suggestions dropdown ===== */
    #job-suggestions{
        border:1px solid var(--soft-border);
        border-radius:12px; overflow:hidden;
        background:#fff;
    }
    #job-suggestions .list-group-item{
        border:0; padding:.75rem 1rem; font-weight:500;
    }
    #job-suggestions .list-group-item:hover{
        background:#f5f7ff; color:#1d4ed8;
    }
</style>
@endpush

@section('content')
@php
    $me = Auth::user();
    $domCity = $me->personalPelamar->city_pelamar ?? null;
    $domLat  = $me->personalPelamar->latitude ?? null;
    $domLon  = $me->personalPelamar->longitude ?? null;
@endphp

<script>
window.USER_DOMICILE = {
    city: @json($domCity),
    lat: {{ $domLat ? (float)$domLat : 'null' }},
    lon: {{ $domLon ? (float)$domLon : 'null' }},
    radiusKmDefault: 20
};
</script>

<div class="container-fluid mt-4">

    <!-- ===== Hint Banner (auto diisi oleh JS) ===== -->
    <div id="match-hint" class="hint-banner mb-3 d-none">
        <i class="fas fa-lightbulb icon"></i>
        <span>Menyiapkan rekomendasi terbaik untukmu…</span>
    </div>

    {{-- Toolbar: Search + Mode + Radius --}}
    <div class="toolbar mb-3">
        <div class="row g-3 align-items-center">
            <div class="col-lg-7">
                <div id="job-search-wrap" class="position-relative search-group">
                    <div class="input-group">
                        <input id="job-search" type="search" class="form-control"
                               placeholder="Cari: jabatan, kota, industri…" autocomplete="off" />
                        <button class="btn btn-primary ml-2" id="job-search-btn">
                            <i class="fas fa-search"></i><span class="d-none d-md-inline"> Cari</span>
                        </button>
                        <button class="btn btn-outline-secondary ml-2" id="job-search-reset">
                            <i class="fas fa-undo"></i><span class="d-none d-md-inline"> Reset</span>
                        </button>
                    </div>
                    <ul id="job-suggestions" class="list-group position-absolute w-100 shadow-sm"
                        style="z-index:1050; display:none; max-height:220px; overflow-y:auto; top:110%;">
                    </ul>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap">
                    <div class="mode-wrap mr-2">
                        <label for="mode-select" class="me-1"><i class="fas fa-sliders-h m-2"></i>Mode</label>
                        <select id="mode-select" class="form-select">
                            <option value="default" selected>Default</option>
                            <option value="nearby">Dekat Domisili</option>
                        </select>
                    </div>

                    {{-- Radius (aktif hanya saat Nearby) --}}
                    <div id="radius-wrap" class="radius-wrap" aria-disabled="true" title="Atur radius saat mode Dekat Domisili">
                        <i class="fas fa-ruler-combined"></i>
                        <label for="radius-km" class="m-0 text-muted">Radius</label>
                        <input id="radius-km" type="range" min="5" max="100" step="5"
                               class="radius-range" value="20">
                        <span class="radius-badge"><span id="radius-out">20</span> km</span>
                    </div>

                    <span class="chip {{ $domLat && $domLon ? '' : 'missing' }}">
                        <span class="dot"></span>
                        <i class="fas fa-location-dot"></i>
                        {{ $domCity ?? 'Domisili belum diatur' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Left: Map --}}
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header text-center">
                    <h6 class="m-0 font-weight-bold text-dark">Peta Lowongan</h6>
                </div>
                <div class="card-body">
                    <div id="map"></div>
                </div>
            </div>
        </div>

        {{-- Right: Rekomendasi --}}
        <div class="col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 id="rekomendasi-title" class="m-0 font-weight-bold text-dark scroll-target">REKOMENDASI UNTUKMU</h6>
                    <span class="badge bg-primary text-white" id="rekomendasi-count">0</span>
                </div>
                <div class="card-body" style="max-height:540px; overflow-y:auto;" id="rekomendasi-container">
                    <div class="text-center text-muted">Memuat rekomendasi...</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom: Detail Lowongan --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header text-center">
                    <h6 id="detail-title" class="m-0 font-weight-bold text-dark scroll-target">
                        INFORMASI PERUSAHAAN & LOWONGAN
                    </h6>
                </div>
                <div class="card-body" id="job-detail">
                    <div class="text-center text-muted">Silakan pilih marker di peta atau lowongan pada rekomendasi untuk melihat detail.</div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Modal Lamaran tetap sama --}}
@include('pelamar.dashboard.partials.modal_lamaran')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ mix('js/app.js') }}"></script>
<script>
    function toggleCVOption() {
        const upload = document.getElementById('upload_cv_group');
        const dash = document.getElementById('dashboard_cv_group');
        const isUpload = document.getElementById('cv_option_upload').checked;
        upload.classList.toggle('d-none', !isUpload);
        dash.classList.toggle('d-none', isUpload);
    }

    function openApplicationModal(jobId) {
        document.getElementById('hiring_id').value = jobId;
        $('#applicationModal').modal('show');
    }

    window.scrollToDetailHeader = function () {
        const header = document.getElementById('detail-title');
        if (header) header.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    function showJobDetail(jobId) {
        fetch(`/dashboard-user/lowongan/${jobId}`)
            .then(res => res.json())
            .then(job => {
                const jobDetailContainer = document.getElementById("job-detail");
                let btn = '';
                if (job.is_closed) {
                    btn = `<p class="text-danger mt-3">Lowongan Ditutup</p>`;
                } else if (job.has_applied) {
                    btn = `<p class="text-success mt-3">Sudah Melamar</p>`;
                } else {
                    btn = `<button class="btn btn-primary mt-3" onclick="openApplicationModal('${job.id}')">Kirim Lamaran</button>`;
                }

                function makeBulletList(text) {
                    if (!text) return "<p>-</p>";
                    const parts = text.split(";").map(p => p.trim()).filter(Boolean);
                    return parts.length === 0 ? "<p>-</p>" :
                        `<ul class="pl-3 mb-0">${parts.map(p => `<li>${p}</li>`).join("")}</ul>`;
                }
                function makeCommaList(text) {
                    if (!text) return "<p>-</p>";
                    const parts = String(text).split(",").map(p => p.trim()).filter(Boolean);
                    return parts.length ? `<ul class="pl-3 mb-0">${parts.map(p => `<li>${p}</li>`).join("")}</ul>` : "<p>-</p>";
                }

                jobDetailContainer.innerHTML = `
                    <div class="d-flex align-items-center mb-4">
                        <img src="${job.personal_company_logo ?? "/images/default-company.png"}"
                             style="width:70px;height:70px;object-fit:contain;border-radius:8px;border:1px solid #ccc;background:#f5f5f5;" class="mr-3">
                        <div>
                            <h5 class="font-weight-bold mb-1">${job.position_hiring ?? "-"}</h5>
                            <small class="text-muted">${job.company_name ?? "-"}</small>
                        </div>
                    </div>

                    <ul class="list-unstyled mb-4">
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-secondary" style="width:18px;text-align:center;"></i>
                            <span>${job.kota ?? ""}${job.provinsi ? ", " + job.provinsi : ""}</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-building mr-2 text-secondary" style="width:18px;text-align:center;"></i>
                            <span>${job.type_of_company ?? "-"}</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-money-bill-wave mr-2 text-secondary" style="width:18px;text-align:center;"></i>
                            <span>Rp ${new Intl.NumberFormat("id-ID").format(job.gaji_min ?? 0)} -
                                  Rp ${new Intl.NumberFormat("id-ID").format(job.gaji_max ?? 0)} / Bulan</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="fas fa-clock mr-2 text-secondary" style="width:18px;text-align:center;"></i>
                            <span>Batas Waktu: ${job.deadline_hiring
                                ? new Date(job.deadline_hiring).toLocaleDateString("id-ID", { day:"2-digit", month:"long", year:"numeric" })
                                : "-"}</span>
                        </li>
                    </ul>

                    <div class="mb-3">
                        <h6 class="font-weight-bold">Deskripsi Pekerjaan</h6>
                        ${makeBulletList(job.description_hiring)}
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Kualifikasi</h6>
                        ${makeBulletList(job.kualifikasi)}
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Keterampilan Teknis</h6>
                        ${makeCommaList(job.keterampilan_teknis)}
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Keterampilan Non-Teknis</h6>
                        ${makeCommaList(job.keterampilan_non_teknis)}
                    </div>
                    ${btn}
                `;
                window.scrollToDetailHeader();
            });
    }

    // ===== Radius UI wiring (frontend) =====
    document.addEventListener("DOMContentLoaded", () => {
        const modeSelect  = document.getElementById("mode-select");
        const radiusWrap  = document.getElementById("radius-wrap");
        const radiusRange = document.getElementById("radius-km");
        const radiusOut   = document.getElementById("radius-out");

        // init dari USER_DOMICILE
        const initial = Number(window.USER_DOMICILE?.radiusKmDefault ?? 20);
        radiusRange.value = initial;
        radiusOut.textContent = initial;

        function setRadiusEnabled(enabled){
            radiusWrap.setAttribute("aria-disabled", enabled ? "false" : "true");
        }

        // toggle enable/disable sesuai mode terpilih
        function syncModeUI(){
            const mode = modeSelect.value;
            setRadiusEnabled(mode === "nearby");
            if (mode === "nearby") {
                // push radius saat ini ke peta (app.js akan dengar event ini)
                const km = Number(radiusRange.value);
                document.dispatchEvent(new CustomEvent("HEATMAP:radius-change", { detail: { radiusKm: km } }));
            } else {
                // mode default – minta app.js refresh tanpa radius (mode default diset di app.js)
                document.dispatchEvent(new CustomEvent("HEATMAP:radius-change", { detail: { radiusKm: null } }));
            }
        }

        // perubahan nilai slider -> refresh nearby
        radiusRange.addEventListener("input", () => {
            const km = Number(radiusRange.value);
            radiusOut.textContent = km;
        });
        radiusRange.addEventListener("change", () => {
            const km = Number(radiusRange.value);
            radiusOut.textContent = km;
            // simpan default radius di window agar fetch berikutnya konsisten
            window.USER_DOMICILE.radiusKmDefault = km;
            // kabari app.js untuk redraw circle + fetch data berdasarkan radius baru
            document.dispatchEvent(new CustomEvent("HEATMAP:radius-change", { detail: { radiusKm: km } }));
        });

        // perubahan mode
        modeSelect.addEventListener("change", syncModeUI);

        // set awal
        syncModeUI();
    });
</script>
@endpush
