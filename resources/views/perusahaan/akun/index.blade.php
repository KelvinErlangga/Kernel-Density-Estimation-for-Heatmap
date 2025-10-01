@extends('perusahaan.master_perusahaan')
@section('title', 'Akun Perusahaan | CVRE GENERATE')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h5 class="mb-0 font-weight-bold text-primary">Dashboard / Pengaturan / <span class="text-dark">Akun Perusahaan</span></h5>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-body m-10">
                    <div class="row">
                        <!-- Sidebar -->
                        <div class="col-md-4 text-center">

                            <!-- Logo Perusahaan -->
                            <form action="{{ route('perusahaan.uploadLogo') }}" method="POST" enctype="multipart/form-data" id="logoForm">
                                @csrf
                                <input type="file" name="logo" id="logoInput" accept="image/*" style="display: none;" required>

                                @if(Auth::user()->personalCompany->logo ?? false)
                                    <img src="{{ asset('storage/company_logo/' . Auth::user()->personalCompany->logo) }}"
                                        alt="Logo {{ Auth::user()->personalCompany->name_company }}"
                                        class="mb-3 mx-auto d-block"
                                        id="logoPreview"
                                        style="width: 150px; height: 150px; object-fit: contain; border: 1px solid #ccc; cursor: pointer;">
                                @else
                                    <div class="mb-3 mx-auto"
                                        id="logoPreview"
                                        style="width: 150px; height: 150px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                        <span class="text-muted">No Logo</span>
                                    </div>
                                @endif

                                <button type="submit" class="btn btn-sm btn-primary mt-2">Upload Logo</button>
                            </form>

                            <h5 class="font-weight-bold mb-2 mt-3">{{ Auth::user()->name }}</h5>
                            <ul class="nav flex-column mt-4">
                                <li class="nav-item"><a class="nav-link active text-primary font-weight-bold m-3" href="#">Akun</a><hr /></li>
                                {{-- <li class="nav-item"><a class="nav-link text-secondary m-3" href="#">Lowongan</a><hr /></li>
                                <li class="nav-item"><a class="nav-link text-secondary m-3" href="#">Lainnya</a><hr /></li> --}}
                            </ul>
                        </div>

                        <!-- Form Edit Akun Perusahaan -->
                        <div class="col-md-8">
                            <div class="form-container" style="border: 1px solid; padding: 50px; border-radius: 8px">
                                <h4 class="mb-4 text-center font-weight-bold" style="font-size: 1.725rem; color: black">Akun Perusahaan</h4>

                                <form action="{{ route('perusahaan.updateProfile') }}" method="POST" id="companyForm">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group">
                                        <label for="name_company">Nama Perusahaan</label>
                                        <input type="text" class="form-control editable" id="name_company" name="name_company"
                                            value="{{ Auth::user()->personalCompany->name_company ?? '' }}" readonly />
                                    </div>

                                    <div class="form-group">
                                        <label for="phone_company">Nomor Telepon</label>
                                        <input type="text" class="form-control editable" id="phone_company" name="phone_company"
                                            value="{{ Auth::user()->personalCompany->phone_company ?? '' }}" readonly />
                                    </div>

                                    <div class="form-group">
                                        <label for="city_company">Kota</label>
                                        <input type="text" class="form-control editable" id="city_company" name="city_company"
                                            value="{{ Auth::user()->personalCompany->city_company ?? '' }}" readonly />
                                    </div>

                                    <div class="form-group">
                                        <label for="type_of_company">Jenis Perusahaan</label>
                                        <input type="text" class="form-control editable" id="type_of_company" name="type_of_company"
                                            value="{{ Auth::user()->personalCompany->type_of_company ?? '' }}" readonly />
                                    </div>

                                    <div class="form-group">
                                        <label for="description_company">Deskripsi</label>
                                        <textarea class="form-control editable" id="description_company" name="description_company" rows="3" readonly>{{ Auth::user()->personalCompany->description_company ?? '' }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="jumlah_karyawan">Jumlah Karyawan</label>
                                        <input type="number" class="form-control editable" id="jumlah_karyawan" name="jumlah_karyawan"
                                            value="{{ Auth::user()->personalCompany->jumlah_karyawan ?? '' }}" readonly />
                                    </div>

                                    <div class="form-group">
                                        <label for="jumlah_divisi">Jumlah Divisi</label>
                                        <input type="number" class="form-control editable" id="jumlah_divisi" name="jumlah_divisi"
                                            value="{{ Auth::user()->personalCompany->jumlah_divisi ?? '' }}" readonly />
                                    </div>

                                    <div class="form-group">
                                        <label for="tahun_berdiri">Tahun Berdiri</label>
                                        <input type="number" class="form-control editable" id="tahun_berdiri" name="tahun_berdiri"
                                            value="{{ Auth::user()->personalCompany->tahun_berdiri ?? '' }}" readonly />
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
    const editBtn = document.getElementById("editBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    const saveBtn = document.getElementById("saveBtn");
    const form = document.getElementById("companyForm");
    const inputs = form.querySelectorAll(".editable");

    // Simpan data awal
    let initialData = {};
    inputs.forEach(input => {
        initialData[input.id] = input.value;
    });

    // Edit
    editBtn.addEventListener("click", function() {
        inputs.forEach(field => field.removeAttribute("readonly"));
        editBtn.style.display = "none";
        cancelBtn.style.display = "inline-block";
        saveBtn.style.display = "inline-block";
    });

    // Batal
    cancelBtn.addEventListener("click", function() {
        inputs.forEach(field => {
            field.value = initialData[field.id];
            field.setAttribute("readonly", true);
        });
        editBtn.style.display = "inline-block";
        cancelBtn.style.display = "none";
        saveBtn.style.display = "none";
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

document.addEventListener('DOMContentLoaded', function() {
    const logoInput = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');

    // Klik frame / gambar untuk memilih file
    logoPreview.addEventListener('click', function() {
        logoInput.click();
    });

    // Preview logo setelah pilih file
    logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if(file){
            const reader = new FileReader();
            reader.onload = function(e){
                if(logoPreview.tagName === 'IMG'){
                    logoPreview.src = e.target.result;
                } else {
                    logoPreview.innerHTML = '';
                    logoPreview.style.backgroundImage = `url(${e.target.result})`;
                    logoPreview.style.backgroundSize = 'contain';
                    logoPreview.style.backgroundRepeat = 'no-repeat';
                    logoPreview.style.backgroundPosition = 'center';
                }
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
