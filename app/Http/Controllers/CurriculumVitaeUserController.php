<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddLanguageRequest;
use App\Http\Requests\AddSkillRequest;
use App\Http\Requests\AddSocialMediaRequest;
use App\Http\Requests\AddUpdateAchievementRequest;
use App\Http\Requests\AddUpdateEducationRequest;
use App\Http\Requests\AddUpdateExperienceRequest;
use App\Http\Requests\AddUpdateOrganizationRequest;
use App\Http\Requests\AddUpdateProfileCurriculumVitaeRequest;
use App\Http\Requests\StoreCurriculumVitaeUserRequest;
use App\Models\Achievement;
use App\Models\CurriculumVitaeUser;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Organization;
use App\Models\PersonalCurriculumVitae;
use App\Models\TemplateCurriculumVitae;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CurriculumVitaeUserController extends Controller
{
    // tampil data template cv
    public function index()
    {
        $templateCurriculumVitae = TemplateCurriculumVitae::getAllTemplateCV();

        return view('pelamar.curriculum_vitae.index', compact('templateCurriculumVitae'));
    }

    // tambah database id template cv dan user id
    public function store(StoreCurriculumVitaeUserRequest $request)
    {
        $user = Auth::user();

        // Validasi input
        $validated = $request->validated();
        $validated['user_id'] = $user->id;

        $adaCVUser = CurriculumVitaeUser::findByUserIdAndTemplateId(
            $validated['user_id'],
            $validated['template_curriculum_vitae_id']
        );

        if ($adaCVUser) {
            // Jika sudah ada, langsung redirect ke view selanjutnya
            return redirect()->route('pelamar.curriculum_vitae.profile.index', $adaCVUser);
        }

        // Jika belum ada, simpan data baru
        $newCVUser = null;
        DB::transaction(function () use ($validated, &$newCVUser) {
            $newCVUser = CurriculumVitaeUser::create($validated);
        });

        return redirect()->route('pelamar.curriculum_vitae.profile.index', $newCVUser);
    }

    // tampil form input profile
    public function getProfile(CurriculumVitaeUser $curriculumVitaeUser)
    {
        return view('pelamar.curriculum_vitae.profile.index', compact('curriculumVitaeUser'));
    }

    // tambah data profile kedalam database
    public function addProfile(AddUpdateProfileCurriculumVitaeRequest $request, CurriculumVitaeUser $curriculumVitaeUser)
    {
        DB::transaction(function () use ($request, $curriculumVitaeUser) {

            $existingAvatar = optional($curriculumVitaeUser->personalCurriculumVitae)->avatar_curriculum_vitae;

            $curriculumVitaeUser->personalCurriculumVitae()->delete();

            $validated = $request->validated();

            if ($request->hasFile('avatar_curriculum_vitae')) {
                $avatar_curriculum_vitaePath = $request->file('avatar_curriculum_vitae')->store('avatar_curriculum_vitae', 'public');
                $validated['avatar_curriculum_vitae'] = $avatar_curriculum_vitaePath;
            } else {
                // Jika tidak ada file baru, gunakan avatar yang sudah ada
                $validated['avatar_curriculum_vitae'] = $existingAvatar;
            }

            $curriculumVitaeUser->personalCurriculumVitae()->create($validated);
        });

        return redirect()->route('pelamar.curriculum_vitae.experience.index', $curriculumVitaeUser->id);
    }

    // tampil data pengalaman kerja
    public function getExperience(CurriculumVitaeUser $curriculumVitaeUser)
    {
        return view('pelamar.curriculum_vitae.experience.index', compact('curriculumVitaeUser'));
    }

    // buat data pengalaman kerja
    public function createExperience(CurriculumVitaeUser $curriculumVitaeUser)
    {
        return view('pelamar.curriculum_vitae.experience.create', compact('curriculumVitaeUser'));
    }

    // tambah data pengalaman kerja ke database
    public function addExperience(AddUpdateExperienceRequest $request, CurriculumVitaeUser $curriculumVitaeUser)
    {
        DB::transaction(function () use ($request, $curriculumVitaeUser) {
            $validated = $request->validated();

            $curriculumVitaeUser->experiences()->create($validated);
        });

        return redirect()->route('pelamar.curriculum_vitae.experience.index', $curriculumVitaeUser->id);
    }

    // edit data pengalaman kerja
    public function editExperience(CurriculumVitaeUser $curriculumVitaeUser, Experience $experience)
    {
        return view('pelamar.curriculum_vitae.experience.edit', compact('curriculumVitaeUser', 'experience'));
    }

    // update data pengalaman kerja ke database
    public function updateExperience(AddUpdateExperienceRequest $request, CurriculumVitaeUser $curriculumVitaeUser, Experience $experience)
    {
        DB::transaction(function () use ($request, $experience) {
            $validated = $request->validated();

            $experience->update($validated);
        });

        return redirect()->route('pelamar.curriculum_vitae.experience.index', $curriculumVitaeUser->id);
    }

    public function deleteExperience(CurriculumVitaeUser $curriculumVitaeUser, Experience $experience)
    {
        DB::transaction(function () use ($experience) {
            $experience->delete();
        });

        return redirect()->route('pelamar.curriculum_vitae.experience.index', $curriculumVitaeUser->id);
    }

    // tampil data pendidikan
    public function getEducation(CurriculumVitaeUser $curriculumVitaeUser)
    {
        return view('pelamar.curriculum_vitae.education.index', compact('curriculumVitaeUser'));
    }

    // buat data pendidikan
    public function createEducation(CurriculumVitaeUser $curriculumVitaeUser)
    {
        return view('pelamar.curriculum_vitae.education.create', compact('curriculumVitaeUser'));
    }

    // tambah data pendidikan ke database
    public function addEducation(AddUpdateEducationRequest $request, CurriculumVitaeUser $curriculumVitaeUser)
    {
        DB::transaction(function () use ($request, $curriculumVitaeUser) {
            $validated = $request->validated();

            $curriculumVitaeUser->educations()->create($validated);
        });

        return redirect()->route('pelamar.curriculum_vitae.education.index', $curriculumVitaeUser->id);
    }

    // edit data pendidikan
    public function editEducation(CurriculumVitaeUser $curriculumVitaeUser, Education $education)
    {
        return view('pelamar.curriculum_vitae.education.edit', compact('curriculumVitaeUser', 'education'));
    }

    // update data pendidikan ke database
    public function updateEducation(AddUpdateEducationRequest $request, CurriculumVitaeUser $curriculumVitaeUser, Education $education)
    {
        DB::transaction(function () use ($request, $education) {
            $validated = $request->validated();

            $education->update($validated);
        });

        return redirect()->route('pelamar.curriculum_vitae.education.index', $curriculumVitaeUser->id);
    }

    public function deleteEducation(CurriculumVitaeUser $curriculumVitaeUser, Education $education)
    {
        DB::transaction(function () use ($education) {
            $education->delete();
        });

        return redirect()->route('pelamar.curriculum_vitae.education.index', $curriculumVitaeUser->id);
    }

    // tampil data bahasa
    public function getLanguage(CurriculumVitaeUser $curriculumVitaeUser)
    {
        return view('pelamar.curriculum_vitae.language.index', compact('curriculumVitaeUser'));
    }

    // tambah data bahasa ke database
    public function addLanguage(AddLanguageRequest $request, CurriculumVitaeUser $curriculumVitaeUser)
    {
        if (!$request->has('language_name') || empty($request->input('language_name'))) {
            $curriculumVitaeUser->languages()->delete();

            return redirect()->route('pelamar.curriculum_vitae.skill.index', $curriculumVitaeUser->id);
        }

        DB::transaction(function () use ($request, $curriculumVitaeUser) {
            $curriculumVitaeUser->languages()->delete();

            $validated = $request->validated();

            foreach ($validated['language_name'] as $index => $languageName) {
                $curriculumVitaeUser->languages()->create([
                    'language_name' => $languageName,
                    'category_level' => $validated['level'][$index],
                ]);
            }
        });
        return redirect()->route('pelamar.curriculum_vitae.skill.index', $curriculumVitaeUser->id);
    }

    // tampil data keahlian
    public function getSkill(CurriculumVitaeUser $curriculumVitaeUser)
    {
        return view('pelamar.curriculum_vitae.skill.index', compact('curriculumVitaeUser'));
    }

    // tambah data keahlian ke database
    public function addSkill(AddSkillRequest $request, CurriculumVitaeUser $curriculumVitaeUser)
    {
        if (!$request->has('skill_name') || empty($request->input('skill_name'))) {
            $curriculumVitaeUser->skills()->delete();

            return redirect()->route('pelamar.curriculum_vitae.organization.index', $curriculumVitaeUser->id);
        }

        DB::transaction(function () use ($request, $curriculumVitaeUser) {
            $curriculumVitaeUser->skills()->delete();

            $validated = $request->validated();

            foreach ($validated['skill_name'] as $index => $skillName) {
                $curriculumVitaeUser->skills()->create([
                    'skill_name' => $skillName,
                    'category_level' => $validated['level'][$index],
                ]);
            }
        });

        return redirect()->route('pelamar.curriculum_vitae.organization.index', $curriculumVitaeUser->id);
    }

    // tampil data organisasi
    public function getOrganization(CurriculumVitaeUser $curriculumVitaeUser)
    {
        return view('pelamar.curriculum_vitae.organization.index', compact('curriculumVitaeUser'));
    }

    // buat data organisasi
    public function createOrganization(CurriculumVitaeUser $curriculumVitaeUser)
    {
        return view('pelamar.curriculum_vitae.organization.create', compact('curriculumVitaeUser'));
    }

    // tambah data organisasi ke database
    public function addOrganization(AddUpdateOrganizationRequest $request, CurriculumVitaeUser $curriculumVitaeUser)
    {
        DB::transaction(function () use ($request, $curriculumVitaeUser) {
            $validated = $request->validated();

            $curriculumVitaeUser->organizations()->create($validated);
        });

        return redirect()->route('pelamar.curriculum_vitae.organization.index', $curriculumVitaeUser->id);
    }

    // edit data organisasi
    public function editOrganization(CurriculumVitaeUser $curriculumVitaeUser, Organization $organization)
    {
        return view('pelamar.curriculum_vitae.organization.edit', compact('curriculumVitaeUser', 'organization'));
    }

    // update data organisasi ke database
    public function updateOrganization(AddUpdateOrganizationRequest $request, CurriculumVitaeUser $curriculumVitaeUser, Organization $organization)
    {
        DB::transaction(function () use ($request, $organization) {
            $validated = $request->validated();

            $organization->update($validated);
        });

        return redirect()->route('pelamar.curriculum_vitae.organization.index', $curriculumVitaeUser->id);
    }

    public function deleteOrganization(CurriculumVitaeUser $curriculumVitaeUser, Organization $organization)
    {
        DB::transaction(function () use ($organization) {
            $organization->delete();
        });

        return redirect()->route('pelamar.curriculum_vitae.organization.index', $curriculumVitaeUser->id);
    }

    // tampil data prestasi
    public function getAchievement(CurriculumVitaeUser $curriculumVitaeUser)
    {
        return view('pelamar.curriculum_vitae.achievement.index', compact('curriculumVitaeUser'));
    }

    // buat data prestasi
    public function createAchievement(CurriculumVitaeUser $curriculumVitaeUser)
    {
        return view('pelamar.curriculum_vitae.achievement.create', compact('curriculumVitaeUser'));
    }

    // tambah data prestasi ke database
    public function addAchievement(AddUpdateAchievementRequest $request, CurriculumVitaeUser $curriculumVitaeUser)
    {
        DB::transaction(function () use ($request, $curriculumVitaeUser) {
            $validated = $request->validated();

            $curriculumVitaeUser->achievements()->create($validated);
        });

        return redirect()->route('pelamar.curriculum_vitae.achievement.index', $curriculumVitaeUser->id);
    }

    // edit data prestasi
    public function editAchievement(CurriculumVitaeUser $curriculumVitaeUser, Achievement $achievement)
    {
        return view('pelamar.curriculum_vitae.achievement.edit', compact('curriculumVitaeUser', 'achievement'));
    }

    // update data prestasi ke database
    public function updateAchievement(AddUpdateAchievementRequest $request, CurriculumVitaeUser $curriculumVitaeUser, Achievement $achievement)
    {
        DB::transaction(function () use ($request, $achievement) {
            $validated = $request->validated();

            $achievement->update($validated);
        });

        return redirect()->route('pelamar.curriculum_vitae.achievement.index', $curriculumVitaeUser->id);
    }

    public function deleteAchievement(CurriculumVitaeUser $curriculumVitaeUser, Achievement $achievement)
    {
        DB::transaction(function () use ($achievement) {
            $achievement->delete();
        });

        return redirect()->route('pelamar.curriculum_vitae.achievement.index', $curriculumVitaeUser->id);
    }

    // tampil data link informasi
    public function getSocialMedia(CurriculumVitaeUser $curriculumVitaeUser)
    {
        return view('pelamar.curriculum_vitae.social_media.index', compact('curriculumVitaeUser'));
    }

    // tambah data link informasi ke database
    public function addSocialMedia(AddSocialMediaRequest $request, CurriculumVitaeUser $curriculumVitaeUser)
    {
        if (!$request->has('link_name') || empty($request->input('link_name'))) {
            $curriculumVitaeUser->links()->delete();

            return redirect()->route('pelamar.curriculum_vitae.preview.index', $curriculumVitaeUser->id);
        }

        DB::transaction(function () use ($request, $curriculumVitaeUser) {
            $curriculumVitaeUser->links()->delete();

            $validated = $request->validated();

            foreach ($validated['link_name'] as $index => $linkName) {
                $curriculumVitaeUser->links()->create([
                    'link_name' => $linkName,
                    'url' => $validated['url'][$index],
                ]);
            }
        });

        return redirect()->route('pelamar.curriculum_vitae.preview.index', $curriculumVitaeUser->id);
    }

    // tampil preview cv
    public function previewCV(CurriculumVitaeUser $curriculumVitaeUser)
    {
        $templateView = 'pelamar.curriculum_vitae.template.cv_' . $curriculumVitaeUser->template_curriculum_vitae_id;

        // Cek apakah view ada
        if (!view()->exists($templateView)) {
            $templateView = 'pelamar.curriculum_vitae.template.cv_1';
        }

        return view($templateView, compact('curriculumVitaeUser'));
    }
}
