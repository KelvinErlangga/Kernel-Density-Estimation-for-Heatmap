<?php

use App\Http\Controllers\CurriculumVitaeUserController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardCompanyController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\HiringController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\RecommendedSkillController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TemplateCurriculumVitaeController;
use App\Http\Controllers\UserAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'))->name('welcome');

Route::get('/set-locale/{lang}', function ($lang) {
    session(['locale' => $lang]);
    return back();
})->name('setLocale');

Route::get('/tentang-kami', fn() => view('kami'))->name('tentang-kami');
Route::get('/tanya-jawab', fn() => view('faq'))->name('tanya-jawab');
Route::get('/kontak', fn() => view('kontak'))->name('kontak');

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::get('/curriculum-vitae/template-curriculum-vitae', [CurriculumVitaeUserController::class, 'index'])
    ->name('pelamar.curriculum_vitae.index');

Route::get('/api/job-skills', [JobController::class, 'getJobSkills']);

Route::middleware('auth', 'verified')->group(function () {

    Route::get('/home', function () {
        $user = auth()->user();
        if ($user && $user->hasRole('pelamar')) {
            return redirect()->route('pelamar.dashboard.index');
        }
        return view('welcome');
    })->name('home');

    // ===== Heatmap Lowongan (Pelamar) =====
    Route::get('/pelamar/heatmap', [HiringController::class, 'index'])->name('pelamar.dashboard.heatmap');
    Route::get('/pelamar/heatmap/data', [HiringController::class, 'heatmapData'])->name('pelamar.heatmap.data');
    Route::get('/pelamar/hirings/{id}', [HiringController::class, 'show'])->name('pelamar.hirings.show');
    Route::get('/api/job-suggestions', [HiringController::class, 'jobSuggestions'])->name('pelamar.job.suggestions');

    // ===================== PELAMAR =====================
    Route::middleware('role:pelamar')->group(function () {

        // Generate CV dari template
        Route::post('/curriculum-vitae/template-curriculum-vitae', [CurriculumVitaeUserController::class, 'store'])
            ->name('pelamar.curriculum_vitae.store');

        // ---------- Profile ----------
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile', [CurriculumVitaeUserController::class, 'getProfile'])
            ->name('pelamar.curriculum_vitae.profile.index');
        Route::post('/curriculum-vitae/{curriculum_vitae_user}/profile', [CurriculumVitaeUserController::class, 'addProfile'])
            ->name('pelamar.curriculum_vitae.profile.addProfile');
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/edit/{personalCurriculumVitae}', [CurriculumVitaeUserController::class, 'editProfile'])
            ->name('pelamar.curriculum_vitae.profile.editProfile');
        Route::put('/curriculum-vitae/{curriculum_vitae_user}/profile/edit/{personalCurriculumVitae}', [CurriculumVitaeUserController::class, 'updateProfile'])
            ->name('pelamar.curriculum_vitae.profile.updateProfile');

        // ---------- Experience ----------
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/experience', [CurriculumVitaeUserController::class, 'getExperience'])
            ->name('pelamar.curriculum_vitae.experience.index');
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/experience/create', [CurriculumVitaeUserController::class, 'createExperience'])
            ->name('pelamar.curriculum_vitae.experience.create');
        Route::post('/curriculum-vitae/{curriculum_vitae_user}/profile/experience', [CurriculumVitaeUserController::class, 'addExperience'])
            ->name('pelamar.curriculum_vitae.experience.addExperience');
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/experience/{experience}', [CurriculumVitaeUserController::class, 'editExperience'])
            ->name('pelamar.curriculum_vitae.experience.editExperience');
        Route::put('/curriculum-vitae/{curriculum_vitae_user}/profile/experience/{experience}', [CurriculumVitaeUserController::class, 'updateExperience'])
            ->name('pelamar.curriculum_vitae.experience.updateExperience');
        Route::delete('/curriculum-vitae/{curriculum_vitae_user}/profile/experience/{experience}', [CurriculumVitaeUserController::class, 'deleteExperience'])
            ->name('pelamar.curriculum_vitae.experience.deleteExperience');

        // ---------- Education ----------
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/education', [CurriculumVitaeUserController::class, 'getEducation'])
            ->name('pelamar.curriculum_vitae.education.index');
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/education/create', [CurriculumVitaeUserController::class, 'createEducation'])
            ->name('pelamar.curriculum_vitae.education.createEducation');
        Route::post('/curriculum-vitae/{curriculum_vitae_user}/profile/education', [CurriculumVitaeUserController::class, 'addEducation'])
            ->name('pelamar.curriculum_vitae.education.addEducation');
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/education/{education}', [CurriculumVitaeUserController::class, 'editEducation'])
            ->name('pelamar.curriculum_vitae.education.editEducation');
        Route::put('/curriculum-vitae/{curriculum_vitae_user}/profile/education/{education}', [CurriculumVitaeUserController::class, 'updateEducation'])
            ->name('pelamar.curriculum_vitae.education.updateEducation');
        Route::delete('/curriculum-vitae/{curriculum_vitae_user}/profile/education/{education}', [CurriculumVitaeUserController::class, 'deleteEducation'])
            ->name('pelamar.curriculum_vitae.education.deleteEducation');

        // ---------- Languages ----------
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/language', [CurriculumVitaeUserController::class, 'getLanguage'])
            ->name('pelamar.curriculum_vitae.language.index');
        Route::post('/curriculum-vitae/{curriculum_vitae_user}/profile/language', [CurriculumVitaeUserController::class, 'addLanguage'])
            ->name('pelamar.curriculum_vitae.language.addLanguage');

        // ---------- Skills ----------
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/skill', [CurriculumVitaeUserController::class, 'getSkill'])
            ->name('pelamar.curriculum_vitae.skill.index');
        Route::post('/curriculum-vitae/{curriculum_vitae_user}/profile/skill', [CurriculumVitaeUserController::class, 'addSkill'])
            ->name('pelamar.curriculum_vitae.skill.addSkill');

        // ---------- Organizations ----------
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/organization', [CurriculumVitaeUserController::class, 'getOrganization'])
            ->name('pelamar.curriculum_vitae.organization.index');
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/organization/create', [CurriculumVitaeUserController::class, 'createOrganization'])
            ->name('pelamar.curriculum_vitae.organization.createOrganization');
        Route::post('/curriculum-vitae/{curriculum_vitae_user}/profile/organization', [CurriculumVitaeUserController::class, 'addOrganization'])
            ->name('pelamar.curriculum_vitae.organization.addOrganization');
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/organization/{organization}', [CurriculumVitaeUserController::class, 'EditOrganization'])
            ->name('pelamar.curriculum_vitae.organization.EditOrganization');
        Route::put('/curriculum-vitae/{curriculum_vitae_user}/profile/organization/{organization}', [CurriculumVitaeUserController::class, 'updateOrganization'])
            ->name('pelamar.curriculum_vitae.organization.updateOrganization');
        Route::delete('/curriculum-vitae/{curriculum_vitae_user}/profile/organization/{organization}', [CurriculumVitaeUserController::class, 'deleteOrganization'])
            ->name('pelamar.curriculum_vitae.organization.deleteOrganization');

        // ---------- Achievements ----------
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/achievement', [CurriculumVitaeUserController::class, 'getAchievement'])
            ->name('pelamar.curriculum_vitae.achievement.index');
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/achievement/create', [CurriculumVitaeUserController::class, 'createAchievement'])
            ->name('pelamar.curriculum_vitae.achievement.createAchievement');
        Route::post('/curriculum-vitae/{curriculum_vitae_user}/profile/achievement', [CurriculumVitaeUserController::class, 'addAchievement'])
            ->name('pelamar.curriculum_vitae.achievement.addAchievement');
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/achievement/{achievement}', [CurriculumVitaeUserController::class, 'editAchievement'])
            ->name('pelamar.curriculum_vitae.achievement.editAchievement');
        Route::put('/curriculum-vitae/{curriculum_vitae_user}/profile/achievement/{achievement}', [CurriculumVitaeUserController::class, 'updateAchievement'])
            ->name('pelamar.curriculum_vitae.achievement.updateAchievement');
        Route::delete('/curriculum-vitae/{curriculum_vitae_user}/profile/achievement/{achievement}', [CurriculumVitaeUserController::class, 'deleteAchievement'])
            ->name('pelamar.curriculum_vitae.achievement.deleteAchievement');

        // ---------- CUSTOM SECTIONS (dibuat di halaman Preview) ----------
        Route::prefix('/curriculum-vitae/{curriculum_vitae_user}/custom-sections')
            ->as('pelamar.curriculum_vitae.custom.')
            ->group(function () {
                // Tambah section baru
                Route::post('/', [CurriculumVitaeUserController::class, 'addCustomSection'])->name('add');
                // Update judul/isi (inline)
                Route::put('/{section}', [CurriculumVitaeUserController::class, 'updateCustomSection'])->name('update');
                // Hapus section
                Route::delete('/{section}', [CurriculumVitaeUserController::class, 'deleteCustomSection'])->name('delete');
                // Reorder beberapa custom section
                Route::post('/reorder', [CurriculumVitaeUserController::class, 'reorderCustomSections'])->name('reorder');
            });

        // ---------- Social Media ----------
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/social-media', [CurriculumVitaeUserController::class, 'getSocialMedia'])
            ->name('pelamar.curriculum_vitae.social_media.index');
        Route::post('/curriculum-vitae/{curriculum_vitae_user}/profile/social-media', [CurriculumVitaeUserController::class, 'addSocialMedia'])
            ->name('pelamar.curriculum_vitae.social_media.addSocialMedia');

        // ---------- Preview + Inline Edit ----------
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/preview', [CurriculumVitaeUserController::class, 'previewCV'])
            ->name('pelamar.curriculum_vitae.preview.index');

        // inline update (contenteditable) – dipakai untuk semua section bawaan
        Route::post('/cv/update-inline', [CurriculumVitaeUserController::class, 'updateInline'])
            ->name('pelamar.curriculum_vitae.updateInline');

        // Simpan CV (PDF ke storage + DB)
        Route::post('/curriculum-vitae/save', [CurriculumVitaeUserController::class, 'saveCV'])
            ->name('pelamar.curriculum_vitae.save');

        // Print & Download
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/print-cv', [PDFController::class, 'printCurriculumVitae'])->name('print-cv');
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/export-cv', [PDFController::class, 'exportPDFCurriculumVitae'])->name('export-cv.pdf');

        // ---------- Dashboard Pelamar ----------
        Route::get('/dashboard-user', [DashboardUserController::class, 'index'])->name('pelamar.dashboard.index');
        Route::get('/dashboard-user/akun', [DashboardUserController::class, 'getAkun'])->name('pelamar.dashboard.akun.index');
        Route::put('/dashboard-user/akun/update', [DashboardUserController::class, 'updateProfile'])->name('pelamar.updateProfile');
        Route::get('/dashboard-user/lowongan', [DashboardUserController::class, 'getLowongan'])->name('pelamar.dashboard.lowongan.index');
        Route::get('/dashboard-user/lowongan/{id}', [DashboardUserController::class, 'getShowLowongan'])->name('pelamar.dashboard.lowongan.show');
        Route::post('/dashboard-user/lowongan/kirim-lamaran', [DashboardUserController::class, 'submitApplication'])->name('pelamar.dashboard.lowongan.kirim_lamaran');
        Route::get('/dashboard-user/heatmap', [HiringController::class, 'index'])->name('pelamar.dashboard.heatmap.index');
        Route::get('/dashboard-user/curriculum-vitae', [DashboardUserController::class, 'getCurriculumVitae'])->name('pelamar.dashboard.curriculum_vitae.index');
        Route::delete('/dashboard-user/curriculum-vitae/{curriculum_vitae_user}', [DashboardUserController::class, 'deleteCurriculumVitae'])
            ->name('pelamar.dashboard.curriculum_vitae.delete');
    });

    // ===================== PERUSAHAAN =====================
    Route::middleware('role:perusahaan')->group(callback: function () {
        Route::get('/dashboard-perusahaan', [DashboardCompanyController::class, 'index'])->name('dashboard-perusahaan');
        Route::get('/dashboard-perusahaan/lowongan', [DashboardCompanyController::class, 'getLowongan'])->name('perusahaan.lowongan.index');
        Route::post('/dashboard-perusahaan/lowongan', [DashboardCompanyController::class, 'addLowongan'])->name('perusahaan.lowongan.addLowongan');
        Route::get('/dashboard-perusahaan/lowongan/edit/{hiring}', [DashboardCompanyController::class, 'editLowongan'])->name('perusahaan.lowongan.editLowongan');
        Route::put('/dashboard-perusahaan/lowongan/edit/{hiring}', [DashboardCompanyController::class, 'updateLowongan'])->name('perusahaan.lowongan.updateLowongan');
        Route::delete('/dashboard-perusahaan/lowongan/{hiring}', [DashboardCompanyController::class, 'nonaktifkanLowongan'])->name('perusahaan.lowongan.nonaktifkanLowongan');
        Route::post('/lowongan/{id}/restore', [DashboardCompanyController::class, 'restoreLowongan'])->name('perusahaan.lowongan.restoreLowongan');
        Route::get('/dashboard-perusahaan/kandidat', [DashboardCompanyController::class, 'getKandidat'])->name('perusahaan.kandidat.index');
        Route::post('/dashboard-perusahaan/kandidat/{id}/update-status', [DashboardCompanyController::class, 'updateStatus'])->name('perusahaan.kandidat.updateStatus');
        Route::delete('/dashboard-perusahaan/kandidat/{applicant}/delete-kandidat', [DashboardCompanyController::class, 'deleteKandidat'])->name('perusahaan.kandidat.deleteKandidat');
        Route::get('/dashboard-perusahaan/akun', [DashboardCompanyController::class, 'getAkun'])->name('perusahaan.akun.index');
        Route::put('/dashboard-perusahaan/akun/update', [DashboardCompanyController::class, 'updateProfile'])->name('perusahaan.updateProfile');
        Route::post('/dashboard-perusahaan/upload-logo', [DashboardCompanyController::class, 'uploadLogo'])->name('perusahaan.uploadLogo');
    });

    // ===================== ADMIN =====================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::middleware('role:admin')->group(function () {
            Route::get('dashboard-admin', [DashboardAdminController::class, 'index'])->name('dashboard-admin');

            // Users
            Route::post('users/{user}/restore', [UserAdminController::class, 'restore'])->name('users.restore');
            Route::resource('users', UserAdminController::class);

            // Templates
            Route::post('template_curriculum_vitae/{templateCurriculumVitae}/restore', [TemplateCurriculumVitaeController::class, 'restore'])
                ->name('template_curriculum_vitae.restore');
            Route::resource('template_curriculum_vitae', TemplateCurriculumVitaeController::class);

            // Skills
            Route::resource('skills', SkillController::class);
            Route::post('skills/{skill}/restore', [SkillController::class, 'restore'])->name('skills.restore');

            // Jobs
            Route::resource('jobs', JobController::class);
            Route::post('jobs/{id}/restore', [JobController::class, 'restore'])->name('jobs.restore');

            // Recommended Skills
            Route::resource('recommended_skills', RecommendedSkillController::class);
            Route::post('recommended_skills/{id}/restore', [RecommendedSkillController::class, 'restore'])->name('recommended_skills.restore');
        });
    });
});

require __DIR__ . '/auth.php';
