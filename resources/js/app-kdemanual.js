// heatmap-kde-pai.js
// Leaflet + KDE manual + Evaluasi HR/PAI (versi jurnal) dengan split deterministik

import L from "leaflet";
import "leaflet.heat";
import "leaflet/dist/leaflet.css";

// ====== perbaiki icon leaflet (bundler)
import iconUrl from "leaflet/dist/images/marker-icon.png";
import iconRetinaUrl from "leaflet/dist/images/marker-icon-2x.png";
import shadowUrl from "leaflet/dist/images/marker-shadow.png";
L.Icon.Default.mergeOptions({ iconRetinaUrl, iconUrl, shadowUrl });

// ========================= KONFIG EVALUASI =========================
const PAI_ALPHAS = [0.01, 0.05, 0.1]; // proporsi area yg diuji (AP) → 1%, 5%, 10%
const SPLIT_SEED = 42; // seed tetap agar hasil konsisten
const TRAIN_RATIO = 0.7; // 70% train, 30% test
const FIXED_BINS = [256, 256];
const FIXED_SIGMA_M = 1200;

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

// ========================= Gaya heat adaptif (untuk tampilan) =========================
function tfixedtHeatStyleForZoom(z) {
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

// ========================= KDE MANUAL (untuk tampilan & evaluasi) =========================
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

// ========================= PARAMETER GRID =========================
// (1) Untuk TAMPILAN: adaptif sesuai viewport
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
    const sigmaM = 0.8 * Math.max(cellX, cellY);

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

// (2) Untuk EVALUASI: tetap (tidak tergantung viewport) → sesuai jurnal
function getFixedEvalParams() {
    if (!jobs.length) return null;
    let minLat = +Infinity,
        maxLat = -Infinity,
        minLon = +Infinity,
        maxLon = -Infinity;
    for (const j of jobs) {
        if (!Number.isFinite(j.lat) || !Number.isFinite(j.lon)) continue;
        if (j.lat < minLat) minLat = j.lat;
        if (j.lat > maxLat) maxLat = j.lat;
        if (j.lon < minLon) minLon = j.lon;
        if (j.lon > maxLon) maxLon = j.lon;
    }
    const [minx, miny] = lngLatToMeters(minLon, minLat);
    const [maxx, maxy] = lngLatToMeters(maxLon, maxLat);
    const pad = 1000;

    const extent = [
        [minx - pad, maxx + pad],
        [miny - pad, maxy + pad],
    ];
    const bins = FIXED_BINS.slice();
    const cellX = (extent[0][1] - extent[0][0]) / bins[0];
    const cellY = (extent[1][1] - extent[1][0]) / bins[1];
    const sigmaM = FIXED_SIGMA_M;

    return { extent, bins, cell: [cellX, cellY], sigmaM };
}

// ========================= GRID DENSITAS UNTUK EVALUASI =========================
function computeDensityGridFor(pointsLngLat) {
    if (!pointsLngLat || !pointsLngLat.length) return null;
    const fp = getFixedEvalParams();
    if (!fp) return null;

    const { extent, bins, cell, sigmaM } = fp;
    const [minx, maxx] = extent[0];
    const [miny, maxy] = extent[1];
    const [w, h] = bins;
    const [cellX, cellY] = cell;

    const ptsM = pointsLngLat.map(([lat, lon]) => lngLatToMeters(lon, lat));
    const sigmaPx = sigmaM / Math.max(cellX, cellY);

    const { data: hist } = makeHistogram2D(ptsM, extent, bins);
    const kernel = gaussianKernel1D(sigmaPx);
    const density = convolveSeparable(hist, w, h, kernel);

    let dmax = 0;
    for (let i = 0; i < density.length; i++)
        if (density[i] > dmax) dmax = density[i];

    const z = new Float32Array(w * h);
    if (dmax > 0)
        for (let i = 0; i < density.length; i++) z[i] = density[i] / dmax;

    return { w, h, minx, miny, cellX, cellY, z };
}

// ========================= UTIL EVALUASI HR & PAI =========================
let lastEvalGrid = null; // {w,h,minx,miny,cellX,cellY,z}

function idxOf(lat, lon, grid) {
    const { w, h, minx, miny, cellX, cellY } = grid;
    const [x, y] = lngLatToMeters(lon, lat);
    const ix = Math.floor((x - minx) / cellX);
    const iy = Math.floor((y - miny) / cellY);
    if (ix < 0 || ix >= w || iy < 0 || iy >= h) return -1;
    return iy * w + ix;
}

// Versi jurnal: AP = α (proporsi area), HR = hits / total_test, PAI = HR / AP
function evaluatePAI(testPoints, alphas = PAI_ALPHAS) {
    if (!lastEvalGrid) {
        console.warn(
            "Grid evaluasi belum ada. Bangun dengan computeDensityGridFor()."
        );
        return null;
    }
    const { w, h, z } = lastEvalGrid;
    const N = w * h;

    // Ranking sel berdasar skor KDE
    const idxs = Array.from({ length: N }, (_, i) => i).sort(
        (a, b) => z[b] - z[a]
    );

    const totalPts = testPoints.length;
    if (!totalPts) return null;

    const out = [];
    for (const alpha of alphas) {
        const topK = Math.max(1, Math.floor(alpha * N));
        const chosen = new Set(idxs.slice(0, topK));

        let hits = 0;
        for (const pt of testPoints) {
            const i = idxOf(pt.lat, pt.lon, lastEvalGrid);
            if (i >= 0 && chosen.has(i)) hits++;
        }
        const HR = hits / totalPts;
        const AP = alpha; // sesuai definisi
        const PAI = AP > 0 ? HR / AP : 0;

        out.push({ alpha, AP, HR, PAI, hits, totalPts, topCells: topK });
    }
    return out;
}

// ========================= KDE -> HEAT (untuk TAMPILAN) =========================
function recomputeKDE() {
    if (!mPoints || !mPoints.length) {
        heatLayer.setLatLngs([]);
        lastEvalGrid = null;
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
        lastEvalGrid = {
            w,
            h,
            minx,
            miny,
            cellX,
            cellY,
            z: new Float32Array(w * h),
        };
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

    // simpan raster tampilan terakhir (berguna bila ingin dipakai langsung; EVAL tetap pakai grid tetap)
    lastEvalGrid = { w, h, minx, miny, cellX, cellY, z: zRaster };
}

// ========================= PRNG + SPLIT DETERMINISTIK =========================
function mulberry32(seed) {
    let t = seed >>> 0;
    return function () {
        t += 0x6d2b79f5;
        let r = Math.imul(t ^ (t >>> 15), 1 | t);
        r ^= r + Math.imul(r ^ (r >>> 7), 61 | r);
        return ((r ^ (r >>> 14)) >>> 0) / 4294967296;
    };
}
function shuffleSeeded(arr, seed = 42) {
    const rng = mulberry32(seed);
    const a = arr.slice();
    for (let i = a.length - 1; i > 0; i--) {
        const j = Math.floor(rng() * (i + 1));
        [a[i], a[j]] = [a[j], a[i]];
    }
    return a;
}

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

        // ===== tombol RUN PAI (versi jurnal) =====
        (function injectPAIButton() {
            const toolbar =
                document.querySelector(".toolbar") ||
                document.querySelector("#heatmap-controls") ||
                document.querySelector("#job-search-wrap");
            if (!toolbar) return;

            const btn = document.createElement("button");
            btn.id = "run-pai";
            btn.className = "btn btn-outline-primary";
            btn.style.marginLeft = "8px";
            btn.textContent = "Run PAI";
            toolbar.appendChild(btn);

            btn.addEventListener("click", () => {
                try {
                    if (!jobs.length) {
                        window.Swal
                            ? Swal.fire(
                                  "Info",
                                  "Tidak ada data untuk evaluasi.",
                                  "info"
                              )
                            : alert("Tidak ada data untuk evaluasi.");
                        return;
                    }

                    // ===== Split deterministik 70/30 =====
                    const shuffled = shuffleSeeded(jobs, SPLIT_SEED);
                    const cut = Math.max(
                        1,
                        Math.floor(TRAIN_RATIO * shuffled.length)
                    );
                    const train = shuffled.slice(0, cut);
                    const test = shuffled.slice(cut);

                    if (!test.length) {
                        window.Swal
                            ? Swal.fire(
                                  "Info",
                                  "Dataset terlalu kecil untuk split 70/30.",
                                  "info"
                              )
                            : alert("Dataset terlalu kecil.");
                        return;
                    }

                    // ===== Grid KDE dari TRAIN (grid evaluasi tetap) =====
                    const grid = computeDensityGridFor(
                        train.map((j) => [j.lat, j.lon])
                    );
                    if (!grid) {
                        window.Swal
                            ? Swal.fire(
                                  "Gagal",
                                  "Tidak bisa membangun grid evaluasi.",
                                  "error"
                              )
                            : alert("Gagal bangun grid.");
                        return;
                    }

                    // Simpan sementara & evaluasi
                    const prev = lastEvalGrid;
                    lastEvalGrid = grid;

                    const out = evaluatePAI(
                        test.map((j) => ({ lat: j.lat, lon: j.lon })),
                        PAI_ALPHAS
                    );
                    lastEvalGrid = prev;

                    if (!out) return;

                    console.table(
                        out.map((r) => ({
                            "Alpha %": r.alpha * 100,
                            "Top Cells": r.topCells,
                            Hits: r.hits,
                            Total: r.totalPts,
                            HR: r.HR.toFixed(3),
                            AP: r.AP.toFixed(3),
                            PAI: r.PAI.toFixed(2),
                        }))
                    );

                    const lines = out
                        .map(
                            (r) =>
                                `α=${(r.alpha * 100).toFixed(
                                    0
                                )}% → HR=${r.HR.toFixed(
                                    3
                                )}, PAI=${r.PAI.toFixed(2)} (${r.hits}/${
                                    r.totalPts
                                })`
                        )
                        .join("<br/>");

                    if (window.Swal)
                        Swal.fire({
                            title: "Hasil PAI",
                            html: lines,
                            icon: "info",
                        });
                    else
                        alert(
                            out
                                .map(
                                    (r) =>
                                        `α ${(r.alpha * 100).toFixed(
                                            0
                                        )}%: HR ${r.HR.toFixed(
                                            3
                                        )}, PAI ${r.PAI.toFixed(2)}`
                                )
                                .join("\n")
                        );
                } catch (e) {
                    console.error("[PAI] error:", e);
                    window.Swal
                        ? Swal.fire(
                              "Gagal",
                              "Terjadi error saat evaluasi.",
                              "error"
                          )
                        : alert("Gagal evaluasi PAI. Cek konsol.");
                }
            });
        })();

        // ====== (opsional) popup “Lihat detail” tetap sama seperti punyamu ======
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
                    const bullet = (text) => {
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
                    };
                    const comma = (text) => {
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
                    };
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
                <li class="d-flex align-items-center mb-2"><i class="fas fa-map-marker-alt mr-2 text-secondary" style="width:18px;text-align:center;"></i>
                  <span>${job.kota ?? ""}${
                            job.provinsi ? ", " + job.provinsi : ""
                        }</span></li>
                <li class="d-flex align-items-center mb-2"><i class="fas fa-building mr-2 text-secondary" style="width:18px;text-align:center;"></i>
                  <span>${job.type_of_company ?? "-"}</span></li>
                <li class="d-flex align-items-center mb-2"><i class="fas fa-money-bill-wave mr-2 text-secondary" style="width:18px;text-align:center;"></i>
                  <span>Rp ${new Intl.NumberFormat("id-ID").format(
                      job.gaji_min ?? 0
                  )} - Rp ${new Intl.NumberFormat("id-ID").format(
                            job.gaji_max ?? 0
                        )} / Bulan</span></li>
                <li class="d-flex align-items-center"><i class="fas fa-clock mr-2 text-secondary" style="width:18px;text-align:center;"></i>
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
                  }</span></li>
              </ul>
              <div class="mb-3"><h6 class="font-weight-bold">Deskripsi Pekerjaan</h6>${bullet(
                  job.description_hiring
              )}</div>
              <div class="mb-3"><h6 class="font-weight-bold">Kualifikasi</h6>${bullet(
                  job.kualifikasi
              )}</div>
              <div class="mb-3"><h6 class="font-weight-bold">Keterampilan Teknis</h6>${comma(
                  job.keterampilan_teknis
              )}</div>
              <div class="mb-3"><h6 class="font-weight-bold">Keterampilan Non-Teknis</h6>${comma(
                  job.keterampilan_non_teknis
              )}</div>
              ${btn}`;
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
