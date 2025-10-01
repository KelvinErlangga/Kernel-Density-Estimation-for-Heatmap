@extends('admin.master_admin')
@section('title', 'Ubah Data Pengguna | CVRE GENERATE')

@section('content')
<div class="container-fluid">
    <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-body">
                <h4 class="mt-4 mb-4 text-center fw-bold text-primary">Form Ubah Data Pengguna</h4>

                <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">

                        <!-- Role -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-control form-control-md" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="admin" {{ old('role', $user->roles->first()?->name) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="pelamar" {{ old('role', $user->roles->first()?->name) == 'pelamar' ? 'selected' : '' }}>Pelamar</option>
                                    <option value="perusahaan" {{ old('role', $user->roles->first()?->name) == 'perusahaan' ? 'selected' : '' }}>Perusahaan</option>
                                </select>
                                @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Nama -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-md" name="name" id="name"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-md" name="email" id="email"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <!-- Tambahan untuk PELAMAR -->
                        <div id="pelamarFields" class="d-none col-12">
                            @php
                                $pelamar = $user->personalPelamar;
                            @endphp
                            <div class="form-group">
                                <label for="gender" class="form-label">Jenis Kelamin</label>
                                <select id="gender" name="gender" class="form-control form-control-md">
                                    <option value="">-- Pilih --</option>
                                    <option value="laki-laki" {{ old('gender', $pelamar?->gender) == 'laki-laki' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="perempuan" {{ old('gender', $pelamar?->gender) == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="date_of_birth_pelamar" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control form-control-md" name="date_of_birth_pelamar"
                                    id="date_of_birth_pelamar" value="{{ old('date_of_birth_pelamar', $pelamar?->date_of_birth_pelamar) }}">
                            </div>
                            <div class="form-group">
                                <label for="city_pelamar" class="form-label">Kota Domisili</label>
                                <input type="text" class="form-control form-control-md" name="city_pelamar" id="city_pelamar"
                                    value="{{ old('city_pelamar', $pelamar?->city_pelamar) }}">
                            </div>
                            <div class="form-group">
                                <label for="phone_pelamar" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control form-control-md" name="phone_pelamar" id="phone_pelamar"
                                    value="{{ old('phone_pelamar', $pelamar?->phone_pelamar) }}">
                            </div>
                        </div>

                        <!-- Tambahan untuk PERUSAHAAN -->
                        <div id="perusahaanFields" class="d-none col-12">
                            @php
                                $company = $user->personalCompany;
                            @endphp
                            <div class="form-group">
                                <label for="name_user_company" class="form-label">Nama Pengguna Perusahaan</label>
                                <input type="text" class="form-control form-control-md" name="name_user_company"
                                    id="name_user_company" value="{{ old('name_user_company', $company?->name_user_company) }}">
                            </div>
                            <div class="form-group">
                                <label for="type_of_company" class="form-label">Jenis Perusahaan</label>
                                <select name="type_of_company" id="type_of_company" class="form-control form-control-md">
                                    <option value="">-- Pilih Jenis Perusahaan --</option>
                                    @php
                                        $types = [
                                            "Teknologi Informasi & Komunikasi",
                                            "Software & Networking",
                                            "Keuangan & Perbankan",
                                            "Asuransi",
                                            "Kesehatan & Farmasi",
                                            "Pendidikan & Pelatihan",
                                            "Manufaktur & Produksi",
                                            "Konstruksi & Properti",
                                            "Energi & Pertambangan",
                                            "Transportasi & Logistik",
                                            "Pariwisata, Hotel & Restoran",
                                            "Media, Kreatif & Hiburan",
                                            "Ritel & Perdagangan",
                                            "Pertanian, Perikanan & Kehutanan",
                                            "Telekomunikasi",
                                            "Konsultan & Layanan Profesional",
                                            "Pemerintahan & Lembaga Publik",
                                            "Organisasi Nirlaba & Sosial"
                                        ];
                                    @endphp
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" {{ old('type_of_company', $company?->type_of_company) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="city_company" class="form-label">Kota Domisili Perusahaan</label>
                                <input type="text" class="form-control form-control-md" name="city_company" id="city_company"
                                    value="{{ old('city_company', $company?->city_company) }}">
                            </div>
                            <div class="form-group">
                                <label for="phone_company" class="form-label">Nomor Telepon Perusahaan</label>
                                <input type="text" class="form-control form-control-md" name="phone_company" id="phone_company"
                                    value="{{ old('phone_company', $company?->phone_company) }}">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="password" class="form-label">Password (kosongkan jika tidak ingin mengubah)</label>
                                <input type="password" class="form-control form-control-md" name="password" id="password"
                                    placeholder="Masukkan Password baru">
                            </div>
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control form-control-md" name="password_confirmation"
                                    placeholder="Ulangi Password baru">
                            </div>
                        </div>

                    </div>

                    <div class="text-center">
                        <button class="mt-4 btn btn-primary btn-md px-3" type="submit">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
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

        if(role === "pelamar") pelamarFields.classList.remove("d-none");
        else if(role === "perusahaan") perusahaanFields.classList.remove("d-none");
    }

    roleSelect.addEventListener("change", toggleRoleFields);
    document.addEventListener("DOMContentLoaded", toggleRoleFields);
</script>
@endpush
