<!-- Modal Lamaran -->
@php
    // Aman kalau view ini di-include dari halaman mana pun yang belum mengirim $cvs
    $cvs = $cvs ?? collect();
@endphp

<div class="modal fade" id="applicationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Kirim Lamaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="applyForm"
                      action="{{ route('pelamar.dashboard.lowongan.kirim_lamaran') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" id="hiring_id" name="hiring_id">

                    {{-- penanda agar balik ke halaman heatmap (boleh kamu ganti dari JS kalau submit dari page lain) --}}
                    <input type="hidden" name="redirect_to" value="heatmap">

                    <div class="form-group">
                        <label class="font-weight-semibold">Pilih Sumber CV</label>

                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="cv_option"
                                   id="cv_option_upload"
                                   value="upload"
                                   checked>
                            <label class="form-check-label" for="cv_option_upload">
                                Unggah File CV
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="cv_option"
                                   id="cv_option_dashboard"
                                   value="dashboard">
                            <label class="form-check-label" for="cv_option_dashboard">
                                Gunakan CV di Dashboard
                            </label>
                        </div>
                    </div>

                    {{-- Upload CV --}}
                    <div class="form-group" id="upload_cv_group">
                        <label for="file_applicant">Unggah CV</label>
                        <input type="file"
                               class="form-control-file"
                               id="file_applicant"
                               name="file_applicant"
                               accept=".pdf,.png,.jpg,.jpeg">
                        <small class="text-muted">Format: png, jpg, jpeg, pdf</small>
                    </div>

                    {{-- Dashboard CV --}}
                    <div class="form-group d-none" id="dashboard_cv_group">
                        <label for="dashboard_cv">CV di Dashboard</label>
                        <select class="form-control" id="dashboard_cv" name="dashboard_cv">
                            <option value="">-- Pilih CV --</option>

                            @forelse($cvs as $cv)
                                <option value="{{ $cv->id }}">
                                    {{ $cv->templateCurriculumVitae->template_curriculum_vitae_name ?? ('CV #'.$cv->id) }}
                                </option>
                            @empty
                                <option value="" disabled>Belum ada CV di Dashboard</option>
                            @endforelse
                        </select>

                        <small class="text-muted">
                            Jika belum ada CV, buat dulu di menu <b>Kelola → Curriculum Vitae</b>.
                        </small>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitLamaran">
                            Kirim Lamaran
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

{{-- Script khusus modal (aman kalau dipanggil dari halaman mana pun) --}}
<script>
(function () {
    function toggleCVOptionLocal() {
        const upload = document.getElementById('upload_cv_group');
        const dash   = document.getElementById('dashboard_cv_group');
        const isUpload = document.getElementById('cv_option_upload')?.checked;

        if (!upload || !dash) return;

        upload.classList.toggle('d-none', !isUpload);
        dash.classList.toggle('d-none', isUpload);

        // Optional: reset field biar tidak nyangkut
        if (isUpload) {
            const sel = document.getElementById('dashboard_cv');
            if (sel) sel.value = '';
        } else {
            const file = document.getElementById('file_applicant');
            if (file) file.value = '';
        }
    }

    document.addEventListener('change', function (e) {
        if (e.target && (e.target.id === 'cv_option_upload' || e.target.id === 'cv_option_dashboard')) {
            toggleCVOptionLocal();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        toggleCVOptionLocal();
    });

    // Expose kalau halaman kamu masih panggil toggleCVOption()
    window.toggleCVOption = toggleCVOptionLocal;
})();
</script>
