@extends('pelamar.dashboard.master_user')
@section('title', 'Dashboard | CVRE GENERATE')

@push('style')
<style>
    :root{
        --card-radius:16px;
        --soft-shadow:0 10px 24px rgba(19,26,53,.08);
        --soft-border:#e9edf4;
        --muted:#6b7280;
    }

    .card{
        border:1px solid var(--soft-border);
        border-radius:var(--card-radius);
        box-shadow:var(--soft-shadow);
    }
    .card-header{
        background:#fff;
        border-bottom:1px solid var(--soft-border);
        border-top-left-radius:var(--card-radius);
        border-top-right-radius:var(--card-radius);
    }
    .stat-card{
        display:flex;
        align-items:center;
        gap:1rem;
        padding:.75rem 1rem;
    }
    .stat-card img{
        width:56px;
        height:56px;
        object-fit:contain;
    }
    .stat-card .label{
        font-size:.85rem;
        font-weight:600;
        color:var(--muted);
        text-transform:uppercase;
        letter-spacing:.03em;
    }
    .stat-card .value{
        font-size:1.8rem;
        font-weight:700;
        color:#111827;
    }

    /* Tabel */
    table th{
        background:#f9fafc;
        font-size:.9rem;
        text-transform:uppercase;
        color:#374151;
        font-weight:700;
    }
    table td{
        font-size:.9rem;
        vertical-align:middle;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mt-4">
    <!-- Statistik -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body stat-card">
                    <img src="{{asset('assets/dashboard/diproses.svg')}}" alt="Berkas Diproses">
                    <div>
                        <div class="label">Berkas Diproses</div>
                        <div class="value">{{$pending}}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body stat-card">
                    <img src="{{asset('assets/dashboard/ditolak.svg')}}" alt="Berkas Ditolak">
                    <div>
                        <div class="label">Berkas Ditolak</div>
                        <div class="value text-danger">{{$ditolak}}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body stat-card">
                    <img src="{{asset('assets/dashboard/diterima.png')}}" alt="Berkas Diterima">
                    <div>
                        <div class="label">Berkas Diterima</div>
                        <div class="value text-success">{{$diterima}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Lowongan -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header text-center">
                    <h6 class="m-0 font-weight-bold text-dark">INFORMASI LOWONGAN</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTables" class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Perusahaan</th>
                                    <th>Posisi Dibutuhkan</th>
                                    <th>Dibuat</th>
                                    <th>Gaji</th>
                                    <th>Batas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hirings as $hiring)
                                <tr>
                                    <td>{{$hiring->personalCompany->name_company}}</td>
                                    <td>{{$hiring->position_hiring}}</td>
                                    <td>{{date('d M Y', strtotime($hiring->created_at))}}</td>
                                    <td>Rp {{number_format($hiring->gaji, 0, ',', '.')}}</td>
                                    <td>{{date('d M Y', strtotime($hiring->deadline_hiring))}}</td>
                                    <td>
                                        <a href="{{route('pelamar.dashboard.lowongan.index')}}" class="btn btn-sm btn-primary">
                                            Selengkapnya
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Tidak ada lowongan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
