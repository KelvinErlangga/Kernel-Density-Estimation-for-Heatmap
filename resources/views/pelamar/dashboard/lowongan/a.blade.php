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
@endsection

@push('scripts')
<script>
    function showJobDetail(jobId) {
        fetch(`/dashboard-user/lowongan/${jobId}`)
            .then(response => response.json())
            .then(job => {
                const jobDetailContainer = document.getElementById("job-detail");
                jobDetailContainer.innerHTML = `

            <h5 class="font-weight-bold pl-4 pr-4 pt-4">${job.position_hiring} - ${job.company_name}</h5>
            <div class="row mt-3 pl-4 pr-4">
                <div class="col-md-9">
                <ul class="list-unstyled mb-4">
                    <li><i class="fas fa-map-marker-alt mr-2"></i><strong>Lokasi:</strong> ${job.address_hiring}</li>
                    <li><i class="fas fa-briefcase mr-2"></i><strong>Posisi:</strong> ${job.position_hiring}</li>
                    <li><i class="fas fa-clock mr-2"></i><strong>Jenis:</strong> ${job.type_of_work}</li>
                    <li><i class="fas fa-money-bill-wave mr-2"></i><strong>Gaji:</strong> Rp ${new Intl.NumberFormat('id-ID').format(job.gaji)}</li>
                </ul>
                <p><strong>Deskripsi Pekerjaan:</strong></p>
                <p class="text-justify">${job.description_hiring}</p>
                </div>
            </div>
                <button type="submit" class="btn btn-primary d-block mt-10">Kirim Lamaran</button>`;
            });
    }
</script>
@endpush