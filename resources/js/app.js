// NOTE: Pastikan di Blade TIDAK memuat leaflet.js via CDN kalau kamu import dari sini.
// Kalau Blade masih pakai CDN leaflet.js, hapus tag script CDN-nya agar tidak double instance.

import L from "leaflet";
import "leaflet.heat";
import { density2d } from "fast-kde";
import "leaflet/dist/leaflet.css";

// Fix icon default path (karena bundler/Vite ga otomatis bawa PNG)
import iconUrl from "leaflet/dist/images/marker-icon.png";
import iconRetinaUrl from "leaflet/dist/images/marker-icon-2x.png";
import shadowUrl from "leaflet/dist/images/marker-shadow.png";

L.Icon.Default.mergeOptions({
    iconRetinaUrl,
    iconUrl,
    shadowUrl,
});

// ========================= STATE GLOBAL =========================
let jobs = [];
let locations = [];
let mPoints = [];
let map, heatLayer, markerLayer, nearCircle, nearLayer;
let currentMode = "default"; // 'default' | 'nearby'
const USER_HOME = window.USER_DOMICILE || {
    lat: null,
    lon: null,
    city: null,
    radiusKmDefault: 60,
};

// ========================= Helper angka & koordinat =========================
const toNum = (v) => {
    if (v === null || v === undefined) return NaN;
    const s = String(v).trim().replace(",", "."); // dukung " -7,123 "
    const n = parseFloat(s);
    return Number.isFinite(n) ? n : NaN;
};
const validLat = (x) => Number.isFinite(x) && x >= -90 && x <= 90;
const validLon = (x) => Number.isFinite(x) && x >= -180 && x <= 180;

// Web Mercator meter <-> lon/lat
const R = 20037508.34;
const lngLatToMeters = (lon, lat) => {
    const x = (lon * R) / 180;
    const y =
        Math.log(Math.tan(((90 + lat) * Math.PI) / 360)) / (Math.PI / 180);
    return [x, (y * R) / 180];
};
const metersToLngLat = (x, y) => {
    const lon = (x / R) * 180;
    let lat = (y / R) * 180;
    lat =
        (180 / Math.PI) *
        (2 * Math.atan(Math.exp((lat * Math.PI) / 180)) - Math.PI / 2);
    return [lon, lat];
};

// ========================= Debounce helper =========================
function debounce(fn, ms = 400) {
    let t;
    return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn(...args), ms);
    };
}

// ========================= Popup builders =========================
function formatRupiah(num) {
    if (num == null || num === "") return "-";
    const n = Number(num);
    if (!Number.isFinite(n)) return String(num);
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(n);
}
function formatRangeRupiah(min, max) {
    if (min == null && max == null) return "-";
    if (min != null && max != null)
        return `${formatRupiah(min)} – ${formatRupiah(max)}`;
    return min != null ? `${formatRupiah(min)}` : `${formatRupiah(max)}`;
}
function formatDateISO(d) {
    if (!d) return "-";
    const dt = new Date(d);
    return isNaN(dt)
        ? String(d)
        : dt.toLocaleDateString("id-ID", {
              year: "numeric",
              month: "short",
              day: "numeric",
          });
}
function buildPopup(job) {
    const posisi = job.position_hiring ?? "Lowongan";
    const jenis = job.jenis_pekerjaan ?? "-";
    const sistem = job.work_system ?? job.pola_kerja ?? "-";
    const lokasi = [job.kota, job.provinsi].filter(Boolean).join(", ");
    const gaji = formatRangeRupiah(job.gaji_min, job.gaji_max);
    const edu = job.education_hiring ?? "-";
    const exp = job.pengalaman_minimal_tahun ?? job.pengalaman ?? null;
    const expText = exp == null || exp === "" ? "-" : `${exp} th`;
    const deadline = formatDateISO(job.deadline_hiring);

    return `
    <div style="min-width:240px">
      <div style="font-weight:700;font-size:14px;margin-bottom:6px">${posisi}</div>
      <div style="font-size:12px;color:#555;margin-bottom:6px">${
          lokasi || "-"
      }</div>
      <div style="font-size:12px;line-height:1.35">
        <div><b>Jenis</b>: ${jenis}</div>
        <div><b>Sistem</b>: ${sistem}</div>
        <div><b>Gaji</b>: ${gaji}</div>
        <div><b>Pendidikan</b>: ${edu}</div>
        <div><b>Pengalaman</b>: ${expText}</div>
        <div><b>Deadline</b>: ${deadline}</div>
      </div>
      <div style="margin-top:8px">
        <a href="#" class="lihat-detail" data-id="${job.id}">Lihat detail</a>
      </div>
    </div>`;
}

// ========================= KDE recompute =========================
function getAdaptiveParams() {
    const b = map.getBounds();
    const [minx, miny] = lngLatToMeters(b.getWest(), b.getSouth());
    const [maxx, maxy] = lngLatToMeters(b.getEast(), b.getNorth());
    const widthM = maxx - minx;
    const heightM = maxy - miny;

    const px = map.getSize();
    const binsX = Math.max(128, Math.min(1024, Math.round(px.x / 4)));
    const binsY = Math.max(128, Math.min(1024, Math.round(px.y / 4)));

    const cellX = widthM / binsX;
    const cellY = heightM / binsY;

    const baseFactor = 1.5;
    const BAND_M = baseFactor * Math.max(cellX, cellY);
    const buf = BAND_M * 3;

    return {
        extent: [
            [minx - buf, maxx + buf],
            [miny - buf, maxy + buf],
        ],
        bins: [binsX, binsY],
        bandwidth: [BAND_M, BAND_M],
    };
}

function recomputeKDE() {
    if (!mPoints || !mPoints.length) {
        heatLayer.setLatLngs([]);
        return;
    }
    const { extent, bins, bandwidth } = getAdaptiveParams();
    const d2 = density2d(mPoints, { bins, extent, bandwidth });

    let zmax = 0;
    const pts = [];
    for (const p of d2) {
        if (p.z > zmax) zmax = p.z;
        pts.push(p);
    }

    const CUTOFF = 0.1;
    const heat = [];
    for (const p of pts) {
        const val = zmax ? p.z / zmax : 0;
        if (val < CUTOFF) continue;
        const [lon, lat] = metersToLngLat(p.x, p.y);
        const latNum = toNum(lat),
            lonNum = toNum(lon);
        if (!validLat(latNum) || !validLon(lonNum)) continue;
        heat.push([latNum, lonNum, val]);
    }
    heatLayer.setLatLngs(heat);
}

// ========================= Marker interaktif =========================
function updateMarkers() {
    markerLayer.clearLayers();
    const MARKER_ZOOM_THRESHOLD = 15;
    if (map.getZoom() < MARKER_ZOOM_THRESHOLD) return;

    const bounds = map.getBounds();
    const visibleJobs = jobs.filter((j) => bounds.contains([j.lat, j.lon]));
    visibleJobs.forEach((job) => {
        L.marker([job.lat, job.lon], { pane: "markers" })
            .bindPopup(buildPopup(job))
            .addTo(markerLayer);
    });
}

// ========================= Render Rekomendasi =========================
function renderRekomendasi(list, highlightQuery = "") {
    const container = document.getElementById("rekomendasi-container");
    const counter = document.getElementById("rekomendasi-count");
    if (!container) return;

    if (counter) counter.textContent = list.length;

    if (!list.length) {
        container.innerHTML = `<p class="text-center text-muted">Tidak ada rekomendasi lowongan</p>`;
        return;
    }

    // Prioritaskan match query
    if (highlightQuery) {
        list.sort((a, b) => {
            const aMatch = (a.position_hiring ?? "")
                .toLowerCase()
                .includes(highlightQuery.toLowerCase());
            const bMatch = (b.position_hiring ?? "")
                .toLowerCase()
                .includes(highlightQuery.toLowerCase());
            return bMatch - aMatch; // true=1
        });
    }

    container.innerHTML = list
        .map((job) => {
            const dist = Number.isFinite(toNum(job.distance_km))
                ? `<small class="d-block text-muted">≈ ${toNum(
                      job.distance_km
                  ).toFixed(1)} km dari Anda</small>`
                : "";

            return `
        <div class="card mb-3 border job-card" style="cursor:pointer;" onclick="showJobDetail('${
            job.id
        }')" tabindex="0">
          <div class="d-flex p-3">
            <img src="${
                job.personal_company?.logo
                    ? "/storage/company_logo/" + job.personal_company.logo
                    : "/images/default-company.png"
            }"
                 alt="Logo ${
                     job.personal_company?.name_company ?? "Perusahaan"
                 }"
                 style="width:70px;height:70px;object-fit:contain;border-radius:6px;border:1px solid #ccc;background:#f5f5f5;" class="mr-3">
            <div>
              <h6 class="font-weight-bold mb-1">${
                  job.position_hiring ?? "-"
              }</h6>
              <small class="d-block text-muted">${
                  job.personal_company?.name_company ?? "-"
              }</small>
              <small class="d-block">${[job.kota, job.provinsi]
                  .filter(Boolean)
                  .join(", ")}</small>
              ${dist}
              <small class="d-block text-dark">Rp ${new Intl.NumberFormat(
                  "id-ID"
              ).format(job.gaji_min ?? 0)} - Rp ${new Intl.NumberFormat(
                "id-ID"
            ).format(job.gaji_max ?? 0)}/Bulan</small>
              <small class="text-muted">Diposting ${new Date(
                  job.created_at
              ).toLocaleDateString("id-ID")}</small>
            </div>
          </div>
        </div>`;
        })
        .join("");
}

// ========================= Fetch data (dengan query & mode) =========================
async function fetchData(query = "", opts = {}) {
    const params = new URLSearchParams();
    params.set("min_score", "2");
    if (query && query.trim() !== "") params.set("q", query.trim());

    const mode = opts.mode || currentMode || "default";
    params.set("mode", mode);

    const hasHome =
        Number.isFinite(USER_HOME?.lat) && Number.isFinite(USER_HOME?.lon);
    if (mode === "nearby" && hasHome) {
        const radiusKm = Number(
            opts.radiusKm ?? USER_HOME.radiusKmDefault ?? 60
        );
        params.set("origin_lat", String(USER_HOME.lat));
        params.set("origin_lon", String(USER_HOME.lon));
        params.set("radius_km", String(radiusKm));
    }

    const res = await fetch(`/pelamar/heatmap/data?${params.toString()}`, {
        credentials: "same-origin",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    });
    if (!res.ok) {
        const preview = await res.text();
        console.error(
            "[heatmap] Bad response:",
            res.status,
            preview.slice(0, 200)
        );
        throw new Error(`HTTP ${res.status}`);
    }

    const rawData = await res.json();
    if (!Array.isArray(rawData)) throw new Error("Data tidak valid");

    // Normalisasi & filter koordinat
    const rawJobs = rawData.map((d) => ({
        ...d,
        lat: toNum(d.latitude),
        lon: toNum(d.longitude),
    }));
    jobs = rawJobs.filter((j) => validLat(j.lat) && validLon(j.lon));
    locations = jobs.map((j) => [j.lat, j.lon]);
    mPoints = locations.map(([lat, lon]) => lngLatToMeters(lon, lat));

    // Urutkan jarak saat nearby (jika backend kirim distance_km)
    if (mode === "nearby") {
        jobs.sort(
            (a, b) =>
                (toNum(a.distance_km) || 1e9) - (toNum(b.distance_km) || 1e9)
        );
    }

    // --- ⬇️ overlay radius: SELALU dibersihkan lalu digambar ulang ---
    if (mode === "nearby" && hasHome) {
        const radiusKm = Number(
            opts.radiusKm ?? USER_HOME.radiusKmDefault ?? 60
        );

        // bersihkan overlay lama
        if (nearLayer) nearLayer.clearLayers();
        nearCircle = L.circle([USER_HOME.lat, USER_HOME.lon], {
            radius: radiusKm * 1000, // km -> meter
            color: "#1d4ed8",
            weight: 1,
            fillOpacity: 0.08,
        });
        nearLayer.addLayer(nearCircle);

        // zoom mengikuti lingkaran (kalau radius dikecilkan -> zoom masuk; dibesarkan -> zoom keluar)
        map.fitBounds(nearCircle.getBounds().pad(0.15));
    } else {
        // mode default: hilangkan overlay radius & optionally fit ke data lowongan
        if (nearLayer) nearLayer.clearLayers();
        nearCircle = null;

        if (jobs.length) {
            const bb = L.latLngBounds(locations);
            map.fitBounds(bb.pad(0.2));
        }
    }

    // render panel & peta
    renderRekomendasi(jobs);
    recomputeKDE();
    updateMarkers();
}

// ========================= DOM Ready =========================
document.addEventListener("DOMContentLoaded", async () => {
    try {
        // Init map & layers
        map = L.map("map").setView([-7.3, 112.7], 12);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors",
        }).addTo(map);

        if (!map.getPane("markers")) {
            const pane = map.createPane("markers");
            pane.style.zIndex = 650;
        }

        heatLayer = L.heatLayer([], {
            radius: 25,
            blur: 15,
            maxZoom: 17,
            minOpacity: 0.06,
        }).addTo(map);
        markerLayer = L.layerGroup().addTo(map);

        // Layer khusus untuk radius domisili (agar gampang dibersihkan)
        nearLayer = L.layerGroup().addTo(map);

        // Fetch awal sesuai mode (default)
        await fetchData("", { mode: currentMode });

        map.on("zoomend moveend", () => {
            recomputeKDE();
            updateMarkers();
        });

        // ====== handler popup "Lihat detail" (tetap) ======
        map.on("popupopen", (e) => {
            const link = e.popup.getElement().querySelector(".lihat-detail");
            if (!link) return;

            link.addEventListener("click", async (evt) => {
                evt.preventDefault();
                const id = link.dataset.id;
                try {
                    const r = await fetch(`/pelamar/hirings/${id}`, {
                        credentials: "same-origin",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            Accept: "application/json",
                        },
                    });
                    if (!r.ok) throw new Error(`HTTP ${r.status}`);
                    const job = await r.json();
                    const target = document.getElementById("job-detail");

                    function makeBulletList(text) {
                        if (!text) return "<p>-</p>";
                        const parts = String(text)
                            .split(";")
                            .map((p) => p.trim())
                            .filter(Boolean);
                        return parts.length
                            ? `<ul class="pl-3 mb-0">${parts
                                  .map((p) => `<li>${p}</li>`)
                                  .join("")}</ul>`
                            : "<p>-</p>";
                    }

                    let btn = "";
                    if (job.is_closed)
                        btn = `<p class="text-danger mt-3">Lowongan Ditutup</p>`;
                    else if (job.has_applied)
                        btn = `<p class="text-success mt-3">Sudah Melamar</p>`;
                    else
                        btn = `<button class="btn btn-primary mt-3" onclick="openApplicationModal('${job.id}')">Kirim Lamaran</button>`;

                    if (target) {
                        // helper list dengan pemisah koma
                        function makeCommaList(text) {
                            if (!text) return "<p>-</p>";
                            const parts = String(text)
                                .split(",")
                                .map((p) => p.trim())
                                .filter(Boolean);
                            if (!parts.length) return "<p>-</p>";
                            return `<ul class="pl-3 mb-0">${parts
                                .map((p) => `<li>${p}</li>`)
                                .join("")}</ul>`;
                        }

                        target.innerHTML = `
                            <div class="d-flex align-items-center mb-4">
                                <img src="${
                                    job.personal_company_logo ??
                                    "/images/default-company.png"
                                }"
                                    style="width:70px;height:70px;object-fit:contain;border-radius:8px;border:1px solid #ccc;background:#f5f5f5;" class="mr-3">
                                <div>
                                    <h5 class="font-weight-bold mb-1">${
                                        job.position_hiring ?? "-"
                                    }</h5>
                                    <small class="text-muted">${
                                        job.company_name ?? "-"
                                    }</small>
                                </div>
                            </div>

                            <ul class="list-unstyled mb-4">
                                <li class="d-flex align-items-center mb-2">
                                    <i class="fas fa-map-marker-alt mr-2 text-secondary" style="width:18px;text-align:center;"></i>
                                    <span>${job.kota ?? ""}${
                            job.provinsi ? ", " + job.provinsi : ""
                        }</span>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="fas fa-building mr-2 text-secondary" style="width:18px;text-align:center;"></i>
                                    <span>${job.type_of_company ?? "-"}</span>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="fas fa-money-bill-wave mr-2 text-secondary" style="width:18px;text-align:center;"></i>
                                    <span>Rp ${new Intl.NumberFormat(
                                        "id-ID"
                                    ).format(job.gaji_min ?? 0)} -
                                        Rp ${new Intl.NumberFormat(
                                            "id-ID"
                                        ).format(
                                            job.gaji_max ?? 0
                                        )} / Bulan</span>
                                </li>
                                <li class="d-flex align-items-center">
                                    <i class="fas fa-clock mr-2 text-secondary" style="width:18px;text-align:center;"></i>
                                    <span>Batas Waktu: ${
                                        job.deadline_hiring
                                            ? new Date(
                                                  job.deadline_hiring
                                              ).toLocaleDateString("id-ID", {
                                                  day: "2-digit",
                                                  month: "long",
                                                  year: "numeric",
                                              })
                                            : "-"
                                    }</span>
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
                    }

                    if (window.scrollToDetailHeader)
                        window.scrollToDetailHeader();
                } catch (err) {
                    console.error("[detail] gagal:", err);
                    const target = document.getElementById("job-detail");
                    if (target)
                        target.innerHTML =
                            '<div class="text-danger">Gagal memuat detail lowongan</div>';
                }
            });
        });

        // ========================= Search & Suggestions (tetap) =========================
        const $input = document.getElementById("job-search");
        const $reset = document.getElementById("job-search-reset");
        const $suggestions = document.getElementById("job-suggestions");
        const $btnSearch = document.getElementById("job-search-btn");
        const $wrap = document.getElementById("job-search-wrap");

        function scrollToRekomendasiHeader() {
            const header = document.getElementById("rekomendasi-title");
            if (header)
                header.scrollIntoView({ behavior: "smooth", block: "start" });
        }

        async function doSearchManual() {
            const query = $input.value.trim();
            if (!query) return;

            await fetchData(query, { mode: currentMode });

            const container = document.getElementById("rekomendasi-container");
            const counter = document.getElementById("rekomendasi-count");

            if (!jobs.length) {
                if (counter) counter.textContent = 0;
                if (container)
                    container.innerHTML = `<p class="text-center text-muted">data pekerjaan yang anda cari tidak tersedia</p>`;
            } else {
                renderRekomendasi(jobs, query);
            }

            $input.value = "";
            scrollToRekomendasiHeader();
        }

        if ($btnSearch)
            $btnSearch.addEventListener("click", (e) => {
                e.preventDefault();
                doSearchManual();
            });

        async function fetchSuggestions(q) {
            if (!q || q.length < 2) {
                $suggestions.style.display = "none";
                return;
            }
            try {
                const res = await fetch(
                    `/api/job-suggestions?q=${encodeURIComponent(q)}`,
                    {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            Accept: "application/json",
                        },
                    }
                );
                if (!res.ok) throw new Error("HTTP " + res.status);
                const data = await res.json();
                renderSuggestions(data);
            } catch (err) {
                console.error("❌ suggestion error:", err);
                $suggestions.style.display = "none";
            }
        }

        function renderSuggestions(list) {
            $suggestions.innerHTML = "";
            if (!list || !list.length) {
                $suggestions.style.display = "none";
                return;
            }
            list.forEach((item) => {
                const li = document.createElement("li");
                li.className = "list-group-item list-group-item-action";
                li.textContent = item;
                li.style.cursor = "pointer";
                li.addEventListener("click", async () => {
                    $input.value = item;
                    $suggestions.style.display = "none";
                    await fetchData(item, { mode: currentMode });

                    if (!jobs.length) {
                        const container = document.getElementById(
                            "rekomendasi-container"
                        );
                        const counter =
                            document.getElementById("rekomendasi-count");
                        if (counter) counter.textContent = 0;
                        if (container)
                            container.innerHTML = `<p class="text-center text-muted">data pekerjaan yang anda cari tidak tersedia</p>`;
                    } else {
                        renderRekomendasi(jobs, item);
                    }

                    $input.value = "";
                    scrollToRekomendasiHeader();
                });
                $suggestions.appendChild(li);
            });
            $suggestions.style.display = "block";
        }

        const doSuggest = ((fn) => {
            let t;
            return (...a) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...a), 300);
            };
        })((v) => fetchSuggestions(v));
        if ($input) {
            $input.addEventListener("input", () => doSuggest($input.value));
            $input.addEventListener("keydown", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault();
                    doSearchManual();
                }
            });
        }

        if ($reset) {
            $reset.addEventListener("click", async (e) => {
                e.preventDefault();
                $input.value = "";
                $suggestions.style.display = "none";
                await fetchData("", { mode: currentMode });
                scrollToRekomendasiHeader();
            });
        }

        function hideSuggestions() {
            if ($suggestions) $suggestions.style.display = "none";
        }
        document.addEventListener("click", (e) => {
            const wrap = $wrap;
            if (!wrap) return;
            if (!wrap.contains(e.target)) hideSuggestions();
        });
        if ($input) {
            $input.addEventListener("blur", () =>
                setTimeout(hideSuggestions, 120)
            );
            $input.addEventListener("keydown", (e) => {
                if (e.key === "Escape") hideSuggestions();
            });
        }
        window.addEventListener("scroll", hideSuggestions, { passive: true });
        window.addEventListener("resize", hideSuggestions, { passive: true });

        // ========================= Toggle Mode Lokasi (Dropdown) =========================
        const modeSelect = document.getElementById("mode-select");
        if (modeSelect) {
            // set currentMode awal dari select
            currentMode = modeSelect.value;

            modeSelect.addEventListener("change", async () => {
                currentMode = modeSelect.value;

                // validasi koordinat
                if (
                    currentMode === "nearby" &&
                    !(
                        Number.isFinite(USER_HOME?.lat) &&
                        Number.isFinite(USER_HOME?.lon)
                    )
                ) {
                    await Swal.fire(
                        "Info",
                        "Koordinat domisili belum tersedia. Silakan lengkapi di Pengaturan Akun.",
                        "info"
                    );
                    currentMode = "default";
                    modeSelect.value = "default";
                }

                await fetchData("", { mode: currentMode });

                const header = document.getElementById("rekomendasi-title");
                if (header)
                    header.scrollIntoView({
                        behavior: "smooth",
                        block: "start",
                    });
            });
        }

        document.addEventListener("HEATMAP:radius-change", async (e) => {
            const modeSelect = document.getElementById("mode-select");
            const km = e.detail && Number(e.detail.radiusKm);

            // Apakah user saat ini berada/ingin berada di nearby?
            const isNearbySelected =
                (modeSelect && modeSelect.value === "nearby") ||
                currentMode === "nearby";

            // Jika radius tidak valid (anggap reset), dan memang sedang nearby -> kembali ke default.
            if (!Number.isFinite(km)) {
                if (isNearbySelected) {
                    currentMode = "default";
                    if (modeSelect) modeSelect.value = "default";
                    // fetch default + bersihkan circle (fetchData default juga remove circle)
                    await fetchData("", { mode: "default" });
                }
                return;
            }

            // Jika dropdown sedang "default", JANGAN paksa ke nearby (user memang mau default).
            // Tapi kalau dropdown bukan default (atau sebelumnya nearby), barulah set nearby.
            if (!isNearbySelected) {
                if (modeSelect) modeSelect.value = "nearby";
                currentMode = "nearby";
            }

            // Refresh data & gambar circle dengan radius terbaru
            await fetchData("", { mode: "nearby", radiusKm: km });
        });
    } catch (err) {
        console.error("❌ Error:", err);
        alert("Gagal memuat data heatmap. Cek konsol.");
    }
});
