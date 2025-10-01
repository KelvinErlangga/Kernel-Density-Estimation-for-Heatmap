import L from 'leaflet';
import 'leaflet.heat';
import "leaflet/dist/leaflet.css";

document.addEventListener('DOMContentLoaded', async () => {
  try {
    const API_BASE = window.location.origin;
    const res = await fetch(`${API_BASE}/api/lokasi-lowongan`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    const raw = await res.json();
    if (!Array.isArray(raw) || raw.length === 0) throw new Error('Data kosong');

    // Normalisasi lat/lon
    const jobs = raw.map(d => ({
      ...d,
      lat: d.latitude ? parseFloat(d.latitude) : NaN,
      lon: d.longitude ? parseFloat(d.longitude) : NaN,
    })).filter(j => Number.isFinite(j.lat) && Number.isFinite(j.lon));

    if (jobs.length === 0) throw new Error('Semua koordinat tidak valid');

    const heatPoints = jobs.map(j => [j.lat, j.lon, 1]); // bobot = 1

    // Init peta
    const map = L.map('map').setView([-7.3, 112.7], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Heatmap polos
    L.heatLayer(heatPoints, {
      radius: 25,
      blur: 15,
      maxZoom: 17,
    }).addTo(map);

    // Layer marker
    const markerLayer = L.layerGroup().addTo(map);
    const MARKER_ZOOM_THRESHOLD = 15;

    function formatRupiah(num) {
      if (num == null) return '-';
      try {
        return new Intl.NumberFormat('id-ID', {
          style:'currency', currency:'IDR', maximumFractionDigits:0
        }).format(Number(num));
      } catch { return String(num); }
    }

    function buildPopup(job){
      return `
        <div style="min-width:220px">
          <div style="font-weight:600;font-size:14px;margin-bottom:4px">
            ${job.position_hiring ?? job.role_lowongan ?? 'Lowongan'}
          </div>
          <div style="font-size:12px;color:#555">
            ${job.jenis_pekerjaan ?? '-'} · ${job.kota ?? ''}${job.provinsi ? ', '+job.provinsi : ''}
          </div>
          <div style="font-size:12px;margin:8px 0">
            ${(job.description_hiring ?? job.deskripsi_lowongan ?? '').toString().slice(0,140)}…
          </div>
          <div style="font-size:12px"><b>Gaji</b>: ${
            job.gaji_min || job.gaji_max
              ? `${formatRupiah(job.gaji_min)} – ${formatRupiah(job.gaji_max)}`
              : formatRupiah(job.gaji_per_bulan)
          }</div>
          <div style="font-size:12px;margin-top:6px"><b>Kualifikasi</b>: ${(job.kualifikasi ?? '').toString().slice(0,120)}…</div>
        </div>
      `;
    }

    function updateMarkers() {
      markerLayer.clearLayers();
      const show = map.getZoom() >= MARKER_ZOOM_THRESHOLD;
      if (!show) return;

      const bounds = map.getBounds();
      jobs.filter(j => bounds.contains([j.lat, j.lon])).forEach(job => {
        L.circleMarker([job.lat, job.lon], {
          radius: 6, weight:1, opacity:1, fillOpacity:0.9
        }).bindPopup(buildPopup(job)).addTo(markerLayer);
      });
    }

    updateMarkers();
    map.on('zoomend moveend', updateMarkers);

  } catch (e) {
    console.error('❌ Error:', e);
    alert('Gagal memuat data heatmap. Cek konsol.');
  }
});
