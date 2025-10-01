<?php

use App\Http\Controllers\CurriculumVitaeUserController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardCompanyController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\RecommendedSkillController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TemplateCurriculumVitaeController;
use App\Http\Controllers\UserAdminController;
use App\Http\Controllers\HiringController;
use App\Models\RecommendedSkill;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Contracts\Role;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/tentang-kami', function () {
    return view('kami');
})->name('tentang-kami');

Route::get('/tanya-jawab', function () {
    return view('faq');
})->name('tanya-jawab');

Route::get('/kontak', function () {
    return view('kontak');
})->name('kontak');

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::get('/curriculum-vitae/template-curriculum-vitae', [CurriculumVitaeUserController::class, 'index'])->name('pelamar.curriculum_vitae.index');

Route::get('/api/job-skills', [JobController::class, 'getJobSkills']);

Route::middleware('auth', 'verified')->group(function () {

    Route::get('/home', function () {
        $user = auth()->user();
        if ($user && $user->hasRole('pelamar')) {
            return redirect()->route('pelamar.dashboard.index');
        }
        return view('welcome');
    })->name('home');

    // === HEATMAP LOWONGAN (khusus pelamar yang login) ===
    // Halaman heatmap (view)
    Route::get('/pelamar/heatmap', [HiringController::class, 'index'])
        ->name('pelamar.dashboard.heatmap');

    // Data heatmap TERFILTER skill user (JSON)
    Route::get('/pelamar/heatmap/data', [HiringController::class, 'heatmapData'])
        ->name('pelamar.heatmap.data');

    // Detail 1 lowongan (panel kanan)
    Route::get('/pelamar/hirings/{id}', [HiringController::class, 'show'])
        ->name('pelamar.hirings.show');

    // ======== API job suggestions untuk autocomplete search ========
    Route::get('/api/job-suggestions', [HiringController::class, 'jobSuggestions'])
        ->name('pelamar.job.suggestions');


    // Route Pelamar
    Route::middleware('role:pelamar')->group(function () {

        // cv generate
        Route::post('/curriculum-vitae/template-curriculum-vitae', [CurriculumVitaeUserController::class, 'store'])->name('pelamar.curriculum_vitae.store');

        // data diri
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile', [CurriculumVitaeUserController::class, 'getProfile'])
            ->name('pelamar.curriculum_vitae.profile.index');
        Route::post('/curriculum-vitae/{curriculum_vitae_user}/profile', [CurriculumVitaeUserController::class, 'addProfile'])
            ->name('pelamar.curriculum_vitae.profile.addProfile');

        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/edit/{personalCurriculumVitae}', [CurriculumVitaeUserController::class, 'editProfile'])
            ->name('pelamar.curriculum_vitae.profile.editProfile');
        Route::put('/curriculum-vitae/{curriculum_vitae_user}/profile/edit/{personalCurriculumVitae}', [CurriculumVitaeUserController::class, 'updateProfile'])
            ->name('pelamar.curriculum_vitae.profile.updateProfile');

        // pengalaman kerja
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

        // pendidikan
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

        // bahasa
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/language', [CurriculumVitaeUserController::class, 'getLanguage'])
            ->name('pelamar.curriculum_vitae.language.index');
        Route::post('/curriculum-vitae/{curriculum_vitae_user}/profile/language', [CurriculumVitaeUserController::class, 'addLanguage'])
            ->name('pelamar.curriculum_vitae.language.addLanguage');

        // keahlian
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/skill', [CurriculumVitaeUserController::class, 'getSkill'])
            ->name('pelamar.curriculum_vitae.skill.index');
        Route::post('/curriculum-vitae/{curriculum_vitae_user}/profile/skill', [CurriculumVitaeUserController::class, 'addSkill'])
            ->name('pelamar.curriculum_vitae.skill.addSkill');

        // organisasi
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

        // prestasi
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

        // media sosial
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/profile/social-media', [CurriculumVitaeUserController::class, 'getSocialMedia'])
            ->name('pelamar.curriculum_vitae.social_media.index');
        Route::post('/curriculum-vitae/{curriculum_vitae_user}/profile/social-media', [CurriculumVitaeUserController::class, 'addSocialMedia'])
            ->name('pelamar.curriculum_vitae.social_media.addSocialMedia');

        // preview cv
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/preview', [CurriculumVitaeUserController::class, 'previewCV'])->name('pelamar.curriculum_vitae.preview.index');

        Route::post('/cv/update-inline', [CurriculumVitaeUserController::class, 'updateInline'])
            ->name('pelamar.curriculum_vitae.updateInline');


        // routes/web.php
        // Route::post('/cv/save', [CurriculumVitaeUserController::class, 'save'])
        //     ->name('pelamar.curriculum_vitae.save');

        // Simpan CV ke storage & database
        // Route::post('/curriculum-vitae/{curriculum_vitae_user}/save', [CurriculumVitaeUserController::class, 'saveCV'])
        //     ->name('pelamar.curriculum_vitae.save');

        // routes/web.php
        // Route::post('/cv/save', [CurriculumVitaeUserController::class, 'saveCv'])
        //     ->name('pelamar.curriculum_vitae.save');

        Route::post('/curriculum-vitae/save', [CurriculumVitaeUserController::class, 'saveCV'])
            ->name('pelamar.curriculum_vitae.save');

        // print cv
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/print-cv', [PDFController::class, 'printCurriculumVitae'])->name('print-cv');

        // download cv
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/export-cv', [PDFController::class, 'exportPDFCurriculumVitae'])->name('export-cv.pdf');

        // dashboard user
        Route::get('/dashboard-user', [DashboardUserController::class, 'index'])
            ->name('pelamar.dashboard.index');

        Route::get('/dashboard-user/akun', [DashboardUserController::class, 'getAkun'])
            ->name('pelamar.dashboard.akun.index');

        // Route untuk update profil
        Route::put('/dashboard-user/akun/update', [DashboardUserController::class, 'updateProfile'])
            ->name('pelamar.updateProfile');

        // dashboard user lowongan
        Route::get('/dashboard-user/lowongan', [DashboardUserController::class, 'getLowongan'])
            ->name('pelamar.dashboard.lowongan.index');

        // dashboard user lowongan
        Route::get('/dashboard-user/lowongan/{id}', [DashboardUserController::class, 'getShowLowongan'])
            ->name('pelamar.dashboard.lowongan.show');

        // dashboard user kirim lamaran
        Route::post('/dashboard-user/lowongan/kirim-lamaran', [DashboardUserController::class, 'submitApplication'])
            ->name('pelamar.dashboard.lowongan.kirim_lamaran');

        // dashboard user lowongan
        // Route::get('/dashboard-user/heatmap', [DashboardUserController::class, 'getHeatmap'])
        //     ->name('pelamar.dashboard.heatmap.index');

        Route::get('/dashboard-user/heatmap', [HiringController::class, 'index'])
            ->name('pelamar.dashboard.heatmap.index');

        // dashboard user cv
        Route::get('/dashboard-user/curriculum-vitae', [DashboardUserController::class, 'getCurriculumVitae'])
            ->name('pelamar.dashboard.curriculum_vitae.index');
        Route::delete('/dashboard-user/curriculum-vitae/{curriculum_vitae_user}', [DashboardUserController::class, 'deleteCurriculumVitae'])
            ->name('pelamar.dashboard.curriculum_vitae.delete');
    });


    // Route Perusahaan
    Route::middleware('role:perusahaan')->group(callback: function () {

        Route::get('/dashboard-perusahaan', [DashboardCompanyController::class, 'index'])
            ->name('dashboard-perusahaan');

        Route::get('/dashboard-perusahaan/lowongan', [DashboardCompanyController::class, 'getLowongan'])
            ->name('perusahaan.lowongan.index');

        Route::post('/dashboard-perusahaan/lowongan', [DashboardCompanyController::class, 'addLowongan'])
            ->name('perusahaan.lowongan.addLowongan');

        Route::get('/dashboard-perusahaan/lowongan/edit/{hiring}', [DashboardCompanyController::class, 'editLowongan'])
            ->name('perusahaan.lowongan.editLowongan');

        Route::put('/dashboard-perusahaan/lowongan/edit/{hiring}', [DashboardCompanyController::class, 'updateLowongan'])
            ->name('perusahaan.lowongan.updateLowongan');

        Route::delete('/dashboard-perusahaan/lowongan/{hiring}', [DashboardCompanyController::class, 'nonaktifkanLowongan'])
            ->name('perusahaan.lowongan.nonaktifkanLowongan');

        Route::post('/lowongan/{id}/restore', [DashboardCompanyController::class, 'restoreLowongan'])
            ->name('perusahaan.lowongan.restoreLowongan');

        Route::get('/dashboard-perusahaan/kandidat', [DashboardCompanyController::class, 'getKandidat'])
            ->name('perusahaan.kandidat.index');

        Route::post('/dashboard-perusahaan/kandidat/{id}/update-status', [DashboardCompanyController::class, 'updateStatus'])->name('perusahaan.kandidat.updateStatus');

        Route::delete('/dashboard-perusahaan/kandidat/{applicant}/delete-kandidat', [DashboardCompanyController::class, 'deleteKandidat'])->name('perusahaan.kandidat.deleteKandidat');

        Route::get('/dashboard-perusahaan/akun', [DashboardCompanyController::class, 'getAkun'])
            ->name('perusahaan.akun.index');

        Route::put('/dashboard-perusahaan/akun/update', [DashboardCompanyController::class, 'updateProfile'])
            ->name('perusahaan.updateProfile');

        Route::post('/dashboard-perusahaan/upload-logo', [DashboardCompanyController::class, 'uploadLogo'])
            ->name('perusahaan.uploadLogo');
    });


    // Route Admin
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::middleware('role:admin')->group(function () {

            Route::get('dashboard-admin', [DashboardAdminController::class, 'index'])->name('dashboard-admin');

            // USERS
            Route::post('users/{user}/restore', [UserAdminController::class, 'restore'])->name('users.restore');
            Route::resource('users', UserAdminController::class);

            // TEMPLATE CURRICULUM VITAE
            Route::post('template_curriculum_vitae/{templateCurriculumVitae}/restore', [TemplateCurriculumVitaeController::class, 'restore'])
                ->name('template_curriculum_vitae.restore');
            Route::resource('template_curriculum_vitae', TemplateCurriculumVitaeController::class);

            // SKILLS
            Route::resource('skills', SkillController::class);
            Route::post('skills/{skill}/restore', [SkillController::class, 'restore'])->name('skills.restore');

            // JOBS
            Route::resource('jobs', JobController::class);
            Route::post('jobs/{id}/restore', [JobController::class, 'restore'])
                ->name('jobs.restore');

            // RECOMMENDED SKILLS
            Route::resource('recommended_skills', RecommendedSkillController::class);
            Route::post('recommended_skills/{id}/restore', [RecommendedSkillController::class, 'restore'])->name('recommended_skills.restore');
        });
    });
});

require __DIR__ . '/auth.php';
