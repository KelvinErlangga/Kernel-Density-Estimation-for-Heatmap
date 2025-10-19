import L from "leaflet";
import "leaflet.heat";
import { density2d } from "fast-kde";
import "leaflet/dist/leaflet.css";

// Fix icon default path (Vite)
import iconUrl from "leaflet/dist/images/marker-icon.png";
import iconRetinaUrl from "leaflet/dist/images/marker-icon-2x.png";
import shadowUrl from "leaflet/dist/images/marker-shadow.png";
L.Icon.Default.mergeOptions({ iconRetinaUrl, iconUrl, shadowUrl });

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

// Hint carousel
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

// ========================= Parameter KDE adaptif (fast-kde) =========================
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

    // bandwidth ~ 1.5x sel grid (stabil across zoom)
    const BAND_M = 1.5 * Math.max(cellX, cellY);
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

// ===== Gaya heat adaptif berdasar zoom =====
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

// ==== Evaluasi PAI (state & util) ====
let lastEvalGrid = null; // {w,h,minx,miny,cellX,cellY,z: Float32Array}

// bikin grid KDE dari kumpulan titik (untuk EVALUASI, param tetap)
function computeDensityGridFor(pointsLngLat) {
    if (!pointsLngLat || !pointsLngLat.length) return null;

    // >>> pakai parameter evaluasi tetap
    const fp = getFixedEvalParams();
    if (!fp) return null;
    const { extent, bins, cell, bandwidth } = fp;

    const [minx, maxx] = extent[0];
    const [miny, maxy] = extent[1];
    const [w, h] = bins;
    const [cellX, cellY] = cell;

    // projek ke meter
    const ptsM = pointsLngLat.map(([lat, lon]) => lngLatToMeters(lon, lat));

    // fast-kde
    const dens = density2d(ptsM, { bins, extent, bandwidth });

    // normalisasi ke [0..1] + raster
    let zmax = 0;
    for (const p of dens) if (p.z > zmax) zmax = p.z;

    const z = new Float32Array(w * h);
    for (const p of dens) {
        const v = zmax ? p.z / zmax : 0;
        let ix = Math.floor((p.x - minx) / cellX);
        let iy = Math.floor((p.y - miny) / cellY);
        if (ix < 0) ix = 0;
        else if (ix >= w) ix = w - 1;
        if (iy < 0) iy = 0;
        else if (iy >= h) iy = h - 1;
        const idx = iy * w + ix;
        if (v > z[idx]) z[idx] = v;
    }

    return { w, h, minx, miny, cellX, cellY, z };
}

// konversi satu titik (lat,lon) ke index sel pada grid
function idxOf(lat, lon, grid) {
    const { w, h, minx, miny, cellX, cellY } = grid;
    const [x, y] = lngLatToMeters(lon, lat);
    const ix = Math.floor((x - minx) / cellX);
    const iy = Math.floor((y - miny) / cellY);
    if (ix < 0 || ix >= w || iy < 0 || iy >= h) return -1; // di luar extent
    return iy * w + ix;
}

// hitung HR & PAI untuk daftar alpha (mis. [0.01, 0.05, 0.10])
function evaluatePAI(testPoints, alphas = [0.005, 0.01, 0.02]) {
    if (!lastEvalGrid) {
        console.warn(
            "Grid evaluasi belum ada. Pastikan recomputeKDE() sudah jalan."
        );
        return;
    }
    const { w, h, z } = lastEvalGrid;
    const N = w * h;

    // urutkan indeks sel berdasarkan skor z desc
    const idxs = Array.from({ length: N }, (_, i) => i);
    idxs.sort((a, b) => z[b] - z[a]); // descending

    const totalPts = testPoints.length;
    if (!totalPts) {
        console.warn("Tidak ada titik test.");
        return;
    }

    const results = [];
    for (const alpha of alphas) {
        const topK = Math.max(1, Math.floor(alpha * N)); // top-α% sel
        const chosen = new Set(idxs.slice(0, topK));

        let hits = 0;
        for (const pt of testPoints) {
            const i = idxOf(pt.lat, pt.lon, lastEvalGrid);
            if (i >= 0 && chosen.has(i)) hits++;
        }
        const HR = hits / totalPts;
        const PAI = HR / alpha;
        results.push({ alpha, HR, PAI, hits, totalPts, topCells: topK });
    }
    return results;
}

// ========= Repro helpers: PRNG ber-seed + param evaluasi tetap =========
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

// Parameter EVALUASI (tetap) — tidak tergantung map/zoom
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
    const [minx0, miny0] = lngLatToMeters(minLon, minLat);
    const [maxx0, maxy0] = lngLatToMeters(maxLon, maxLat);

    const pad = 1000; // buffer 1 km
    const extent = [
        [minx0 - pad, maxx0 + pad],
        [miny0 - pad, maxy0 + pad],
    ];

    const bins = [256, 256]; // grid tetap
    const cellX = (extent[0][1] - extent[0][0]) / bins[0];
    const cellY = (extent[1][1] - extent[1][0]) / bins[1];

    const BAND_M = 1200; // bandwidth tetap ~1.2 km (atur sesuai kebutuhan)
    const bandwidth = [BAND_M, BAND_M];

    return { extent, bins, cell: [cellX, cellY], bandwidth };
}

// ========================= KDE -> HeatLayer (fast-kde) =========================
function recomputeKDE() {
    if (!mPoints || !mPoints.length) {
        heatLayer.setLatLngs([]);
        lastEvalGrid = null;
        return;
    }

    const { extent, bins, bandwidth } = getAdaptiveParams();
    const [minx, maxx] = extent[0];
    const [miny, maxy] = extent[1];
    const [w, h] = bins;
    const cellX = (maxx - minx) / w;
    const cellY = (maxy - miny) / h;

    const dens = density2d(mPoints, { bins, extent, bandwidth });
    if (!dens) {
        heatLayer.setLatLngs([]);
        lastEvalGrid = null;
        return;
    }

    // --- cari zmax & siapkan values buat cutoff + heat ---
    let zmax = 0;
    for (const p of dens) if (p.z > zmax) zmax = p.z;

    // buat ulang iterator, isi untuk heat + kumpulkan vals + simpan raster evaluasi
    const vals = [];
    const heat = [];
    const zRaster = new Float32Array(w * h);

    // kita butuh iterasi ulang: density2d bisa di-loop lagi
    for (const p of density2d(mPoints, { bins, extent, bandwidth })) {
        const v = zmax ? p.z / zmax : 0;
        if (v > 0) vals.push(v);

        // simpan ke raster evaluasi
        let ix = Math.floor((p.x - minx) / cellX);
        let iy = Math.floor((p.y - miny) / cellY);
        if (ix < 0) ix = 0;
        else if (ix >= w) ix = w - 1;
        if (iy < 0) iy = 0;
        else if (iy >= h) iy = h - 1;
        const idx = iy * w + ix;
        if (v > zRaster[idx]) zRaster[idx] = v;
    }

    if (!vals.length) {
        heatLayer.setLatLngs([]);
        lastEvalGrid = null;
        return;
    }
    vals.sort((a, b) => a - b);

    // cutoff adaptif (sesuai kode kamu)
    const z = map.getZoom();
    let q = 0.75;
    if (z <= 12) q = 0.86;
    else if (z <= 14) q = 0.8;
    const qIdx = Math.floor(vals.length * q);
    const CUTOFF = Math.max(0.12, vals[qIdx] ?? 0.12);

    // konversi ke titik heat
    for (const p of density2d(mPoints, { bins, extent, bandwidth })) {
        const v = zmax ? p.z / zmax : 0;
        if (v < CUTOFF) continue;
        const [lon, lat] = metersToLngLat(p.x, p.y);
        const latNum = toNum(lat),
            lonNum = toNum(lon);
        if (!validLat(latNum) || !validLon(lonNum)) continue;
        heat.push([latNum, lonNum, v]);
    }

    heatLayer.setLatLngs(heat);

    // simpan grid buat evaluasi
    lastEvalGrid = { w, h, minx, miny, cellX, cellY, z: zRaster };
}

// ========================= Marker interaktif =========================
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

// ========================= Rekomendasi =========================
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

// ========================= HINT BANNER =========================
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

// ========================= Fetch data =========================
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

    // Render
    renderRekomendasi(jobs);
    startHintCarousel(jobs);
    applyHeatStyle();
    recomputeKDE();
    updateMarkers();
}

// ========================= DOM Ready =========================
document.addEventListener("DOMContentLoaded", async () => {
    try {
        // Map & layers
        map = L.map("map").setView([-7.3, 112.7], 12);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors",
        }).addTo(map);

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
            radius: 20, // awal; akan di-override applyHeatStyle()
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

        // ====== popup "Lihat detail" ======
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

        // ========================= Search & Suggestions =========================
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

        (function injectPAIButton() {
            // cari container tombol; kalau .toolbar tidak ada, sesuaikan selector-nya
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
                        alert("Tidak ada data lowongan untuk dievaluasi.");
                        return;
                    }

                    // Pastikan grid tampilan sudah dibangun sekali (biasanya sudah dipanggil di fetchData)
                    if (!lastEvalGrid) {
                        // recompute dari semua titik agar lastEvalGrid tidak null (opsional)
                        recomputeKDE();
                    }

                    // ===== split 70/30 =====
                    const shuffled = shuffleSeeded(jobs, 42);
                    const cut = Math.max(1, Math.floor(0.7 * shuffled.length));
                    const train = shuffled.slice(0, cut);
                    const test = shuffled.slice(cut);

                    if (!test.length) {
                        alert("Dataset terlalu kecil untuk split 70/30.");
                        return;
                    }

                    // ===== grid dari TRAIN (biar fair, tidak "mengintip" test) =====
                    const trainLngLat = train.map((j) => [j.lat, j.lon]);
                    const grid = computeDensityGridFor(trainLngLat);
                    if (!grid) {
                        alert("Gagal membangun grid dari data TRAIN.");
                        return;
                    }

                    // simpan & ganti sementara grid evaluasi
                    const prevGrid = lastEvalGrid;
                    lastEvalGrid = grid;

                    // ===== hitung PAI =====
                    const testPts = test.map((j) => ({
                        lat: j.lat,
                        lon: j.lon,
                    }));
                    const alphas = [0.01, 0.05, 0.1];
                    const out = evaluatePAI(testPts, alphas);

                    // restore grid tampilan
                    lastEvalGrid = prevGrid;

                    if (!out || !out.length) {
                        alert("Evaluasi PAI tidak menghasilkan nilai.");
                        return;
                    }

                    // tampilkan di console
                    console.table(
                        out.map((r) => ({
                            "Alpha %": r.alpha * 100,
                            "Top Cells": r.topCells,
                            Hits: r.hits,
                            Total: r.totalPts,
                            HR: r.HR.toFixed(3),
                            PAI: r.PAI.toFixed(2),
                        }))
                    );

                    // popup ringkas (pakai Swal kalau ada, fallback alert)
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
                        .join("<br>");
                    if (window.Swal) {
                        Swal.fire({
                            title: "Hasil PAI",
                            html: lines,
                            icon: "info",
                        });
                    } else {
                        alert(
                            out
                                .map(
                                    (r) =>
                                        `alpha ${(r.alpha * 100).toFixed(
                                            0
                                        )}%: HR ${r.HR.toFixed(
                                            3
                                        )}, PAI ${r.PAI.toFixed(2)}`
                                )
                                .join("\n")
                        );
                    }
                } catch (e) {
                    console.error("[PAI] error:", e);
                    alert("Gagal menjalankan evaluasi PAI. Cek konsol.");
                }
            });
        })();
    } catch (err) {
        console.error("❌ Error:", err);
        alert("Gagal memuat data heatmap. Cek konsol.");
    }
});
