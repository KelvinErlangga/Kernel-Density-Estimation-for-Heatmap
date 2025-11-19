// heatmap-kde.js
// Leaflet + KDE manual (tampilan saja) — PAI/Evaluasi DIHAPUS

import L from "leaflet";
import "leaflet.heat";
import "leaflet/dist/leaflet.css";

// ====== perbaiki icon leaflet (bundler)
import iconUrl from "leaflet/dist/images/marker-icon.png";
import iconRetinaUrl from "leaflet/dist/images/marker-icon-2x.png";
import shadowUrl from "leaflet/dist/images/marker-shadow.png";
L.Icon.Default.mergeOptions({ iconRetinaUrl, iconUrl, shadowUrl });

// ========================= STATE GLOBAL =========================
let jobs = [];
let locations = []; // [[lat, lon], ...]
let mPoints = []; // titik Web Mercator (meter): [[x,y], ...]
let map, heatLayer, markerLayer, nearCircle, nearLayer;
let currentMode = "default"; // 'default' | 'nearby'
const USER_HOME = window.USER_DOMICILE || {
    lat: null,
    lon: null,
    city: null,
    radiusKmDefault: 60,
};

// ===== Hint carousel state
let hintTimer = null;
let hintIdx = 0;

// ========================= Helper angka & koordinat =========================
const toNum = (v) => {
    if (v === null || v === undefined) return NaN;
    const s = String(v).trim().replace(",", ".");
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

// Haversine (fallback jarak)
function computeDistanceKm(lat1, lon1, lat2, lon2) {
    const toRad = (d) => (d * Math.PI) / 180;
    const RR = 6371;
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) ** 2;
    return 2 * RR * Math.asin(Math.sqrt(a));
}

// ========================= Popup builder =========================
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
function getHeatStyleForZoom(z) {
    const table = {
        10: { r: 6, b: 6, min: 0.48 },
        11: { r: 10, b: 9, min: 0.54 },
        12: { r: 24, b: 30, min: 0.6 },
        13: { r: 40, b: 47, min: 0.6 },
        14: { r: 50, b: 64, min: 0.62 },
        15: { r: 54, b: 74, min: 0.68 },
        16: { r: 54, b: 74, min: 0.8 },
        17: { r: 54, b: 74, min: 0.8 },
        18: { r: 64, b: 79, min: 0.8 },
    };
    const zc = Math.max(10, Math.min(18, Math.round(z)));
    return table[zc];
}
function applyHeatStyle() {
    if (!map || !heatLayer) return;
    const s = getHeatStyleForZoom(map.getZoom());
    heatLayer.setOptions({ radius: s.r, blur: s.b, minOpacity: s.min });
}

// ========================= KDE MANUAL (untuk tampilan) =========================
function makeHistogram2D(pointsM, extent, bins) {
    const [minx, maxx] = extent[0];
    const [miny, maxy] = extent[1];
    const [nx, ny] = bins;
    const w = nx,
        h = ny;
    const arr = new Float32Array(w * h);
    const invDx = w / (maxx - minx);
    const invDy = h / (maxy - miny);

    for (const [x, y] of pointsM) {
        const xi = Math.floor((x - minx) * invDx);
        const yi = Math.floor((y - miny) * invDy);
        if (xi >= 0 && xi < w && yi >= 0 && yi < h) arr[yi * w + xi] += 1;
    }
    return { data: arr, w, h, minx, miny };
}

function gaussianKernel1D(sigmaPixel) {
    const radius = Math.max(1, Math.ceil(3 * sigmaPixel));
    const len = 2 * radius + 1;
    const k = new Float32Array(len);
    const s2 = sigmaPixel * sigmaPixel;
    let sum = 0;
    for (let i = -radius; i <= radius; i++) {
        const v = Math.exp(-(i * i) / (2 * s2));
        k[i + radius] = v;
        sum += v;
    }
    for (let i = 0; i < len; i++) k[i] /= sum;
    return { k, radius };
}

function convolveSeparable(data, w, h, kernel) {
    const { k, radius } = kernel;
    const tmp = new Float32Array(w * h);
    const out = new Float32Array(w * h);

    // Horizontal
    for (let y = 0; y < h; y++) {
        const rowOff = y * w;
        for (let x = 0; x < w; x++) {
            let sum = 0;
            for (let t = -radius; t <= radius; t++) {
                const xx = Math.min(w - 1, Math.max(0, x + t));
                sum += data[rowOff + xx] * k[t + radius];
            }
            tmp[rowOff + x] = sum;
        }
    }
    // Vertical
    for (let x = 0; x < w; x++) {
        for (let y = 0; y < h; y++) {
            let sum = 0;
            for (let t = -radius; t <= radius; t++) {
                const yy = Math.min(h - 1, Math.max(0, y + t));
                sum += tmp[yy * w + x] * k[t + radius];
            }
            out[y * w + x] = sum;
        }
    }
    return out;
}

// ========================= PARAMETER GRID (tampilan) =========================
function getAdaptiveParams() {
    const b = map.getBounds();
    const [minx, miny] = lngLatToMeters(b.getWest(), b.getSouth());
    const [maxx, maxy] = lngLatToMeters(b.getEast(), b.getNorth());
    const widthM = maxx - minx;
    const heightM = maxy - miny;

    const px = map.getSize();
    const binsX = Math.max(96, Math.min(256, Math.round(px.x / 5)));
    const binsY = Math.max(96, Math.min(256, Math.round(px.y / 5)));

    const cellX = widthM / binsX;
    const cellY = heightM / binsY;
    const sigmaM = 1.6 * Math.max(cellX, cellY);

    return {
        extent: [
            [minx, maxx],
            [miny, maxy],
        ],
        bins: [binsX, binsY],
        cell: [cellX, cellY],
        sigmaM,
    };
}

// ========================= KDE -> HEAT (TAMPILAN SAJA) =========================
function recomputeKDE() {
    if (!mPoints || !mPoints.length) {
        heatLayer.setLatLngs([]);
        return;
    }

    const { extent, bins, cell, sigmaM } = getAdaptiveParams();
    const [w, h] = bins;
    const [minx, miny] = [extent[0][0], extent[1][0]];
    const [cellX, cellY] = cell;

    const sigmaPx = sigmaM / Math.max(cellX, cellY);
    const { data: hist } = makeHistogram2D(mPoints, extent, bins);
    const kernel = gaussianKernel1D(sigmaPx);
    const density = convolveSeparable(hist, w, h, kernel);

    // normalisasi & cutoff visual
    let dmax = 0;
    for (let i = 0; i < density.length; i++)
        if (density[i] > dmax) dmax = density[i];
    if (dmax <= 0) {
        heatLayer.setLatLngs([]);
        return;
    }
    const zRaster = new Float32Array(w * h);
    const vals = [];
    for (let i = 0; i < density.length; i++) {
        const v = density[i] / dmax;
        zRaster[i] = v;
        if (v > 0) vals.push(v);
    }
    vals.sort((a, b) => a - b);

    const zZoom = map.getZoom();
    let q = 0.75;
    if (zZoom <= 12) q = 0.86;
    else if (zZoom <= 14) q = 0.8;
    const qIdx = Math.floor(vals.length * q);
    const CUTOFF = Math.max(0.12, vals[qIdx] ?? 0.12);

    const heat = [];
    for (let iy = 0; iy < h; iy++) {
        for (let ix = 0; ix < w; ix++) {
            const v = zRaster[iy * w + ix];
            if (v < CUTOFF) continue;
            const gx = minx + (ix + 0.5) * cellX;
            const gy = miny + (iy + 0.5) * cellY;
            const [lon, lat] = metersToLngLat(gx, gy);
            if (!validLat(lat) || !validLon(lon)) continue;
            heat.push([lat, lon, v]);
        }
    }
    heatLayer.setLatLngs(heat);
}

// ========================= FOKUSKAN MAP KE LOWONGAN TERTENTU =========================
window.focusJobOnMap = function (id) {
    if (!map || !jobs || !jobs.length) return;

    const job = jobs.find((j) => String(j.id) === String(id));
    if (!job) return;

    const lat = toNum(job.lat);
    const lon = toNum(job.lon);
    if (!validLat(lat) || !validLon(lon)) return;

    const target = L.latLng(lat, lon);
    const targetZoom = Math.max(map.getZoom(), 16); // zoom dekat

    map.flyTo(target, targetZoom, {
        duration: 0.8,
    });
};

// Wrapper: dipanggil dari card rekomendasi (panel kanan)
window.handleJobCardClick = function (id) {
    if (typeof window.focusJobOnMap === "function") {
        window.focusJobOnMap(id);
    }
    if (typeof window.showJobDetail === "function") {
        window.showJobDetail(id);
    }
};

// ========================= MARKER & REKOMENDASI (UI) =========================
const BRIEFCASE_PIN_ICON = L.divIcon({
    className: "job-briefcase-pin",
    html: `
    <svg viewBox="0 0 38 54" width="38" height="54" xmlns="http://www.w3.org/2000/svg">
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

function renderRekomendasi(list, highlightQuery = "") {
    const container = document.getElementById("rekomendasi-container");
    const counter = document.getElementById("rekomendasi-count");
    if (!container) return;
    if (counter) counter.textContent = list.length;

    if (!list.length) {
        container.innerHTML = `<p class="text-center text-muted">Tidak ada rekomendasi lowongan</p>`;
        return;
    }

    // Sort jobs by matching percentage (from highest to lowest)
    list.sort((a, b) => {
        const aMatch = toNum(a.matching_percentage);
        const bMatch = toNum(b.matching_percentage);
        return bMatch - aMatch;
    });

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

            const match = Number.isFinite(toNum(job.matching_percentage))
                ? `${Math.round(toNum(job.matching_percentage))}%`
                : "0%";

            return `
      <div class="card mb-3 border job-card" style="cursor:pointer;"
           onclick="handleJobCardClick('${job.id}')" tabindex="0">
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
            <div class="mt-1 font-weight-bold">
              Kecocokan CV kamu: ${match}
            </div>
          </div>
        </div>
      </div>`;
        })
        .join("");
}

// ========================= HINT BANNER (UI) =========================
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

    const match = Number.isFinite(toNum(job.matching_percentage))
        ? `${Math.round(toNum(job.matching_percentage))}%`
        : "0%";

    const msgHtml = `Posisi “${pos}” di ${comp} cocok dengan CV kamu (${match})${jarakTxt}, buruan daftar! <a href="#" id="hint-cta" class="ms-1">Lihat & daftar</a>`;

    const setContent = () => {
        inner.innerHTML = msgHtml;
        const cta = document.getElementById("hint-cta");
        if (cta) {
            cta.onclick = (e) => {
                e.preventDefault();
                if (typeof window.handleJobCardClick === "function") {
                    window.handleJobCardClick(job.id);
                } else if (typeof window.showJobDetail === "function") {
                    window.showJobDetail(job.id);
                }
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

// ========================= FETCH DATA =========================
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

    const rawJobs = rawData.map((d) => ({
        ...d,
        lat: toNum(d.latitude),
        lon: toNum(d.longitude),
    }));
    jobs = rawJobs.filter((j) => validLat(j.lat) && validLon(j.lon));
    locations = jobs.map((j) => [j.lat, j.lon]);
    mPoints = locations.map(([lat, lon]) => lngLatToMeters(lon, lat));

    if (mode === "nearby") {
        jobs.sort(
            (a, b) =>
                (toNum(a.distance_km) || 1e9) - (toNum(b.distance_km) || 1e9)
        );
    }

    // overlay radius
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
        if (jobs.length) {
            const bb = L.latLngBounds(locations);
            map.fitBounds(bb.pad(0.2));
        }
    }

    renderRekomendasi(jobs);
    startHintCarousel(jobs);
    applyHeatStyle();
    recomputeKDE();
    updateMarkers();
}

// ========================= DOM READY =========================
document.addEventListener("DOMContentLoaded", async () => {
    try {
        // Map & layers
        map = L.map("map").setView([-7.3, 112.7], 12);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors",
        }).addTo(map);

        // Panes
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
            radius: 40,
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
            applyHeatStyle();
            recomputeKDE();
            updateMarkers();
        });

        // ====== popup “Lihat detail”
        map.on("popupopen", (e) => {
            const link = e.popup.getElement().querySelector(".lihat-detail");
            if (!link) return;
            link.addEventListener("click", async (evt) => {
                evt.preventDefault();
                const id = link.dataset.id;

                if (typeof window.handleJobCardClick === "function") {
                    window.handleJobCardClick(id);
                    return;
                }
                if (typeof window.showJobDetail === "function") {
                    window.showJobDetail(id);
                }
            });
        });

        // ========================= Search & Suggestions
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
        })(fetchSuggestions);
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

        // ========================= Mode Lokasi & Radius =========================
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
    } catch (err) {
        console.error("❌ Error:", err);
        alert("Gagal memuat data heatmap. Cek konsol.");
    }
});
