@extends('admin.master_admin')
@section('title', 'Tambah Data Pengguna | CVRE GENERATE')

@section('content')
<div class="container-fluid">
    <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-body">
                <h4 class="mt-4 mb-4 text-center fw-bold text-primary">Form Tambah Data Pengguna</h4>

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="row g-3">

                        <!-- Role -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-control form-control-md" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="pelamar" {{ old('role') == 'pelamar' ? 'selected' : '' }}>Pelamar</option>
                                    <option value="perusahaan" {{ old('role') == 'perusahaan' ? 'selected' : '' }}>Perusahaan</option>
                                </select>
                                @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Nama -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-md" name="name" id="name"
                                    value="{{ old('name') }}" placeholder="Masukkan Nama" required>
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-md" name="email" id="email"
                                    value="{{ old('email') }}" placeholder="Masukkan Email" required>
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Tambahan untuk PELAMAR -->
                        <div id="pelamarFields" class="d-none col-12">
                            <div class="form-group">
                                <label for="gender" class="form-label">Jenis Kelamin</label>
                                <select id="gender" name="gender" class="form-control form-control-md">
                                    <option value="">-- Pilih --</option>
                                    <option value="laki-laki" {{ old('gender') == 'laki-laki' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="perempuan" {{ old('gender') == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="date_of_birth_pelamar" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control form-control-md" name="date_of_birth_pelamar"
                                    id="date_of_birth_pelamar" value="{{ old('date_of_birth_pelamar') }}">
                            </div>
                            <div class="form-group">
                                <label for="city_pelamar" class="form-label">Kota Domisili</label>
                                <input type="text" class="form-control form-control-md" name="city_pelamar" id="city_pelamar"
                                    value="{{ old('city_pelamar') }}" placeholder="Masukkan Kota Domisili">
                            </div>
                            <div class="form-group">
                                <label for="phone_pelamar" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control form-control-md" name="phone_pelamar" id="phone_pelamar"
                                    value="{{ old('phone_pelamar') }}" placeholder="Masukkan Nomor Telepon">
                            </div>
                        </div>

                        <!-- Tambahan untuk PERUSAHAAN -->
                        <div id="perusahaanFields" class="d-none col-12">
                            <div class="form-group">
                                <label for="name_user_company" class="form-label">Nama Pengguna Perusahaan</label>
                                <input type="text" class="form-control form-control-md" name="name_user_company"
                                    id="name_user_company" value="{{ old('name_user_company') }}" placeholder="Masukkan Nama Pengguna Perusahaan">
                            </div>
                            <div class="form-group">
                                <label for="type_of_company" class="form-label">Jenis Perusahaan</label>
                                <select name="type_of_company" id="type_of_company" class="form-control form-control-md">
                                    <option value="">-- Pilih Jenis Perusahaan --</option>
                                    <option value="Teknologi Informasi & Komunikasi" {{ old('type_of_company') == 'Teknologi Informasi & Komunikasi' ? 'selected' : '' }}>Teknologi Informasi & Komunikasi</option>
                                    <option value="Software & Networking" {{ old('type_of_company') == 'Software & Networking' ? 'selected' : '' }}>Software & Networking</option>
                                    <option value="Keuangan & Perbankan" {{ old('type_of_company') == 'Keuangan & Perbankan' ? 'selected' : '' }}>Keuangan & Perbankan</option>
                                    <option value="Asuransi" {{ old('type_of_company') == 'Asuransi' ? 'selected' : '' }}>Asuransi</option>
                                    <option value="Kesehatan & Farmasi" {{ old('type_of_company') == 'Kesehatan & Farmasi' ? 'selected' : '' }}>Kesehatan & Farmasi</option>
                                    <option value="Pendidikan & Pelatihan" {{ old('type_of_company') == 'Pendidikan & Pelatihan' ? 'selected' : '' }}>Pendidikan & Pelatihan</option>
                                    <option value="Manufaktur & Produksi" {{ old('type_of_company') == 'Manufaktur & Produksi' ? 'selected' : '' }}>Manufaktur & Produksi</option>
                                    <option value="Konstruksi & Properti" {{ old('type_of_company') == 'Konstruksi & Properti' ? 'selected' : '' }}>Konstruksi & Properti</option>
                                    <option value="Energi & Pertambangan" {{ old('type_of_company') == 'Energi & Pertambangan' ? 'selected' : '' }}>Energi & Pertambangan</option>
                                    <option value="Transportasi & Logistik" {{ old('type_of_company') == 'Transportasi & Logistik' ? 'selected' : '' }}>Transportasi & Logistik</option>
                                    <option value="Pariwisata, Hotel & Restoran" {{ old('type_of_company') == 'Pariwisata, Hotel & Restoran' ? 'selected' : '' }}>Pariwisata, Hotel & Restoran</option>
                                    <option value="Media, Kreatif & Hiburan" {{ old('type_of_company') == 'Media, Kreatif & Hiburan' ? 'selected' : '' }}>Media, Kreatif & Hiburan</option>
                                    <option value="Ritel & Perdagangan" {{ old('type_of_company') == 'Ritel & Perdagangan' ? 'selected' : '' }}>Ritel & Perdagangan</option>
                                    <option value="Pertanian, Perikanan & Kehutanan" {{ old('type_of_company') == 'Pertanian, Perikanan & Kehutanan' ? 'selected' : '' }}>Pertanian, Perikanan & Kehutanan</option>
                                    <option value="Telekomunikasi" {{ old('type_of_company') == 'Telekomunikasi' ? 'selected' : '' }}>Telekomunikasi</option>
                                    <option value="Konsultan & Layanan Profesional" {{ old('type_of_company') == 'Konsultan & Layanan Profesional' ? 'selected' : '' }}>Konsultan & Layanan Profesional</option>
                                    <option value="Pemerintahan & Lembaga Publik" {{ old('type_of_company') == 'Pemerintahan & Lembaga Publik' ? 'selected' : '' }}>Pemerintahan & Lembaga Publik</option>
                                    <option value="Organisasi Nirlaba & Sosial" {{ old('type_of_company') == 'Organisasi Nirlaba & Sosial' ? 'selected' : '' }}>Organisasi Nirlaba & Sosial</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="city_company" class="form-label">Kota Domisili Perusahaan</label>
                                <input type="text" class="form-control form-control-md" name="city_company" id="city_company"
                                    value="{{ old('city_company') }}" placeholder="Masukkan Kota Domisili Perusahaan">
                            </div>
                            <div class="form-group">
                                <label for="phone_company" class="form-label">Nomor Telepon Perusahaan</label>
                                <input type="text" class="form-control form-control-md" name="phone_company" id="phone_company"
                                    value="{{ old('phone_company') }}" placeholder="Masukkan Nomor Telepon Perusahaan">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control form-control-md" name="password" id="password"
                                    placeholder="Masukkan Password" required>
                            </div>
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control form-control-md" name="password_confirmation"
                                    id="password_confirmation" placeholder="Ulangi Password" required>
                            </div>
                        </div>

                    </div>

                    <div class="text-center">
                        <button class="mt-4 btn btn-primary btn-md px-3" type="submit">
                            <i class="fas fa-user-plus me-2"></i> Tambah Data Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const roleSelect = document.getElementById("role");
    const pelamarFields = document.getElementById("pelamarFields");
    const perusahaanFields = document.getElementById("perusahaanFields");

    function toggleRoleFields() {
        const role = roleSelect.value;

        pelamarFields.classList.add("d-none");
        perusahaanFields.classList.add("d-none");

        if (role === "pelamar") pelamarFields.classList.remove("d-none");
        if (role === "perusahaan") perusahaanFields.classList.remove("d-none");
    }

    roleSelect.addEventListener("change", toggleRoleFields);

    document.addEventListener("DOMContentLoaded", toggleRoleFields);

    // Enable semua field sebelum submit agar tidak ada field yg hilang
    document.querySelector("form").addEventListener("submit", function() {
        pelamarFields.querySelectorAll("input, select").forEach(el => el.disabled = false);
        perusahaanFields.querySelectorAll("input, select").forEach(el => el.disabled = false);
    });
</script>
@endpush
