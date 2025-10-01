@extends('pelamar.dashboard.master_user')
@section('title', 'Info Lowongan | CVRE GENERATE')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Panel kiri: Lowongan Lainnya -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 text-center">
                    <h6 class="m-0 font-weight-bold text-dark">LOWONGAN LAINNYA</h6>
                </div>
                <div class="card-body" style="max-height:800px; overflow-y:auto;">
                    @forelse($hirings as $hiring)
                        <div class="card mb-3 border" style="cursor:pointer;" onclick="showJobDetail('{{ $hiring->id }}')">
                            <div class="d-flex p-3">
                                <img src="{{ $hiring->personalCompany && $hiring->personalCompany->logo ? asset('storage/company_logo/' .$hiring->personalCompany->logo) : asset('images/default-company.png') }}"
                                alt="Logo {{ $hiring->personalCompany->name_company ?? 'Perusahaan' }}"
                                style="width:85px;height:85px;object-fit:contain;border-radius:6px;
                                        border:1px solid #ccc;background:#f5f5f5;" class="mr-3">
                                <div>
                                    <h6 class="font-weight-bold mb-1">{{ $hiring->position_hiring }}</h6>
                                    <small class="d-block text-muted">{{ $hiring->personalCompany->name_company ?? '-' }}</small>
                                    <small class="d-block">{{ $hiring->kota ?? '' }}{{ $hiring->provinsi ? ', '.$hiring->provinsi : '' }}</small>
                                    <small class="d-block text-dark">Rp {{ number_format($hiring->gaji_min,0,',','.') }} - Rp {{ number_format($hiring->gaji_max,0,',','.') }}/Bulan</small>
                                    <small class="text-muted">Diposting {{ $hiring->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Tidak ada lowongan</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Panel kanan: Informasi Lowongan -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 text-center">
                    <h6 class="m-0 font-weight-bold text-dark">INFORMASI PERUSAHAAN & LOWONGAN</h6>
                </div>
                <div class="card-body" id="job-detail">
                    <div class="text-center text-muted">Silakan pilih lowongan untuk melihat detail.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lamaran -->
<div class="modal fade" id="applicationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kirim Lamaran</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('pelamar.dashboard.lowongan.kirim_lamaran') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="hiring_id" name="hiring_id">
                    <!-- penanda agar balik ke info lowongan -->
                    <input type="hidden" name="redirect_to" value="lowongan">

                    <div class="form-group">
                        <label>Pilih Sumber CV</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cv_option" id="cv_option_upload" value="upload" checked onchange="toggleCVOption()">
                            <label class="form-check-label" for="cv_option_upload">Unggah File CV</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cv_option" id="cv_option_dashboard" value="dashboard" onchange="toggleCVOption()">
                            <label class="form-check-label" for="cv_option_dashboard">Gunakan CV di Dashboard</label>
                        </div>
                    </div>

                    <div class="form-group" id="upload_cv_group">
                        <label for="file_applicant">Unggah CV</label>
                        <input type="file" class="form-control-file" id="file_applicant" name="file_applicant">
                        <small class="text-muted">Format: png, jpg, jpeg, pdf</small>
                    </div>

                    <div class="form-group d-none" id="dashboard_cv_group">
                        <label for="dashboard_cv">CV di Dashboard</label>
                        <select class="form-control" id="dashboard_cv" name="dashboard_cv">
                            <option value="">-- Pilih CV --</option>
                            @foreach($cvs as $cv)
                                <option value="{{ $cv->id }}">{{ $cv->cv_name ?? 'CV #'.$cv->id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Kirim Lamaran</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showJobDetail(jobId) {
        fetch(`/dashboard-user/lowongan/${jobId}`)
            .then(res => res.json())
            .then(job => {
                const jobDetailContainer = document.getElementById("job-detail");
                let btn = '';
                if (job.is_closed) {
                    btn = `<p class="text-danger mt-3">Lowongan Ditutup</p>`;
                } else if (job.has_applied) {
                    btn = `<p class="text-success mt-3">Sudah Melamar</p>`;
                } else {
                    btn = `<button class="btn btn-primary mt-3" onclick="openApplicationModal('${job.id}')">Kirim Lamaran</button>`;
                }

                // Fungsi bullet list
                function makeBulletList(text) {
                    if (!text) return "<p>-</p>";
                    const parts = text.split(";").map(p => p.trim()).filter(Boolean);
                    if (parts.length === 0) return "<p>-</p>";
                    return `<ul class="pl-3 mb-0">${parts.map(p => `<li>${p}</li>`).join("")}</ul>`;
                }

                jobDetailContainer.innerHTML = `
                    <div class="d-flex align-items-center mb-4">
                        <img src="${job.personal_company_logo}"
                            style="width:70px;height:70px;object-fit:contain;border-radius:8px;
                                    border:1px solid #ccc;background:#f5f5f5;"
                            class="mr-3">
                        <div>
                            <h5 class="font-weight-bold mb-1">${job.position_hiring}</h5>
                            <small class="text-muted">${job.company_name}</small>
                        </div>
                    </div>

                    <ul class="list-unstyled mb-4">
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-secondary" style="width:18px; text-align:center;"></i>
                            <span>${job.kota ?? ''}${job.provinsi ? ', ' + job.provinsi : ''}</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-building mr-2 text-secondary" style="width:18px; text-align:center;"></i>
                            <span>${job.type_of_company}</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-money-bill-wave mr-2 text-secondary" style="width:18px; text-align:center;"></i>
                            <span>Rp ${new Intl.NumberFormat('id-ID').format(job.gaji_min)} - Rp ${new Intl.NumberFormat('id-ID').format(job.gaji_max)} / Bulan</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="fas fa-clock mr-2 text-secondary" style="width:18px; text-align:center;"></i>
                            <span>Batas Waktu: ${new Date(job.deadline_hiring).toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'})}</span>
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

                    ${btn}
                `;
            });
    }

    function openApplicationModal(jobId) {
        document.getElementById('hiring_id').value = jobId;
        $('#applicationModal').modal('show');
    }

    function toggleCVOption() {
        const upload = document.getElementById('upload_cv_group');
        const dash = document.getElementById('dashboard_cv_group');
        const isUpload = document.getElementById('cv_option_upload').checked;
        upload.classList.toggle('d-none', !isUpload);
        dash.classList.toggle('d-none', isUpload);
    }
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: "{{ session('error') }}",
        showConfirmButton: true
    });
</script>
@endif
@endpush
