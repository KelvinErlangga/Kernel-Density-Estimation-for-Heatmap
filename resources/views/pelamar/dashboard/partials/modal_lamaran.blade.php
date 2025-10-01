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
                    <!-- penanda agar balik ke halaman heatmap -->
                    <input type="hidden" name="redirect_to" value="heatmap">

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
