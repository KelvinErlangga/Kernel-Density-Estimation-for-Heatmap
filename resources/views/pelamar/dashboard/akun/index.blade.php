@extends('pelamar.dashboard.master_user')
@section('title', 'Akun | CVRE GENERATE')

@push('style')
<style>
    .addr-suggest {
        position: absolute;
        z-index: 1050;
        width: 100%;
        max-height: 220px;
        overflow-y: auto;
        display: none;
    }
    .addr-suggest .list-group-item { cursor: pointer; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h5 class="mb-0 font-weight-bold text-primary">Dashboard / Pengaturan / <span class="text-dark">Akun</span></h5>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-body m-10">
                    <div class="row">
                        <!-- Sidebar -->
                        <div class="col-md-4 text-center">
                            <img src="{{asset('assets/akun/profil.png')}}" alt="Profile Picture"
                                 class="img-fluid rounded-circle mb-3 mx-auto d-block" style="width: 150px" />
                            <h5 class="font-weight-bold mb-2">{{Auth::user()->name}}</h5>
                            <ul class="nav flex-column mt-8">
                                <li class="nav-item"><a class="nav-link active text-primary font-weight-bold m-3" href="#">Akun</a><hr /></li>
                                <li class="nav-item"><a class="nav-link text-secondary m-3" href="#">Aktivitas</a><hr /></li>
                                <li class="nav-item"><a class="nav-link text-secondary m-3" href="#">Lainnya</a><hr /></li>
                            </ul>
                        </div>

                        <!-- Form Edit Akun -->
                        <div class="col-md-8">
                            <div class="form-container" style="border: 1px solid; padding: 50px; border-radius: 8px">
                                <h4 class="mb-4 text-center font-weight-bold" style="font-size: 1.725rem; color: black">Akun</h4>

                                <form action="{{ route('pelamar.updateProfile') }}" method="POST" id="akunForm" autocomplete="off">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group">
                                        <label for="name">Nama</label>
                                        <input type="text" class="form-control editable" id="name" name="name_pelamar"
                                            value="{{ Auth::user()->personalPelamar->name_pelamar ?? '' }}" readonly />
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">Nomor Handphone</label>
                                        <input type="text" class="form-control editable" id="phone" name="phone_pelamar"
                                            value="{{ Auth::user()->personalPelamar->phone_pelamar ?? '' }}" readonly />
                                    </div>

                                    <div class="form-group">
                                        <label for="city">Kota</label>
                                        <input type="text" class="form-control editable" id="city" name="city_pelamar"
                                            value="{{ Auth::user()->personalPelamar->city_pelamar ?? '' }}" readonly />
                                    </div>

                                    {{-- Alamat Domisili + Geocode --}}
                                    @php
                                        $latInit = Auth::user()->personalPelamar->latitude ?? '';
                                        $lonInit = Auth::user()->personalPelamar->longitude ?? '';
                                    @endphp
                                    <div class="form-group position-relative">
                                        <label for="alamat_domisili">Alamat Domisili (lengkap)</label>
                                        <input type="text"
                                               class="form-control editable"
                                               id="alamat_domisili"
                                               name="alamat_domisili"
                                               value="{{ Auth::user()->personalPelamar->alamat_domisili ?? '' }}"
                                               placeholder="Contoh: Jalan Raya Darmo No. 1, Wonokromo, Surabaya"
                                               readonly />

                                        <ul id="alamat_suggestions" class="list-group addr-suggest"></ul>

                                        <div class="d-flex align-items-center gap-2 mt-2">
                                            <button type="button" id="btn-geocode" class="btn btn-outline-primary btn-sm" style="display:none;">
                                                Deteksi Lokasi
                                            </button>
                                            <small id="geo-preview" class="text-muted ml-2">
                                                {{ ($latInit && $lonInit) ? "Koordinat: $latInit, $lonInit" : "Koordinat belum terdeteksi" }}
                                            </small>
                                        </div>

                                        <input type="hidden" id="latitude"  name="latitude"  value="{{ $latInit }}">
                                        <input type="hidden" id="longitude" name="longitude" value="{{ $lonInit }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="dob">Tanggal Lahir</label>
                                        <input type="date" class="form-control editable" id="dob" name="date_of_birth_pelamar"
                                            value="{{ Auth::user()->personalPelamar->date_of_birth_pelamar ?? '' }}" readonly />
                                    </div>

                                    <div class="form-group">
                                        <label for="gender">Jenis Kelamin</label>
                                        <select class="form-control editable" id="gender" name="gender" disabled>
                                            <option value="laki-laki" {{ (Auth::user()->personalPelamar->gender ?? '') == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="perempuan" {{ (Auth::user()->personalPelamar->gender ?? '') == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>

                                    <div class="text-center mt-4">
                                        <button type="button" id="editBtn" class="btn btn-primary">Edit</button>
                                        <button type="button" id="cancelBtn" class="btn btn-secondary" style="display:none;">Batal</button>
                                        <button type="submit" id="saveBtn" class="btn btn-success" style="display:none;">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- End Form -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const editBtn   = document.getElementById("editBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    const saveBtn   = document.getElementById("saveBtn");
    const form      = document.getElementById("akunForm");
    const inputs    = form.querySelectorAll(".editable");

    // Field alamat & geocode elements
    const inputAlamat = document.getElementById('alamat_domisili');
    const suggList    = document.getElementById('alamat_suggestions');
    const btnGeocode  = document.getElementById('btn-geocode');
    const latEl       = document.getElementById('latitude');
    const lonEl       = document.getElementById('longitude');
    const geoPreview  = document.getElementById('geo-preview');

    // Simpan data awal (editable)
    let initialData = {};
    inputs.forEach(input => { initialData[input.id] = input.value; });
    // Simpan koordinat awal untuk keperluan reset
    const latInit = latEl.value;
    const lonInit = lonEl.value;

    // ====== Tombol Edit ======
    editBtn.addEventListener("click", function() {
        inputs.forEach(field => field.removeAttribute("readonly"));
        document.getElementById("gender").removeAttribute("disabled");
        editBtn.style.display   = "none";
        cancelBtn.style.display = "inline-block";
        saveBtn.style.display   = "inline-block";
        // tampilkan tombol geocode saat mode edit
        btnGeocode.style.display = 'inline-block';
    });

    // ====== Tombol Batal ======
    function hideAddrSuggest(){ if(suggList){ suggList.style.display='none'; suggList.innerHTML=''; } }
    cancelBtn.addEventListener("click", function() {
        // reset nilai text inputs
        inputs.forEach(field => {
            field.value = initialData[field.id];
            field.setAttribute("readonly", true);
        });
        // reset koordinat dan preview
        latEl.value = latInit;
        lonEl.value = lonInit;
        geoPreview.textContent = (latInit && lonInit) ? `Koordinat: ${latInit}, ${lonInit}` : 'Koordinat belum terdeteksi';

        document.getElementById("gender").setAttribute("disabled", true);
        editBtn.style.display   = "inline-block";
        cancelBtn.style.display = "none";
        saveBtn.style.display   = "none";

        btnGeocode.style.display = 'none';
        hideAddrSuggest();
    });

    // ====== Geocode – saran alamat & deteksi ======
    let t = null;
    const debounce = (fn, ms=350) => (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };

    if (inputAlamat) {
        inputAlamat.addEventListener('input', debounce(async () => {
            const q = inputAlamat.value.trim();
            if (q.length < 4) { hideAddrSuggest(); return; }
            await fetchAddrSuggest(q);
        }, 350));

        inputAlamat.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && suggList && suggList.children.length) {
                e.preventDefault();
                suggList.children[0].click();
            }
        });

        inputAlamat.addEventListener('blur', ()=> setTimeout(hideAddrSuggest,120));
    }

    document.addEventListener('click', (e)=>{
        if (!suggList) return;
        if (!suggList.contains(e.target) && e.target !== inputAlamat) hideAddrSuggest();
    });

    async function fetchAddrSuggest(q) {
        try {
            const url = `https://nominatim.openstreetmap.org/search?format=json&limit=5&addressdetails=1&countrycodes=id&q=${encodeURIComponent(q)}`;
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'User-Agent': 'CVRE-GENERATE/1.0 (contact: admin@example.com)'
                }
            });
            const data = await res.json();
            renderAddrSuggest(Array.isArray(data) ? data : []);
        } catch (err) {
            console.error('Geocode suggest error:', err);
            hideAddrSuggest();
        }
    }

    function renderAddrSuggest(items) {
        if (!items.length) { hideAddrSuggest(); return; }
        suggList.innerHTML = '';
        items.forEach(row => {
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.textContent = row.display_name;
            li.addEventListener('click', () => {
                inputAlamat.value = row.display_name;
                latEl.value = row.lat ?? '';
                lonEl.value = row.lon ?? '';
                geoPreview.textContent = (row.lat && row.lon)
                    ? `Koordinat: ${row.lat}, ${row.lon}`
                    : 'Koordinat belum terdeteksi';
                hideAddrSuggest();
            });
            suggList.appendChild(li);
        });
        suggList.style.display = 'block';
    }

    if (btnGeocode) {
        btnGeocode.addEventListener('click', async () => {
            const q = inputAlamat.value.trim();
            if (!q) return Swal.fire('Info','Isi alamat terlebih dahulu.','info');
            try {
                const url = `https://nominatim.openstreetmap.org/search?format=json&limit=1&addressdetails=1&countrycodes=id&q=${encodeURIComponent(q)}`;
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'User-Agent': 'CVRE-GENERATE/1.0 (contact: admin@example.com)'
                    }
                });
                const data = await res.json();
                if (Array.isArray(data) && data.length) {
                    latEl.value = data[0].lat;
                    lonEl.value = data[0].lon;
                    geoPreview.textContent = `Koordinat: ${data[0].lat}, ${data[0].lon}`;
                    Swal.fire('Berhasil','Koordinat alamat terdeteksi.','success');
                } else {
                    latEl.value = '';
                    lonEl.value = '';
                    geoPreview.textContent = 'Koordinat belum terdeteksi';
                    Swal.fire('Tidak ditemukan','Alamat tidak dapat dideteksi. Coba lebih spesifik.','warning');
                }
            } catch (e) {
                console.error(e);
                Swal.fire('Gagal','Terjadi gangguan saat mendeteksi lokasi.','error');
            }
        });
    }

    // ====== Submit – auto-geocode jika perlu, lalu submit ======
    form.addEventListener('submit', async (e) => {
        // jika ada alamat tetapi koordinat kosong, coba geocode sekali
        if (inputAlamat.value.trim() && (!latEl.value || !lonEl.value)) {
            e.preventDefault();
            try {
                const url = `https://nominatim.openstreetmap.org/search?format=json&limit=1&addressdetails=1&countrycodes=id&q=${encodeURIComponent(inputAlamat.value.trim())}`;
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'User-Agent': 'CVRE-GENERATE/1.0 (contact: admin@example.com)'
                    }
                });
                const data = await res.json();
                if (Array.isArray(data) && data.length) {
                    latEl.value = data[0].lat;
                    lonEl.value = data[0].lon;
                    geoPreview.textContent = `Koordinat: ${data[0].lat}, ${data[0].lon}`;
                }
            } catch (_) { /* abaikan kegagalan geocode */ }
            // lanjut submit setelah geocode cepat
            form.submit();
        }
    });

    // Notifikasi sukses dari backend
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#3085d6',
        });
    @endif
});
</script>
@endpush
