<?php

use App\Http\Controllers\CoverLetterUserController;
use App\Http\Controllers\CurriculumVitaeUserController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardCompanyController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\RecommendedSkillController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TemplateCoverLetterController;
use App\Http\Controllers\TemplateCurriculumVitaeController;
use App\Http\Controllers\UserAdminController;
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

Route::get('/cover-letter/template-cover-letter', [CoverLetterUserController::class, 'index'])->name('pelamar.cover_letter.index');

Route::get('/api/job-skills', [JobController::class, 'getJobSkills']);

Route::middleware('auth', 'verified')->group(function () {

    Route::get('/home', function () {
        return view('welcome');
    })->name('home');

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

        // print cv
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/print-cv', [PDFController::class, 'printCurriculumVitae'])->name('print-cv');

        // download cv
        Route::get('/curriculum-vitae/{curriculum_vitae_user}/export-cv', [PDFController::class, 'exportPDFCurriculumVitae'])->name('export-cv.pdf');


        // cover letter generate
        Route::post('/cover-letter/template-cover-letter', [CoverLetterUserController::class, 'store'])->name('pelamar.cover_letter.store');

        // data diri
        Route::get('/cover-letter/{cover_letter_user}/profile', [CoverLetterUserController::class, 'getProfile'])
            ->name('pelamar.cover_letter.profile.index');
        Route::post('/cover-letter/{cover_letter_user}/profile', [CoverLetterUserController::class, 'addProfile'])
            ->name('pelamar.cover_letter.profile.addProfile');

        // data perusahaan
        Route::get('/cover-letter/{cover_letter_user}/profile-company', [CoverLetterUserController::class, 'getProfileCompany'])
            ->name('pelamar.cover_letter.profile_company.index');
        Route::post('/cover-letter/{cover_letter_user}/profile-company', [CoverLetterUserController::class, 'addProfileCompany'])
            ->name('pelamar.cover_letter.profile_company.addProfileCompany');

        // paragraf pembuka
        Route::get('/cover-letter/{cover_letter_user}/paragraf-pembuka', [CoverLetterUserController::class, 'getParagrafPembuka'])
            ->name('pelamar.cover_letter.paragraf_pembuka.index');
        Route::post('/cover-letter/{cover_letter_user}/paragraf-pembuka', [CoverLetterUserController::class, 'addParagrafPembuka'])
            ->name('pelamar.cover_letter.paragraf_pembuka.addParagrafPembuka');

        // paragraf utama
        Route::get('/cover-letter/{cover_letter_user}/paragraf-utama', [CoverLetterUserController::class, 'getParagrafUtama'])
            ->name('pelamar.cover_letter.paragraf_utama.index');
        Route::post('/cover-letter/{cover_letter_user}/paragraf-utama', [CoverLetterUserController::class, 'addParagrafUtama'])
            ->name('pelamar.cover_letter.paragraf_utama.addParagrafUtama');

        // paragraf penutup
        Route::get('/cover-letter/{cover_letter_user}/paragraf-penutup', [CoverLetterUserController::class, 'getParagrafPenutup'])
            ->name('pelamar.cover_letter.paragraf_penutup.index');
        Route::post('/cover-letter/{cover_letter_user}/paragraf-penutup', [CoverLetterUserController::class, 'addParagrafPenutup'])
            ->name('pelamar.cover_letter.paragraf_penutup.addParagrafPenutup');

        // tanda tangan
        Route::get('/cover-letter/{cover_letter_user}/tanda-tangan', [CoverLetterUserController::class, 'getTandaTangan'])
            ->name('pelamar.cover_letter.tanda_tangan.index');
        Route::post('/cover-letter/{cover_letter_user}/tanda-tangan', [CoverLetterUserController::class, 'addTandaTangan'])
            ->name('pelamar.cover_letter.tanda_tangan.addTandaTangan');

        // dashboard user
        Route::get('/dashboard-user', [DashboardUserController::class, 'index'])
            ->name('pelamar.dashboard.index');

        Route::get('/dashboard-user/akun', [DashboardUserController::class, 'getAkun'])
            ->name('pelamar.dashboard.akun.index');

        // dashboard user lowongan
        Route::get('/dashboard-user/lowongan', [DashboardUserController::class, 'getLowongan'])
            ->name('pelamar.dashboard.lowongan.index');

        // dashboard user lowongan
        Route::get('/dashboard-user/lowongan/{id}', [DashboardUserController::class, 'getShowLowongan'])
            ->name('pelamar.dashboard.lowongan.show');

        // dashboard user kirim lamaran
        Route::post('/dashboard-user/lowongan/kirim-lamaran', [DashboardUserController::class, 'submitApplication'])
            ->name('pelamar.dashboard.lowongan.kirim_lamaran');

        // dashboard user cv
        Route::get('/dashboard-user/curriculum-vitae', [DashboardUserController::class, 'getCurriculumVitae'])
            ->name('pelamar.dashboard.curriculum_vitae.index');
        Route::delete('/dashboard-user/curriculum-vitae/{curriculum_vitae_user}', [DashboardUserController::class, 'deleteCurriculumVitae'])
            ->name('pelamar.dashboard.curriculum_vitae.delete');

        // dashboard user cl
        Route::get('/dashboard-user/cover-letter', [DashboardUserController::class, 'getCoverLetter'])
            ->name('pelamar.dashboard.cover_letter.index');
        Route::delete('/dashboard-user/cover-letter/{cover_letter_user}', [DashboardUserController::class, 'deleteCoverLetter'])
            ->name('pelamar.dashboard.cover_letter.delete');
    });


    // Route Perusahaan
    Route::middleware('role:perusahaan')->group(function () {

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

        Route::delete('/dashboard-perusahaan/lowongan/{hiring}', [DashboardCompanyController::class, 'deleteLowongan'])
            ->name('perusahaan.lowongan.deleteLowongan');

        Route::get('/dashboard-perusahaan/kandidat', [DashboardCompanyController::class, 'getKandidat'])
            ->name('perusahaan.kandidat.index');

        Route::post('/dashboard-perusahaan/kandidat/{id}/update-status', [DashboardCompanyController::class, 'updateStatus'])->name('perusahaan.kandidat.updateStatus');

        Route::delete('/dashboard-perusahaan/kandidat/{applicant}/delete-kandidat', [DashboardCompanyController::class, 'deleteKandidat'])->name('perusahaan.kandidat.deleteKandidat');
    });


    // Route Admin
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::middleware('role:admin')->group(function () {

            Route::get('dashboard-admin', [DashboardAdminController::class, 'index'])->name('dashboard-admin');

            Route::get('users', [UserAdminController::class, 'index'])->name('users-admin');
            Route::delete('users/{user}', [UserAdminController::class, 'destroy'])->name('destroy-user-admin');

            Route::resource('template_curriculum_vitae', TemplateCurriculumVitaeController::class);
            Route::resource('template_cover_letter', TemplateCoverLetterController::class);
            Route::resource('skills', SkillController::class);
            Route::resource('jobs', JobController::class);
            Route::resource('recommended_skills', RecommendedSkillController::class);
        });
    });
});

require __DIR__ . '/auth.php';
