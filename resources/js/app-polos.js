import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import 'leaflet.heat';
import { density2d } from 'fast-kde';

document.addEventListener('DOMContentLoaded', async () => {
  try {
    const res = await fetch('/api/hirings/heatmap');
    const rawData = await res.json();
    if (!Array.isArray(rawData) || rawData.length === 0) {
      throw new Error('Data tidak valid atau kosong');
    }

    // simpan array objek dengan lat/lon float
    const jobs = rawData.map(d => ({
      ...d,
      lat: parseFloat(d.latitude),
      lon: parseFloat(d.longitude)
    }));
    const locations = jobs.map(j => [j.lat, j.lon]);

    // ====== Leaflet map ======
    const map = L.map('map').setView([-7.3, 112.7], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // ====== helper lon/lat <-> Web Mercator meter ======
    const R = 20037508.34;
    const lngLatToMeters = (lon, lat) => {
      const x = (lon * R) / 180;
      const y = Math.log(Math.tan(((90 + lat) * Math.PI) / 360)) / (Math.PI / 180);
      return [x, (y * R) / 180];
    };
    const metersToLngLat = (x, y) => {
      const lon = (x / R) * 180;
      let lat = (y / R) * 180;
      lat = (180 / Math.PI) * (2 * Math.atan(Math.exp((lat * Math.PI) / 180)) - Math.PI / 2);
      return [lon, lat];
    };

    // data ke meter untuk KDE
    const mPoints = locations.map(([lat, lon]) => lngLatToMeters(lon, lat));

    // ====== heat layer (hasil KDE) ======
    const heatLayer = L.heatLayer([], {
      radius: 25,
      blur: 15,
      maxZoom: 17,
      minOpacity: 0.06
    }).addTo(map);

    // ====== KDE adaptif per zoom ======
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
        extent: [[minx - buf, maxx + buf], [miny - buf, maxy + buf]],
        bins: [binsX, binsY],
        bandwidth: [BAND_M, BAND_M]
      };
    }

    function recomputeKDE() {
      const { extent, bins, bandwidth } = getAdaptiveParams();
      const d2 = density2d(mPoints, { bins, extent, bandwidth });

      const pts = [];
      let zmax = 0;
      for (const p of d2) {
        if (p.z > zmax) zmax = p.z;
        pts.push(p);
      }

      const CUTOFF = 0.10;
      const heat = [];
      for (const p of pts) {
        const val = zmax ? (p.z / zmax) : 0;
        if (val < CUTOFF) continue;
        const [lon, lat] = metersToLngLat(p.x, p.y);
        heat.push([lat, lon, val]);
      }
      heatLayer.setLatLngs(heat);
    }

    // ====== marker interaktif ======
    const markerLayer = L.layerGroup().addTo(map);
    const MARKER_ZOOM_THRESHOLD = 15;

    function buildPopup(job) {
      return `
        <div style="min-width:220px">
          <div style="font-weight:600;font-size:14px;margin-bottom:4px">
            Lowongan #${job.id}
          </div>
          <div style="font-size:12px;color:#555">
            ${job.kota ?? ''}${job.provinsi ? ', ' + job.provinsi : ''}
          </div>
          <div style="margin-top:6px">
            <a href="#" class="lihat-detail" data-id="${job.id}">Lihat detail</a>
          </div>
        </div>
      `;
    }

    function updateMarkers() {
      const showMarkers = map.getZoom() >= MARKER_ZOOM_THRESHOLD;
      markerLayer.clearLayers();
      if (!showMarkers) return;

      const bounds = map.getBounds();
      jobs.filter(j => bounds.contains([j.lat, j.lon])).forEach(job => {
        const m = L.circleMarker([job.lat, job.lon], {
          radius: 6,
          weight: 1,
          opacity: 1,
          fillOpacity: 0.9
        });
        m.bindPopup(buildPopup(job));
        m.addTo(markerLayer);
      });
    }

    // klik link "Lihat detail" di popup → load detail ke panel kanan
    map.on('popupopen', (e) => {
      const link = e.popup.getElement().querySelector('.lihat-detail');
      if (link) {
        link.addEventListener('click', async (evt) => {
          evt.preventDefault();
          const id = link.dataset.id;
          try {
            const res = await fetch(`/api/hirings/${id}`);
            const detail = await res.json();
            renderJobDetail(detail);
          } catch (err) {
            console.error(err);
            document.getElementById('job-detail').innerHTML =
              '<div class="text-danger">Gagal memuat detail lowongan</div>';
          }
        });
      }
    });

    function renderJobDetail(detail) {
      document.getElementById('job-detail').innerHTML = `
        <h6 class="font-weight-bold">${detail.position_hiring ?? 'Lowongan'}</h6>
        <p><b>Perusahaan:</b> ${detail.personal_company?.name ?? '-'}</p>
        <p><b>Alamat:</b> ${detail.address_hiring ?? '-'}</p>
        <p><b>Sistem Kerja:</b> ${detail.work_system ?? '-'}</p>
        <p><b>Gaji:</b> ${detail.gaji_min ?? ''} - ${detail.gaji_max ?? ''}</p>
        <p><b>Deskripsi:</b><br>${detail.description_hiring ?? ''}</p>
      `;
    }

    // init pertama
    recomputeKDE();
    updateMarkers();

    map.on('zoomend moveend', () => {
      recomputeKDE();
      updateMarkers();
    });

  } catch (err) {
    console.error('❌ Error:', err);
    alert('Gagal memuat data heatmap. Cek konsol.');
  }
});
