@extends('perusahaan.master_perusahaan')
@section('title', 'Perusahaan - Kandidat | CVRE GENERATE')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h5 class="mb-0 font-weight-bold text-primary">Dashboard / Kelola Data / <span class="text-dark">Kandidat</span></h5>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Tabel Kandidat -->
        <div class="col-xl-12 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Header Card -->
                <div class="card-header py-3 text-center">
                    <h6 class="m-0 font-weight-bold text-dark">KANDIDAT</h6>
                </div>
                <!-- Body Card -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTables" class="table table-bordered table-striped" width="100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Kandidat</th>
                                    <th>Posisi Dilamar</th>
                                    <th>Alamat Email</th>
                                    <th>No. Handphone</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $no = 1;
                                @endphp
                                @foreach($applicants as $applicant)
                                <tr>
                                    <td>{{$no}}.</td>
                                    <td>{{$applicant->user->name}}</td>
                                    <td>{{$applicant->hiring->position_hiring}}</td>
                                    <td>{{$applicant->user->email}}</td>
                                    <td>{{$applicant->user->personalPelamar->phone_pelamar}}</td>
                                    <td>{{$applicant->user->personalPelamar->gender}}</td>
                                    <td>{{$applicant->status}}</td>
                                    <td class="text-center">
                                        <!-- Button to open modal -->
                                        <button class="btn btn-sm" data-toggle="modal" data-target="#viewDetailModal{{$applicant->id}}">
                                            <img src="{{asset('assets/icons/view.svg')}}" alt="View" style="width: 30px; height: 30px" />
                                        </button>

                                        <!-- Form for Deleting -->
                                        <form action="{{ route('perusahaan.kandidat.deleteKandidat', $applicant->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm">
                                                <img src="{{asset('assets/icons/delete.svg')}}" alt="Delete" style="width: 30px; height: 30px" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Modal for viewing and updating details -->
                                <div class="modal fade" id="viewDetailModal{{$applicant->id}}" tabindex="-1" role="dialog" aria-labelledby="viewDetailModalLabel{{$applicant->id}}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="viewDetailModalLabel{{$applicant->id}}">Detail Kandidat</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <ul class="list-group mb-3">
                                                    <li class="list-group-item"><strong>Nama:</strong> {{$applicant->user->name}}</li>
                                                    <li class="list-group-item"><strong>Posisi Dilamar:</strong> {{$applicant->hiring->position_hiring}}</li>
                                                    <li class="list-group-item"><strong>Email:</strong> {{$applicant->user->email}}</li>
                                                    <li class="list-group-item"><strong>No. Handphone:</strong> {{$applicant->user->personalPelamar->phone_pelamar}}</li>
                                                    <li class="list-group-item"><strong>Jenis Kelamin:</strong> {{$applicant->user->personalPelamar->gender}}</li>
                                                    <li class="list-group-item"><strong>Status:</strong> {{$applicant->status}}</li>
                                                </ul>
                                                <div>
                                                    <h6>File Lamaran</h6>
                                                    <iframe src="{{asset('storage/' . $applicant->file_applicant)}}" style="width: 100%; height: 400px;" frameborder="0"></iframe>
                                                </div>

                                                <!-- Form to update status -->
                                                <form action="{{ route('perusahaan.kandidat.updateStatus', $applicant->id) }}" method="POST" class="mt-3">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="status{{$applicant->id}}">Status</label>
                                                        <select name="status" id="status{{$applicant->id}}" class="form-control">
                                                            <option value="Pending" {{$applicant->status == 'Pending' ? 'selected' : ''}}>Pending</option>
                                                            <option value="Diterima" {{$applicant->status == 'Diterima' ? 'selected' : ''}}>Diterima</option>
                                                            <option value="Ditolak" {{$applicant->status == 'Ditolak' ? 'selected' : ''}}>Ditolak</option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Simpan Status</button>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php
                                $no++;
                                @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection