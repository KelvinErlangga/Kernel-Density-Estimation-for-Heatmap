// heatmap-simple.js
// Heatmap polosan (leaflet.heat) TANPA evaluasi PAI/HR (semua terkait PAI dihapus)

import L from "leaflet";
import "leaflet.heat";
import "leaflet/dist/leaflet.css";

// Perbaiki path icon default (bundler/Vite)
import iconUrl from "leaflet/dist/images/marker-icon.png";
import iconRetinaUrl from "leaflet/dist/images/marker-icon-2x.png";
import shadowUrl from "leaflet/dist/images/marker-shadow.png";
L.Icon.Default.mergeOptions({ iconRetinaUrl, iconUrl, shadowUrl });

/* ========================= STATE GLOBAL ========================= */
let jobs = [];
let locations = []; // [[lat, lon], ...]
let map, heatLayer, markerLayer, nearCircle, nearLayer;
let currentMode = "default"; // 'default' | 'nearby'
const USER_HOME = window.USER_DOMICILE || {
    lat: null,
    lon: null,
    city: null,
    radiusKmDefault: 60,
};

// Hint carousel
let hintTimer = null;
let hintIdx = 0;

/* ========================= Helpers umum ========================= */
const toNum = (v) => {
    if (v === null || v === undefined) return NaN;
    const s = String(v).trim().replace(",", ".");
    const n = parseFloat(s);
    return Number.isFinite(n) ? n : NaN;
};
const validLat = (x) => Number.isFinite(x) && x >= -90 && x <= 90;
const validLon = (x) => Number.isFinite(x) && x >= -180 && x <= 180;

// Haversine (jarak untuk hint)
function computeDistanceKm(lat1, lon1, lat2, lon2) {
    const toRad = (d) => (d * Math.PI) / 180;
    const R = 6371;
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) ** 2;
    return 2 * R * Math.asin(Math.sqrt(a));
}

/* ========================= Popup ========================= */
function buildPopup(job) {
    const posisi = job.position_hiring ?? "Lowongan";
    const lokasi = [job.kota, job.provinsi].filter(Boolean).join(", ");
    const gajiMin = new Intl.NumberFormat("id-ID").format(job.gaji_min ?? 0);
    const gajiMax = new Intl.NumberFormat("id-ID").format(job.gaji_max ?? 0);
    return `
    <div style="min-width:240px">
      <div style="font-weight:700;font-size:14px;margin-bottom:6px">${posisi}</div>
      <div style="font-size:12px;color:#555;margin-bottom:6px">${
          lokasi || "-"
      }</div>
      <div style="font-size:12px;line-height:1.35">
        <div><b>Gaji</b>: Rp ${gajiMin} – Rp ${gajiMax}/Bulan</div>
      </div>
      <div style="margin-top:8px">
        <a href="#" class="lihat-detail" data-id="${job.id}">Lihat detail</a>
      </div>
    </div>`;
}

/* ===== Gaya heat adaptif (visual) ===== */
function getHeatStyleForZoom(z) {
    const table = {
        10: { r: 8, b: 6, min: 0.25 },
        11: { r: 10, b: 7, min: 0.25 },
        12: { r: 14, b: 9, min: 0.28 },
        13: { r: 20, b: 12, min: 0.3 },
        14: { r: 26, b: 15, min: 0.32 },
        15: { r: 34, b: 18, min: 0.33 },
        16: { r: 42, b: 20, min: 0.34 },
        17: { r: 50, b: 22, min: 0.35 },
    };
    const zc = Math.max(10, Math.min(17, Math.round(z)));
    return table[zc];
}
function applyHeatStyle() {
    if (!map || !heatLayer) return;
    const s = getHeatStyleForZoom(map.getZoom());
    heatLayer.setOptions({ radius: s.r, blur: s.b, minOpacity: s.min });
}

/* ========================= Heatmap murni (polosan) =========================
   Untuk tampilan saja: ambil titik dalam viewport (+padding) dan kirim ke leaflet.heat
*/
function recomputeHeat() {
    if (!locations.length) {
        heatLayer.setLatLngs([]);
        return;
    }
    const bounds = map.getBounds();

    // padding kecil agar transisi halus saat peta digeser/zoom
    const padLat = (bounds.getNorth() - bounds.getSouth()) * 0.08;
    const padLng = (bounds.getEast() - bounds.getWest()) * 0.08;
    const south = bounds.getSouth() - padLat;
    const north = bounds.getNorth() + padLat;
    const west = bounds.getWest() - padLng;
    const east = bounds.getEast() + padLng;

    const inView = locations.filter(
        ([lat, lon]) =>
            lat >= south && lat <= north && lon >= west && lon <= east
    );
    const heat = inView.map(([lat, lon]) => [lat, lon, 1]); // intensitas=1
    heatLayer.setLatLngs(heat);
}

/* ========================= Marker interaktif ========================= */
const BRIEFCASE_PIN_ICON = L.divIcon({
    className: "job-briefcase-pin",
    html: `
    <svg viewBox="0 0 38 54" width="38" height="54"
         xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0 1px 3px rgba(0,0,0,.35))">
      <path fill="#1d4ed8" d="M19 0C8.5 0 0 8.5 0 19c0 13.2 19 35 19 35s19-21.8 19-35C38 8.5 29.5 0 19 0z"/>
      <image href="/assets/icons/briefcase.svg" x="10" y="11" width="18" height="18" style="filter:brightness(0) invert(1)"/>
    </svg>`,
    iconSize: [48, 64],
    iconAnchor: [19, 52],
    popupAnchor: [0, -46],
});

function updateMarkers() {
    markerLayer.clearLayers();
    const MARKER_ZOOM_THRESHOLD = 15;
    if (map.getZoom() < MARKER_ZOOM_THRESHOLD) return;

    const bounds = map.getBounds();
    const visibleJobs = jobs.filter((j) => bounds.contains([j.lat, j.lon]));
    visibleJobs.forEach((job) => {
        L.marker([job.lat, job.lon], {
            pane: "markers",
            icon: BRIEFCASE_PIN_ICON,
        })
            .bindPopup(buildPopup(job))
            .addTo(markerLayer);
    });
}

/* ========================= Rekomendasi ========================= */
function renderRekomendasi(list, highlightQuery = "") {
    const container = document.getElementById("rekomendasi-container");
    const counter = document.getElementById("rekomendasi-count");
    if (!container) return;
    if (counter) counter.textContent = list.length;

    if (!list.length) {
        container.innerHTML = `<p class="text-center text-muted">Tidak ada rekomendasi lowongan</p>`;
        return;
    }

    if (highlightQuery) {
        list.sort((a, b) => {
            const aMatch = (a.position_hiring ?? "")
                .toLowerCase()
                .includes(highlightQuery.toLowerCase());
            const bMatch = (b.position_hiring ?? "")
                .toLowerCase()
                .includes(highlightQuery.toLowerCase());
            return Number(bMatch) - Number(aMatch);
        });
    }

    container.innerHTML = list
        .map((job) => {
            const dist = Number.isFinite(toNum(job.distance_km))
                ? `<small class="d-block text-muted">≈ ${toNum(
                      job.distance_km
                  ).toFixed(1)} km dari lokasi Anda</small>`
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
               alt="Logo ${job.personal_company?.name_company ?? "Perusahaan"}"
               style="width:70px;height:70px;object-fit:contain;border-radius:6px;border:1px solid #ccc;background:#f5f5f5;" class="mr-3">
          <div>
            <h6 class="font-weight-bold mb-1">${job.position_hiring ?? "-"}</h6>
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

/* ========================= Hint Banner ========================= */
function renderHint(job, animate = true) {
    const bar = document.getElementById("match-hint");
    if (!bar || !job) return;

    if (!bar.querySelector(".hint-inner")) {
        bar.innerHTML = `<i class="fas fa-lightbulb icon"></i><span class="hint-inner"></span>`;
    }
    const inner = bar.querySelector(".hint-inner");

    const pos = job.position_hiring ?? "Pekerjaan";
    const comp =
        job.personal_company?.name_company ?? job.company_name ?? "perusahaan";

    let dkm = toNum(job.distance_km);
    const hasHome =
        Number.isFinite(USER_HOME?.lat) && Number.isFinite(USER_HOME?.lon);
    if (
        !Number.isFinite(dkm) &&
        hasHome &&
        Number.isFinite(toNum(job.lat)) &&
        Number.isFinite(toNum(job.lon))
    ) {
        dkm = computeDistanceKm(USER_HOME.lat, USER_HOME.lon, job.lat, job.lon);
    }
    const jarakTxt = Number.isFinite(dkm)
        ? ` dan jaraknya hanya ${dkm.toFixed(1)} km dari lokasi kamu`
        : "";
    const msgHtml = `Posisi “${pos}” di ${comp} cocok dengan skill kamu${jarakTxt}, buruan daftar! <a href="#" id="hint-cta" class="ms-1">Lihat & daftar</a>`;

    const setContent = () => {
        inner.innerHTML = msgHtml;
        const cta = document.getElementById("hint-cta");
        if (cta) {
            cta.onclick = (e) => {
                e.preventDefault();
                if (typeof showJobDetail === "function") showJobDetail(job.id);
                const header = document.getElementById("detail-title");
                if (header)
                    header.scrollIntoView({
                        behavior: "smooth",
                        block: "start",
                    });
            };
        }
    };

    bar.classList.remove("d-none");
    if (!animate) {
        inner.classList.remove("fade-out", "fade-in");
        setContent();
        return;
    }

    inner.classList.remove("fade-in");
    inner.classList.add("fade-out");
    setTimeout(() => {
        setContent();
        inner.classList.remove("fade-out");
        inner.classList.add("fade-in");
        setTimeout(() => inner.classList.remove("fade-in"), 380);
    }, 180);
}
function startHintCarousel(list) {
    const bar = document.getElementById("match-hint");
    if (!bar) return;

    if (hintTimer) {
        clearInterval(hintTimer);
        hintTimer = null;
    }
    hintIdx = 0;

    if (!Array.isArray(list) || list.length === 0) {
        bar.classList.add("d-none");
        return;
    }

    let candidates = list.slice(0, Math.min(8, list.length));
    if (currentMode === "nearby") {
        const withDist = candidates.filter((j) =>
            Number.isFinite(toNum(j.distance_km))
        );
        if (withDist.length) {
            withDist.sort(
                (a, b) =>
                    (toNum(a.distance_km) || 1e9) -
                    (toNum(b.distance_km) || 1e9)
            );
            candidates = withDist
                .concat(candidates.filter((j) => !withDist.includes(j)))
                .slice(0, 8);
        }
    }

    renderHint(candidates[hintIdx % candidates.length], false);
    hintIdx++;

    hintTimer = setInterval(() => {
        renderHint(candidates[hintIdx % candidates.length], true);
        hintIdx++;
    }, 7000);
}

/* ========================= Fetch data ========================= */
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

    // Urutkan jarak saat nearby (jika backend kirim distance_km)
    if (mode === "nearby") {
        jobs.sort(
            (a, b) =>
                (toNum(a.distance_km) || 1e9) - (toNum(b.distance_km) || 1e9)
        );
    }

    // Overlay radius
    if (mode === "nearby" && hasHome) {
        const radiusKm = Number(
            opts.radiusKm ?? USER_HOME.radiusKmDefault ?? 60
        );
        if (nearLayer) nearLayer.clearLayers();
        nearCircle = L.circle([USER_HOME.lat, USER_HOME.lon], {
            radius: radiusKm * 1000,
            color: "#1d4ed8",
            weight: 1,
            fillOpacity: 0.08,
            pane: "overlayPane",
        });
        nearLayer.addLayer(nearCircle);
        map.fitBounds(nearCircle.getBounds().pad(0.15));
    } else {
        if (nearLayer) nearLayer.clearLayers();
        nearCircle = null;
        if (locations.length) {
            const bb = L.latLngBounds(locations);
            map.fitBounds(bb.pad(0.2));
        }
    }

    // Render UI
    renderRekomendasi(jobs);
    startHintCarousel(jobs);
    applyHeatStyle();
    recomputeHeat();
    updateMarkers();
}

/* ========================= DOM Ready ========================= */
document.addEventListener("DOMContentLoaded", async () => {
    try {
        // Init map & layers
        map = L.map("map").setView([-7.3, 112.7], 12);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors",
        }).addTo(map);

        // Panes: heat di bawah marker
        if (!map.getPane("heat")) {
            const paneHeat = map.createPane("heat");
            paneHeat.style.zIndex = 450;
            paneHeat.style.pointerEvents = "none";
        }
        if (!map.getPane("markers")) {
            const paneMarkers = map.createPane("markers");
            paneMarkers.style.zIndex = 650;
        }

        heatLayer = L.heatLayer([], {
            pane: "heat",
            radius: 40, // awal, akan diubah oleh applyHeatStyle()
            blur: 12,
            maxZoom: 17,
            minOpacity: 0.3,
            gradient: {
                0.0: "transparent",
                0.25: "#4c6ef5",
                0.5: "#2dd4bf",
                0.75: "#f59e0b",
                1.0: "#ef4444",
            },
        }).addTo(map);
        applyHeatStyle();

        markerLayer = L.layerGroup([], { pane: "markers" }).addTo(map);
        nearLayer = L.layerGroup().addTo(map);

        // Fetch awal
        await fetchData("", { mode: currentMode });

        map.on("zoomend moveend", () => {
            applyHeatStyle(); // radius/blur adaptif
            recomputeHeat(); // hanya titik di viewport
            updateMarkers();
        });

        // ====== handler popup "Lihat detail" (UTUH) ======
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
                    function makeCommaList(text) {
                        if (!text) return "<p>-</p>";
                        const parts = String(text)
                            .split(",")
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
                        target.innerHTML = `
              <div class="d-flex align-items-center mb-4">
                <img src="${
                    job.personal_company_logo ?? "/images/default-company.png"
                }"
                     style="width:70px;height:70px;object-fit:contain;border-radius:8px;border:1px solid #ccc;background:#f5f5f5;" class="mr-3">
                <div>
                  <h5 class="font-weight-bold mb-1">${
                      job.position_hiring ?? "-"
                  }</h5>
                  <small class="text-muted">${job.company_name ?? "-"}</small>
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
                  <span>Rp ${new Intl.NumberFormat("id-ID").format(
                      job.gaji_min ?? 0
                  )} -
                        Rp ${new Intl.NumberFormat("id-ID").format(
                            job.gaji_max ?? 0
                        )} / Bulan</span>
                </li>
                <li class="d-flex align-items-center">
                  <i class="fas fa-clock mr-2 text-secondary" style="width:18px;text-align:center;"></i>
                  <span>Batas Waktu: ${
                      job.deadline_hiring
                          ? new Date(job.deadline_hiring).toLocaleDateString(
                                "id-ID",
                                {
                                    day: "2-digit",
                                    month: "long",
                                    year: "numeric",
                                }
                            )
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

        /* ========================= Search & Suggestions (UTUH) ========================= */
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

        if ($btnSearch) {
            $btnSearch.addEventListener("click", (e) => {
                e.preventDefault();
                doSearchManual();
            });
        }

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

        /* ========================= Toggle Mode & Radius ========================= */
        const modeSelect = document.getElementById("mode-select");
        if (modeSelect) {
            currentMode = modeSelect.value;

            modeSelect.addEventListener("change", async () => {
                currentMode = modeSelect.value;

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

        // Perubahan radius (Nearby)
        document.addEventListener("HEATMAP:radius-change", async (e) => {
            const modeSelect = document.getElementById("mode-select");
            const km = e.detail && Number(e.detail.radiusKm);

            const isNearbySelected =
                (modeSelect && modeSelect.value === "nearby") ||
                currentMode === "nearby";

            if (!Number.isFinite(km)) {
                if (isNearbySelected) {
                    currentMode = "default";
                    if (modeSelect) modeSelect.value = "default";
                    await fetchData("", { mode: "default" });
                }
                return;
            }

            if (!isNearbySelected) {
                if (modeSelect) modeSelect.value = "nearby";
                currentMode = "nearby";
            }

            await fetchData("", { mode: "nearby", radiusKm: km });
        });

        // ⚠️ Tidak ada tombol / handler PAI lagi.
    } catch (err) {
        console.error("❌ Error:", err);
        alert("Gagal memuat data heatmap. Cek konsol.");
    }
});
