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
use App\Models\CustomSection;
use App\Models\PersonalDetail;
use App\Models\TemplateCurriculumVitae;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

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
            // return redirect()->route('pelamar.curriculum_vitae.preview.index', $adaCVUser);
        }

        // Jika belum ada, simpan data baru
        $newCVUser = null;
        DB::transaction(function () use ($validated, &$newCVUser) {
            $newCVUser = CurriculumVitaeUser::create($validated);
        });

        return redirect()->route('pelamar.curriculum_vitae.profile.index', $newCVUser);
        // return redirect()->route('pelamar.curriculum_vitae.preview.index', $newCVUser);
    }

    // 1) Buat section baru (default: Text Section)
    public function addCustomSection(Request $request, CurriculumVitaeUser $curriculumVitaeUser)
    {
        $type = $request->input('type', 'custom_text'); // sekarang cuma 'custom_text'
        $nextOrder = (int) $curriculumVitaeUser->customSections()->max('sort_order') + 1;

        $section = $curriculumVitaeUser->customSections()->create([
            'section_key'   => $type,
            'section_title' => $request->input('title', 'Text section'),
            'subtitle'      => $request->input('subtitle'),
            'payload'       => ['body' => 'Your skills, certifications, interests, and more.'],
            'sort_order'    => $nextOrder,
        ]);

        // balikin HTML partial biar langsung di-inject ke preview
        $html = view('pelamar.curriculum_vitae.preview.sections.custom_text', [
            'cv'      => $curriculumVitaeUser,
            'section' => $section
        ])->render();

        return response()->json([
            'success' => true,
            'id'      => $section->id,
            'html'    => $html,
        ]);
    }

    // 2) Update inline (judul / isi)
    public function updateCustomSection(Request $request, CurriculumVitaeUser $curriculumVitaeUser, CustomSection $section)
    {
        abort_unless($section->curriculum_vitae_user_id === $curriculumVitaeUser->id, 403);

        $field = $request->input('field');
        $value = $request->input('value');

        if ($field === 'section_title') {
            $section->section_title = $value;
        } elseif (str_starts_with($field, 'payload.')) {
            $path = substr($field, 8); // setelah 'payload.'
            $payload = $section->payload ?? [];
            data_set($payload, $path, $value);
            $section->payload = $payload;
        } elseif ($field === 'subtitle') {
            $section->subtitle = $value;
        }

        $section->save();

        return response()->json(['success' => true]);
    }

    // 3) Hapus section
    public function deleteCustomSection(CurriculumVitaeUser $curriculumVitaeUser, CustomSection $section)
    {
        abort_unless($section->curriculum_vitae_user_id === $curriculumVitaeUser->id, 403);
        $section->delete();
        return response()->json(['success' => true]);
    }

    // 4) Reorder section (khusus custom)
    public function reorderCustomSections(Request $request, CurriculumVitaeUser $curriculumVitaeUser)
    {
        $ids = $request->input('ids', []); // array of custom section IDs in new order

        DB::transaction(function () use ($ids, $curriculumVitaeUser) {
            foreach ($ids as $i => $id) {
                $curriculumVitaeUser->customSections()
                    ->where('id', $id)
                    ->update(['sort_order' => $i + 1]);
            }
        });

        return response()->json(['success' => true]);
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

            $existingAvatar = optional($curriculumVitaeUser->personalDetail)->avatar_curriculum_vitae;

            $curriculumVitaeUser->personalDetail()->delete();

            $validated = $request->validated();

            if ($request->hasFile('avatar_curriculum_vitae')) {
                $avatar_curriculum_vitaePath = $request->file('avatar_curriculum_vitae')->store('avatar_curriculum_vitae', 'public');
                $validated['avatar_curriculum_vitae'] = $avatar_curriculum_vitaePath;
            } else {
                // Jika tidak ada file baru, gunakan avatar yang sudah ada
                $validated['avatar_curriculum_vitae'] = $existingAvatar;
            }

            $curriculumVitaeUser->personalDetail()->create($validated);
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
    // public function previewCV(CurriculumVitaeUser $curriculumVitaeUser)
    // {
    //     $templateView = 'pelamar.curriculum_vitae.template.cv_' . $curriculumVitaeUser->template_curriculum_vitae_id;

    //     // Cek apakah view ada
    //     if (!view()->exists($templateView)) {
    //         $templateView = 'pelamar.curriculum_vitae.template.cv_1';
    //     }

    //     return view($templateView, compact('curriculumVitaeUser'));
    // }

    // public function previewCV(CurriculumVitaeUser $curriculumVitaeUser)
    // {
    //     $curriculumVitaeUser->load([
    //         'personalDetail',
    //         'educations',
    //         'experiences',
    //         'skills',
    //         'languages',
    //         'templateCV'
    //     ]);

    //     $template = $curriculumVitaeUser->templateCV;

    //     return view('pelamar.curriculum_vitae.preview.index', compact('curriculumVitaeUser', 'template'));
    // }

    public function previewCV(CurriculumVitaeUser $curriculumVitaeUser)
    {
        $curriculumVitaeUser->load([
            'personalDetail',
            'educations',
            'experiences',
            'skills',
            'languages',
            'organizations',
            'achievements',
            'links',
            'templateCV'
        ]);

        $template = $curriculumVitaeUser->templateCV;

        if (!$template) {
            return redirect()->back()->with('error', 'Template CV belum tersedia!');
        }

        // Layout: pastikan array
        if (is_array($template->layout_json)) {
            $layout = $template->layout_json;
        } elseif (is_string($template->layout_json)) {
            $layout = json_decode($template->layout_json, true) ?? [];
        } else {
            $layout = [];
        }

        // Style: pastikan array
        if (is_array($template->style_json)) {
            $style = $template->style_json;
        } elseif (is_string($template->style_json)) {
            $style = json_decode($template->style_json, true) ?? [];
        } else {
            $style = [];
        }

        return view('pelamar.curriculum_vitae.preview.index', [
            'cv'       => $curriculumVitaeUser,
            'layout'   => $layout,
            'style'    => $style,
            'template' => $template
        ]);
    }

    public function updateInline(Request $request)
    {
        // basic validation (id nullable here, later validated for experiences)
        $request->validate([
            'cv_id'   => 'required|integer',
            'section' => 'required|string',
            'field'   => 'required|string',
            'value'   => 'nullable',
            'id'      => 'nullable|integer',
        ]);

        $cv = CurriculumVitaeUser::findOrFail($request->cv_id);
        $field = $request->field;
        $value = $request->value ?? '';

        // -----------------------
        // PERSONAL DETAIL (hasOne)
        // -----------------------
        if ($request->section === 'personal_detail') {
            $personal = $cv->personalDetail()->first();
            if (!$personal) {
                return response()->json(['success' => false, 'message' => 'Personal detail tidak ditemukan'], 404);
            }

            $allowedPersonal = [
                'first_name_curriculum_vitae',
                'last_name_curriculum_vitae',
                'city_curriculum_vitae',
                'address_curriculum_vitae',
                'phone_curriculum_vitae',
                'email_curriculum_vitae',
                'personal_summary',
            ];

            // special "name" token (if you still send field === 'name')
            if ($field === 'name') {
                $parts = preg_split('/\s+/', trim(strip_tags($value)), 2, PREG_SPLIT_NO_EMPTY);
                $personal->update([
                    'first_name_curriculum_vitae' => $parts[0] ?? '',
                    'last_name_curriculum_vitae'  => $parts[1] ?? '',
                ]);
                return response()->json(['success' => true, 'message' => 'Nama berhasil diperbarui']);
            }

            if (in_array($field, $allowedPersonal, true)) {
                // sanitize: text fields -> strip tags
                $sanitized = in_array($field, ['personal_summary'], true) ? strip_tags($value, '<p><br><strong><em><ul><li>') : strip_tags($value);
                $personal->update([$field => $sanitized]);
                return response()->json(['success' => true, 'message' => 'Perubahan personal detail disimpan']);
            }

            return response()->json(['success' => false, 'message' => 'Field personal_detail tidak diizinkan'], 400);
        }

        // -----------------------
        // EXPERIENCES (hasMany)
        // -----------------------
        if ($request->section === 'experiences') {
            $request->validate(['id' => 'required|integer']);
            $id = $request->id;

            // ensure experience belongs to this CV
            $exp = \App\Models\Experience::where('id', $id)
                ->where('curriculum_vitae_user_id', $cv->id) // adjust column name if different
                ->first();

            if (!$exp) {
                return response()->json(['success' => false, 'message' => 'Experience tidak ditemukan atau bukan milik CV ini'], 404);
            }

            $allowedExp = [
                'company_experience',
                'position_experience',
                'city_experience',
                'start_date',
                'end_date',
                'description_experience',
            ];

            if (!in_array($field, $allowedExp, true)) {
                return response()->json(['success' => false, 'message' => 'Field experiences tidak diizinkan'], 400);
            }

            // sanitize:
            if ($field === 'description_experience') {
                // allow limited HTML for lists / linebreaks
                $sanitized = strip_tags($value, '<ul><li><br><p><strong><em><b>');
            } else {
                $sanitized = strip_tags($value);
            }

            // update using update() so Laravel mass-assignment checks apply
            $updated = $exp->update([$field => $sanitized]);

            if ($updated) {
                return response()->json(['success' => true, 'message' => 'Experience berhasil diperbarui', 'data' => $exp->fresh()]);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan perubahan'], 500);
            }
        }

        // -----------------------
        // EDUCATIONS (hasMany)
        // -----------------------
        if ($request->section === 'educations') {
            $request->validate(['id' => 'required|integer']);
            $id = $request->id;

            $edu = \App\Models\Education::where('id', $id)
                ->where('curriculum_vitae_user_id', $cv->id)
                ->first();

            if (!$edu) {
                return response()->json(['success' => false, 'message' => 'Education tidak ditemukan atau bukan milik CV ini'], 404);
            }

            $allowedEdu = [
                'school_name',
                'city_education',
                'start_date',
                'end_date',
                'field_of_study',
                'description_education',
            ];

            if (!in_array($field, $allowedEdu, true)) {
                return response()->json(['success' => false, 'message' => 'Field educations tidak diizinkan'], 400);
            }

            if ($field === 'description_education') {
                $sanitized = strip_tags($value, '<ul><li><br><p><strong><em><b>');
            } else {
                $sanitized = strip_tags($value);
            }

            $updated = $edu->update([$field => $sanitized]);

            if ($updated) {
                return response()->json(['success' => true, 'message' => 'Education berhasil diperbarui', 'data' => $edu->fresh()]);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan perubahan'], 500);
            }
        }

        // -----------------------
        // SKILLS (hasMany)
        // -----------------------
        if ($request->section === 'skills') {
            $request->validate(['id' => 'required|integer']);
            $id = $request->id;

            $skill = \App\Models\Skill::where('id', $id)
                ->where('curriculum_vitae_user_id', $cv->id)
                ->first();

            if (!$skill) {
                return response()->json(['success' => false, 'message' => 'Skill tidak ditemukan atau bukan milik CV ini'], 404);
            }

            $allowedSkills = ['skill_name', 'category_level'];

            if (!in_array($field, $allowedSkills, true)) {
                return response()->json(['success' => false, 'message' => 'Field skills tidak diizinkan'], 400);
            }

            $sanitized = strip_tags($value);
            $updated = $skill->update([$field => $sanitized]);

            if ($updated) {
                return response()->json(['success' => true, 'message' => 'Skill berhasil diperbarui', 'data' => $skill->fresh()]);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan perubahan'], 500);
            }
        }

        // -----------------------
        // ACHIEVEMENTS (hasMany)
        // -----------------------
        if ($request->section === 'achievements') {
            $request->validate(['id' => 'required|integer']);
            $id = $request->id;

            $ach = \App\Models\Achievement::where('id', $id)
                ->where('curriculum_vitae_user_id', $cv->id)
                ->first();

            if (!$ach) {
                return response()->json(['success' => false, 'message' => 'Achievement tidak ditemukan atau bukan milik CV ini'], 404);
            }

            $allowedFields = ['achievement_name', 'date_achievement', 'description_achievement'];

            if (!in_array($field, $allowedFields, true)) {
                return response()->json(['success' => false, 'message' => 'Field achievements tidak diizinkan'], 400);
            }

            $sanitized = ($field === 'description_achievement')
                ? strip_tags($value, '<ul><li><br><p><strong><em><b>')
                : strip_tags($value);

            $updated = $ach->update([$field => $sanitized]);

            if ($updated) {
                return response()->json(['success' => true, 'message' => 'Achievement berhasil diperbarui', 'data' => $ach->fresh()]);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan perubahan'], 500);
            }
        }

        // -----------------------
        // LANGUAGES (hasMany)
        // -----------------------
        if ($request->section === 'languages') {
            $request->validate(['id' => 'required|integer']);
            $id = $request->id;

            $lang = \App\Models\Language::where('id', $id)
                ->where('curriculum_vitae_user_id', $cv->id)
                ->first();

            if (!$lang) {
                return response()->json(['success' => false, 'message' => 'Language tidak ditemukan atau bukan milik CV ini'], 404);
            }

            $allowedFields = ['language_name', 'proficiency'];

            if (!in_array($field, $allowedFields, true)) {
                return response()->json(['success' => false, 'message' => 'Field languages tidak diizinkan'], 400);
            }

            $sanitized = strip_tags($value);

            $updated = $lang->update([$field => $sanitized]);

            if ($updated) {
                return response()->json(['success' => true, 'message' => 'Language berhasil diperbarui', 'data' => $lang->fresh()]);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan perubahan'], 500);
            }
        }

        // -----------------------
        // LINKS (hasMany)
        // -----------------------
        if ($request->section === 'links') {
            $request->validate(['id' => 'required|integer']);
            $id = $request->id;

            $link = \App\Models\SocialMedia::where('id', $id)
                ->where('curriculum_vitae_user_id', $cv->id)
                ->first();

            if (!$link) {
                return response()->json(['success' => false, 'message' => 'Link tidak ditemukan atau bukan milik CV ini'], 404);
            }

            $allowedFields = ['link_name', 'url'];

            if (!in_array($field, $allowedFields, true)) {
                return response()->json(['success' => false, 'message' => 'Field links tidak diizinkan'], 400);
            }

            $sanitized = strip_tags($value);

            $updated = $link->update([$field => $sanitized]);

            if ($updated) {
                return response()->json(['success' => true, 'message' => 'Link berhasil diperbarui', 'data' => $link->fresh()]);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan perubahan'], 500);
            }
        }

        // -----------------------
        // ORGANIZATIONS (hasMany)
        // -----------------------
        if ($request->section === 'organizations') {
            $request->validate(['id' => 'required|integer']);
            $id = $request->id;

            $org = \App\Models\Organization::where('id', $id)
                ->where('curriculum_vitae_user_id', $cv->id)
                ->first();

            if (!$org) {
                return response()->json(['success' => false, 'message' => 'Organization tidak ditemukan atau bukan milik CV ini'], 404);
            }

            $allowedFields = ['organization_name', 'position_organization', 'start_date', 'end_date', 'description'];

            if (!in_array($field, $allowedFields, true)) {
                return response()->json(['success' => false, 'message' => 'Field organizations tidak diizinkan'], 400);
            }

            $sanitized = ($field === 'description')
                ? strip_tags($value, '<ul><li><br><p><strong><em><b>')
                : strip_tags($value);

            $updated = $org->update([$field => $sanitized]);

            if ($updated) {
                return response()->json(['success' => true, 'message' => 'Organization berhasil diperbarui', 'data' => $org->fresh()]);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan perubahan'], 500);
            }
        }
        return response()->json(['success' => false, 'message' => 'Section tidak dikenali'], 400);
    }

    // Daftar custom section yg diminta template + status isi/belum untuk CV user
    public function customIndex(CurriculumVitaeUser $curriculumVitaeUser)
    {
        $tpl = $curriculumVitaeUser->templateCurriculumVitae;
        $layout = $tpl?->layout_json ?? [];

        // Ambil hanya section yang key-nya diawali "custom"
        $customConfigs = collect($layout)->filter(function ($it) {
            $key = $it['key'] ?? '';
            return is_string($key) && Str::startsWith($key, 'custom');
        })->values();

        // existing records by section_key
        $existing = $curriculumVitaeUser->customSections()->get()->keyBy('section_key');

        return view('pelamar.curriculum_vitae.custom.index', [
            'curriculumVitaeUser' => $curriculumVitaeUser,
            'customConfigs' => $customConfigs,
            'existing' => $existing,
        ]);
    }

    // Form create untuk 1 section_key custom (sesuai layout_json)
    public function customCreate(CurriculumVitaeUser $curriculumVitaeUser, string $section_key)
    {
        $tpl = $curriculumVitaeUser->templateCurriculumVitae;
        $layout = $tpl?->layout_json ?? [];
        $config = collect($layout)->firstWhere('key', $section_key);

        if (!$config) {
            abort(404, 'Section tidak ditemukan di template.');
        }

        // prefer title/subtitle di layout_json; fallback ke null
        $defaultTitle = $config['title'] ?? ($config['section_title'] ?? null);
        $defaultSubtitle = $config['subtitle'] ?? null;

        return view('pelamar.curriculum_vitae.custom.create', [
            'curriculumVitaeUser' => $curriculumVitaeUser,
            'sectionKey' => $section_key,
            'defaultTitle' => $defaultTitle,
            'defaultSubtitle' => $defaultSubtitle,
        ]);
    }

    // Simpan/Upsert isi custom section untuk 1 section_key
    public function customStore(Request $request, CurriculumVitaeUser $curriculumVitaeUser, string $section_key)
    {
        $validated = $request->validate([
            'section_title' => ['nullable', 'string', 'max:255'],
            'subtitle'      => ['nullable', 'string', 'max:255'],
            'items'         => ['nullable', 'array'],
            'items.*.title' => ['nullable', 'string', 'max:255'],
            'items.*.meta'  => ['nullable', 'string', 'max:255'],
            'items.*.desc'  => ['nullable', 'string'], // boleh HTML
        ]);

        // Normalisasi payload
        $payload = ['items' => []];
        foreach (($validated['items'] ?? []) as $it) {
            if (!empty($it['title']) || !empty($it['meta']) || !empty($it['desc'])) {
                $payload['items'][] = [
                    'title' => $it['title'] ?? null,
                    'meta'  => $it['meta'] ?? null,
                    'desc'  => $it['desc'] ?? null,
                ];
            }
        }

        // Upsert by (cv_user_id, section_key)
        CustomSection::updateOrCreate(
            [
                'curriculum_vitae_user_id' => $curriculumVitaeUser->id,
                'section_key' => $section_key,
            ],
            [
                'section_title' => $validated['section_title'] ?? null,
                'subtitle'      => $validated['subtitle'] ?? null,
                'payload'       => $payload,
            ]
        );

        return redirect()
            ->route('pelamar.curriculum_vitae.custom.index', $curriculumVitaeUser->id)
            ->with('success', 'Custom section disimpan.');
    }

    public function customEdit(CurriculumVitaeUser $curriculumVitaeUser, CustomSection $custom_section)
    {
        // pastikan milik CV ini
        abort_if($custom_section->curriculum_vitae_user_id !== $curriculumVitaeUser->id, 404);

        $tpl = $curriculumVitaeUser->templateCurriculumVitae;
        $layout = $tpl?->layout_json ?? [];
        $config = collect($layout)->firstWhere('key', $custom_section->section_key);

        $defaultTitle = $config['title'] ?? ($config['section_title'] ?? null);
        $defaultSubtitle = $config['subtitle'] ?? null;

        return view('pelamar.curriculum_vitae.custom.edit', [
            'curriculumVitaeUser' => $curriculumVitaeUser,
            'customSection' => $custom_section,
            'defaultTitle' => $defaultTitle,
            'defaultSubtitle' => $defaultSubtitle,
        ]);
    }

    public function customUpdate(Request $request, CurriculumVitaeUser $curriculumVitaeUser, CustomSection $custom_section)
    {
        abort_if($custom_section->curriculum_vitae_user_id !== $curriculumVitaeUser->id, 404);

        $validated = $request->validate([
            'section_title' => ['nullable', 'string', 'max:255'],
            'subtitle'      => ['nullable', 'string', 'max:255'],
            'items'         => ['nullable', 'array'],
            'items.*.title' => ['nullable', 'string', 'max:255'],
            'items.*.meta'  => ['nullable', 'string', 'max:255'],
            'items.*.desc'  => ['nullable', 'string'],
        ]);

        $payload = ['items' => []];
        foreach (($validated['items'] ?? []) as $it) {
            if (!empty($it['title']) || !empty($it['meta']) || !empty($it['desc'])) {
                $payload['items'][] = [
                    'title' => $it['title'] ?? null,
                    'meta'  => $it['meta'] ?? null,
                    'desc'  => $it['desc'] ?? null,
                ];
            }
        }

        $custom_section->update([
            'section_title' => $validated['section_title'] ?? null,
            'subtitle'      => $validated['subtitle'] ?? null,
            'payload'       => $payload,
        ]);

        return redirect()
            ->route('pelamar.curriculum_vitae.custom.index', $curriculumVitaeUser->id)
            ->with('success', 'Custom section diperbarui.');
    }

    public function customDestroy(CurriculumVitaeUser $curriculumVitaeUser, CustomSection $custom_section)
    {
        abort_if($custom_section->curriculum_vitae_user_id !== $curriculumVitaeUser->id, 404);
        $custom_section->delete();

        return redirect()
            ->route('pelamar.curriculum_vitae.custom.index', $curriculumVitaeUser->id)
            ->with('success', 'Custom section dihapus.');
    }

    public function saveCv(Request $request)
    {
        // LOG awal request untuk debugging
        Log::info('saveCv called', [
            'headers' => $request->headers->all(),
            'files_keys' => array_keys($request->files->all()),
            'inputs' => $request->except(['cv_file'])
        ]);

        // VALIDASI: wajib ada file & template_id yang valid
        $validator = Validator::make($request->all(), [
            'cv_file'    => 'required|file|mimes:pdf|max:20480', // max 20MB
            'template_id' => 'required|exists:template_curriculum_vitaes,id'
        ]);

        if ($validator->fails()) {
            Log::warning('saveCv validation failed', $validator->errors()->toArray());
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            // ambil file
            $file = $request->file('cv_file');
            if (!$file) {
                Log::error('saveCv: file was null after validation (weird)', []);
                return response()->json(['success' => false, 'message' => 'File tidak tersedia setelah validasi'], 400);
            }

            // nama file
            $userId = auth()->id() ?? 'guest';
            $fileName = 'cv_' . $userId . '_' . time() . '.pdf';

            // simpan ke storage/app/public/applicants
            $filePath = $file->storeAs('applicants', $fileName, 'public'); // returns path like 'applicants/xxx.pdf'

            // SIMPAN ke DB dengan kondisi unik user+template (update jika sudah ada)
            $cv = CurriculumVitaeUser::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'template_curriculum_vitae_id' => $request->input('template_id')
                ],
                [
                    'pdf_file' => $filePath,
                    'pdf_filename' => $fileName
                ]
            );

            Log::info('saveCv success', ['cv_id' => $cv->id, 'path' => $filePath]);

            return response()->json(['success' => true, 'message' => 'CV berhasil disimpan', 'data' => $cv], 200);
        } catch (\Throwable $e) {
            Log::error('saveCv exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
