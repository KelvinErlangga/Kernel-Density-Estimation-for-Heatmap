@extends('pelamar.dashboard.master_user')
@section('title', 'Info Lowongan | CVRE GENERATE')

@section('content')
<div class="container-fluid mt-4">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h5 class="mb-0 font-weight-bold text-primary">Dashboard / <span class="text-dark">Informasi Lowongan</span></h5>
    </div>

    <div class="row">
        <!-- Left Column: Job Listings -->
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 text-center">
                    <h6 class="m-0 font-weight-bold text-dark">Lowongan Lainnya</h6>
                </div>
                <div class="card-body" style="max-height: 760px; overflow-y: auto">
                    @forelse ($hirings as $hiring)
                    <div class="list-group mb-3">
                        <button class="list-group-item list-group-item-action" onclick="showJobDetail('{{ $hiring->id }}')">
                            <div class="d-flex align-items-start">
                                <div>
                                    <h5 class="mb-0 font-weight-bold">{{ $hiring->position_hiring }} - {{ $hiring->personalCompany->name_company }}</h5>
                                    <small>{{ $hiring->address_hiring }}<br />Rp {{ number_format($hiring->gaji, 0, ',', '.') }} / Bulan</small>
                                </div>
                            </div>
                        </button>
                    </div>
                    @empty
                    <p>Tidak ada lowongan</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Job Details -->
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 text-center">
                    <h6 class="m-0 font-weight-bold text-dark">INFORMASI PERUSAHAAN & LOWONGAN</h6>
                </div>
                <div class="card-body" id="job-detail">
                    <div class="text-center">Silakan pilih lowongan untuk melihat detail.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Job Application -->
<div class="modal fade" id="applicationModal" tabindex="-1" aria-labelledby="applicationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applicationModalLabel">Kirim Lamaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('pelamar.dashboard.lowongan.kirim_lamaran')}}" method="POST" enctype="multipart/form-data" onsubmit="console.log('Form dikirim'); return true;">
                    @csrf
                    <input type="hidden" id="hiring_id" name="hiring_id">
                    <div class="form-group">
                        <label for="file_applicant">Unggah CV</label>
                        <input
                            type="file"
                            class="form-control-file @error('file_applicant') is-invalid @enderror"
                            id="file_applicant"
                            name="file_applicant"
                            required>
                        <p>*png, jpg, jpeg, pdf</p>
                        @error('file_applicant')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showJobDetail(jobId) {
        fetch(`/dashboard-user/lowongan/${jobId}`)
            .then(response => response.json())
            .then(job => {
                const jobDetailContainer = document.getElementById("job-detail");
                let actionButton = '';

                if (job.is_closed) {
                    // Jika lowongan sudah ditutup
                    actionButton = `<p class="text-danger mt-3">Lowongan Ditutup</p>`;
                } else if (job.has_applied) {
                    // Jika user sudah melamar
                    actionButton = `<p class="text-success mt-3">Sudah Melamar</p>`;
                } else {
                    // Jika lowongan masih dibuka dan user belum melamar
                    actionButton = `<button type="button" class="btn btn-primary d-block mt-10" onclick="openApplicationModal('${job.id}')">Kirim Lamaran</button>`;
                }

                jobDetailContainer.innerHTML = `
                <h5 class="font-weight-bold pl-4 pr-4 pt-4">${job.position_hiring} - ${job.company_name}</h5>
                <div class="row mt-3 pl-4 pr-4">
                    <div class="col-md-9">
                        <ul class="list-unstyled mb-4">
                            <li><i class="fas fa-map-marker-alt mr-2"></i><strong>Lokasi:</strong> ${job.address_hiring}</li>
                            <li><i class="fas fa-briefcase mr-2"></i><strong>Posisi:</strong> ${job.position_hiring}</li>
                            <li><i class="fas fa-clock mr-2"></i><strong>Jenis:</strong> ${job.type_of_work}</li>
                            <li><i class="fas fa-money-bill-wave mr-2"></i><strong>Gaji:</strong> Rp ${new Intl.NumberFormat('id-ID').format(job.gaji)}</li>
                            <li><i class="fas fa-clock mr-2"></i><strong>Batas Waktu:</strong> ${new Date(job.deadline_hiring).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}</li>
                        </ul>
                        <p><strong>Deskripsi Pekerjaan:</strong></p>
                        <p class="text-justify">${job.description_hiring}</p>
                    </div>
                </div>
                ${actionButton}`;
            });
    }


    function openApplicationModal(jobId) {
        document.getElementById('hiring_id').value = jobId;
        $('#applicationModal').modal('show');
    }
</script>
@endpush