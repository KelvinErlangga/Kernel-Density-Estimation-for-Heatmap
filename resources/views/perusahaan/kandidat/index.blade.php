@extends('perusahaan.master_perusahaan')
@section('title', 'Perusahaan - Kandidat | CVRE GENERATE')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header text-center">
                    <h6 class="m-0 font-weight-bold text-dark">KANDIDAT</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTables" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Kandidat</th>
                                    <th>Posisi Dilamar</th>
                                    <th>Email</th>
                                    <th>No. HP</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no=1; @endphp
                                @foreach($applicants as $applicant)
                                <tr>
                                    <td>{{ $no++ }}.</td>
                                    <td>{{ $applicant->user->name }}</td>
                                    <td>{{ $applicant->hiring->position_hiring }}</td>
                                    <td>{{ $applicant->user->email }}</td>
                                    <td>{{ $applicant->user->personalPelamar->phone_pelamar }}</td>
                                    <td>{{ $applicant->user->personalPelamar->gender }}</td>
                                    <td>{{ $applicant->status }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <!-- Tombol Detail -->
                                            <button class="btn btn-sm btn-light mr-2" data-toggle="modal" data-target="#viewDetailModal{{ $applicant->id }}">
                                                <img src="{{ asset('assets/icons/view.svg') }}" alt="View" style="width: 28px; height:28px;">
                                            </button>
                                            <!-- Tombol Hapus -->
                                            <form id="delete-form-{{ $applicant->id }}" action="{{ route('perusahaan.kandidat.deleteKandidat', $applicant->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-light" onclick="confirmDelete('delete-form-{{ $applicant->id }}')">
                                                    <img src="{{ asset('assets/icons/delete.svg') }}" alt="Delete" style="width: 28px; height:28px;">
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Detail Kandidat -->
                                <div class="modal fade" id="viewDetailModal{{ $applicant->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detail Kandidat</h5>
                                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <ul class="list-group mb-3">
                                                    <li class="list-group-item"><strong>Nama:</strong> {{ $applicant->user->name }}</li>
                                                    <li class="list-group-item"><strong>Posisi:</strong> {{ $applicant->hiring->position_hiring }}</li>
                                                    <li class="list-group-item"><strong>Email:</strong> {{ $applicant->user->email }}</li>
                                                    <li class="list-group-item"><strong>No. HP:</strong> {{ $applicant->user->personalPelamar->phone_pelamar }}</li>
                                                    <li class="list-group-item"><strong>Gender:</strong> {{ $applicant->user->personalPelamar->gender }}</li>
                                                    <li class="list-group-item"><strong>Status:</strong> {{ $applicant->status }}</li>
                                                </ul>

                                                <div>
                                                    <h6>File Lamaran</h6>
                                                    <iframe src="{{ asset('storage/' . $applicant->file_applicant) }}" style="width: 100%; height: 400px;" frameborder="0"></iframe>
                                                </div>

                                                <form action="{{ route('perusahaan.kandidat.updateStatus', $applicant->id) }}" method="POST" class="mt-3">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select name="status" id="status{{ $applicant->id }}"
                                                            class="form-control status-select"
                                                            data-applicant-id="{{ $applicant->id }}">
                                                            <option value="Proses Seleksi" {{ $applicant->status=="Proses Seleksi"?'selected':'' }}>Proses Seleksi</option>
                                                            <option value="Diterima" {{ $applicant->status=="Diterima"?'selected':'' }}>Diterima</option>
                                                            <option value="Ditolak" {{ $applicant->status=="Ditolak"?'selected':'' }}>Ditolak</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group d-none" id="inviteOption{{ $applicant->id }}">
                                                        <label>Undang Wawancara <span class="text-danger">*</span></label>
                                                        <select name="undangan_via" id="invite_type{{ $applicant->id }}"
                                                            class="form-control invite-select"
                                                            data-applicant-id="{{ $applicant->id }}">
                                                            <option value="">-- Pilih Opsi --</option>
                                                            <option value="email">Kirim Undangan ke Email</option>
                                                            <option value="whatsapp">Kirim Undangan ke WhatsApp</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group d-none" id="interviewNote{{ $applicant->id }}">
                                                        <label>Isi Undangan Wawancara</label>
                                                        <textarea name="interview_note" class="form-control"
                                                            rows="4" placeholder="Tulis undangan di sini..."></textarea>
                                                    </div>

                                                    <div class="form-group d-none" id="whatsappInvite{{ $applicant->id }}">
                                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$applicant->user->personalPelamar->phone_pelamar) }}"
                                                           target="_blank" class="btn btn-success">
                                                            <i class="fab fa-whatsapp"></i> Kirim WhatsApp
                                                        </a>
                                                    </div>

                                                    <button type="submit" class="btn btn-primary">Simpan Status</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(formId){
    Swal.fire({
        title:'Apakah Anda yakin?',
        text:'Data kandidat ini akan dihapus permanen!',
        icon:'warning',
        showCancelButton:true,
        confirmButtonText:'Ya, hapus',
        cancelButtonText:'Batal',
    }).then((res)=>{ if(res.isConfirmed){ document.getElementById(formId).submit(); } });
}

document.addEventListener("DOMContentLoaded",function(){
    document.querySelectorAll(".status-select").forEach(function(select){
        const id=select.dataset.applicantId;
        const inviteOption=document.getElementById("inviteOption"+id);
        const inviteSelect=document.getElementById("invite_type"+id);
        const emailField=document.getElementById("interviewNote"+id);
        const whatsappBtn=document.getElementById("whatsappInvite"+id);

        function toggleStatus(){
            if(select.value==="Proses Seleksi"){
                inviteOption.classList.remove("d-none");
                inviteSelect.setAttribute("required","required");
            } else {
                inviteOption.classList.add("d-none");
                emailField.classList.add("d-none");
                whatsappBtn.classList.add("d-none");
                inviteSelect.value="";
                inviteSelect.removeAttribute("required");
            }
        }
        function toggleInvite(){
            if(inviteSelect.value==="email"){
                emailField.classList.remove("d-none");
                whatsappBtn.classList.add("d-none");
            } else if(inviteSelect.value==="whatsapp"){
                emailField.classList.add("d-none");
                whatsappBtn.classList.remove("d-none");
            } else {
                emailField.classList.add("d-none");
                whatsappBtn.classList.add("d-none");
            }
        }

        select.addEventListener("change",toggleStatus);
        inviteSelect.addEventListener("change",toggleInvite);
        toggleStatus(); toggleInvite();
    });
});
</script>

@if(session('success'))
<script>
Swal.fire({
    icon:'success',
    title:'Berhasil',
    text:"{{ session('success') }}",
    confirmButtonText:'OK'
}).then(()=>{
    // setelah OK, reload ulang untuk clear session → tidak muter
    window.location.href="{{ route('perusahaan.kandidat.index') }}";
});
</script>
@endif
@endpush
