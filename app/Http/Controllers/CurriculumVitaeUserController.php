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
use App\Models\TemplateCurriculumVitae;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Link;

class CurriculumVitaeUserController extends Controller
{
    public function index()
    {
        $templateCurriculumVitae = TemplateCurriculumVitae::getAllTemplateCV();

        return view('pelamar.curriculum_vitae.index', compact('templateCurriculumVitae'));
    }

    // tambah database id template cv dan user id
    // public function store(StoreCurriculumVitaeUserRequest $request)
    // {
    //     $user = Auth::user();

    //     // Validasi input
    //     $validated = $request->validated();
    //     $validated['user_id'] = $user->id;

    //     $adaCVUser = CurriculumVitaeUser::findByUserIdAndTemplateId(
    //         $validated['user_id'],
    //         $validated['template_curriculum_vitae_id']
    //     );

    //     if ($adaCVUser) {
    //         // Jika sudah ada, langsung redirect ke view selanjutnya
    //         return redirect()->route('pelamar.curriculum_vitae.profile.index', $adaCVUser);
    //         // return redirect()->route('pelamar.curriculum_vitae.preview.index', $adaCVUser);
    //     }

    //     // Jika belum ada, simpan data baru
    //     $newCVUser = null;
    //     DB::transaction(function () use ($validated, &$newCVUser) {
    //         $newCVUser = CurriculumVitaeUser::create($validated);
    //     });

    //     return redirect()->route('pelamar.curriculum_vitae.profile.index', $newCVUser);
    //     // return redirect()->route('pelamar.curriculum_vitae.preview.index', $newCVUser);
    // }

    public function store(StoreCurriculumVitaeUserRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();
        $validated['user_id'] = $user->id;

        $ada = CurriculumVitaeUser::findByUserIdAndTemplateId(
            $validated['user_id'],
            $validated['template_curriculum_vitae_id']
        );
        if ($ada) {
            return redirect()->route('pelamar.curriculum_vitae.profile.index', $ada);
        }

        $cv = null;
        DB::transaction(function () use ($validated, &$cv) {
            $cv = CurriculumVitaeUser::create($validated);
        });

        $this->seedCustomSectionsFromTemplate($cv);

        return redirect()->route('pelamar.curriculum_vitae.profile.index', $cv);
    }

    /**
     * Membuat record custom_sections default berdasarkan layout_json template.
     */
    private function seedCustomSectionsFromTemplate(CurriculumVitaeUser $cv): void
    {
        $tpl = $cv->template;
        if (!$tpl) return;

        $layout = $tpl->layoutArray();
        $order  = 1;

        foreach ($layout as $item) {
            $key = $item['key'] ?? null;
            if (!$key) continue;

            $base = preg_replace('/-(ats|kreatif)$/i', '', $key);

            if ($base === 'custom') {
                $title = $item['title'] ?? $item['section_title'] ?? 'Custom Section';
                $body  = data_get($item, 'payload.body')
                    ?? data_get($item, 'body')
                    ?? 'Tulis deskripsi di sini…';

                $cv->customSections()->create([
                    'section_key'   => 'custom_text',
                    'section_title' => $title,
                    'subtitle'      => $item['subtitle'] ?? null,
                    'payload'       => ['body' => $body],
                    'sort_order'    => $order,
                ]);
            }

            $order++;
        }
    }

    // Buat section baru (default: Text Section)
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

    // Update inline (judul / isi)
    public function updateCustomSection(Request $request, CurriculumVitaeUser $curriculumVitaeUser, CustomSection $section)
    {
        abort_unless($section->curriculum_vitae_user_id === $curriculumVitaeUser->id, 403);

        $field = $request->input('field');
        $value = $request->input('value');

        if ($field === 'section_title') {
            $section->section_title = $value;
        } elseif (str_starts_with($field, 'payload.')) {
            $path = substr($field, 8);
            $payload = $section->payload ?? [];
            data_set($payload, $path, $value);
            $section->payload = $payload;
        } elseif ($field === 'subtitle') {
            $section->subtitle = $value;
        }

        $section->save();

        return response()->json(['success' => true]);
    }

    // Hapus section
    public function deleteCustomSection(CurriculumVitaeUser $curriculumVitaeUser, CustomSection $section)
    {
        abort_unless($section->curriculum_vitae_user_id === $curriculumVitaeUser->id, 403);
        $section->delete();
        return response()->json(['success' => true]);
    }

    // Reorder section (khusus custom)
    public function reorderCustomSections(Request $request, CurriculumVitaeUser $curriculumVitaeUser)
    {
        $ids = $request->input('ids', []);

        DB::transaction(function () use ($ids, $curriculumVitaeUser) {
            foreach ($ids as $i => $id) {
                $curriculumVitaeUser->customSections()
                    ->where('id', $id)
                    ->update(['sort_order' => $i + 1]);
            }
        });

        return response()->json(['success' => true]);
    }

    // Ambil daftar key yang diizinkan dari layout_json template
    private function allowedKeysForTemplate(?\App\Models\TemplateCurriculumVitae $template): array
    {
        if (!$template) return [];

        $raw = $template->layout_json;

        $layout = is_string($raw) ? (json_decode($raw, true) ?: []) : (is_array($raw) ? $raw : []);

        return collect($layout)
            ->pluck('key')
            ->filter(fn($k) => is_string($k) && $k !== '')
            ->values()
            ->all();
    }

    // tampil form input profile
    public function getProfile(CurriculumVitaeUser $curriculumVitaeUser)
    {
        $curriculumVitaeUser->load('templateCV', 'personalDetail');
        $allowedKeys = $this->allowedKeysForTemplate($curriculumVitaeUser->templateCV);

        abort_unless(in_array('personal_detail', $allowedKeys, true), 404);

        return view('pelamar.curriculum_vitae.profile.index', [
            'curriculumVitaeUser' => $curriculumVitaeUser,
            'allowedKeys'         => $allowedKeys,
        ]);
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
                $validated['avatar_curriculum_vitae'] = $existingAvatar;
            }

            $curriculumVitaeUser->personalDetail()->create($validated);
        });

        return redirect()->route('pelamar.curriculum_vitae.experience.index', $curriculumVitaeUser->id);
    }

    // public function addProfile(AddUpdateProfileCurriculumVitaeRequest $request, CurriculumVitaeUser $curriculumVitaeUser)
    // {
    //     DB::transaction(function () use ($request, $curriculumVitaeUser) {

    //         $existingAvatar = optional($curriculumVitaeUser->personalDetail)->avatar_curriculum_vitae;

    //         $curriculumVitaeUser->personalDetail()->delete();

    //         $validated = $request->validated();

    //         if ($request->hasFile('avatar_curriculum_vitae')) {
    //             $avatar_curriculum_vitaePath = $request->file('avatar_curriculum_vitae')->store('avatar_curriculum_vitae', 'public');
    //             $validated['avatar_curriculum_vitae'] = $avatar_curriculum_vitaePath;
    //         } else {
    //             // Jika tidak ada file baru, gunakan avatar yang sudah ada
    //             $validated['avatar_curriculum_vitae'] = $existingAvatar;
    //         }

    //         $curriculumVitaeUser->personalDetail()->create($validated);
    //     });

    //     return redirect()->route('pelamar.curriculum_vitae.detail_section.index', $curriculumVitaeUser->id);
    // }

    // tampil data pengalaman kerja
    public function getDetailSection(CurriculumVitaeUser $curriculumVitaeUser)
    {
        $curriculumVitaeUser->load([
            'experiences',
            'educations',
            'languages',
            'skills',
            'organizations',
            'achievements',
            'links',
            'customSections',
            'templateCV',
        ]);

        $allowedKeys = $this->allowedKeysForTemplate($curriculumVitaeUser->templateCV);

        return view('pelamar.curriculum_vitae.detail_section.index', [
            'curriculumVitaeUser' => $curriculumVitaeUser,
            'allowedKeys'         => $allowedKeys,
        ]);
    }

    // tampil data pengalaman kerja
    public function getExperience(CurriculumVitaeUser $curriculumVitaeUser)
    {
        $curriculumVitaeUser->load('templateCV', 'experiences');
        $allowedKeys = $this->allowedKeysForTemplate($curriculumVitaeUser->templateCV);
        abort_unless(in_array('experiences', $allowedKeys, true), 404);

        return view('pelamar.curriculum_vitae.experience.index', [
            'curriculumVitaeUser' => $curriculumVitaeUser,
            'allowedKeys'         => $allowedKeys,
        ]);
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
        $curriculumVitaeUser->load('templateCV', 'educations');
        $allowedKeys = $this->allowedKeysForTemplate($curriculumVitaeUser->templateCV);
        abort_unless(in_array('educations', $allowedKeys, true), 404);

        return view('pelamar.curriculum_vitae.education.index', [
            'curriculumVitaeUser' => $curriculumVitaeUser,
            'allowedKeys'         => $allowedKeys,
        ]);
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

            $validated['is_current'] = $request->boolean('is_current');

            $validated['start_date'] = Carbon::createFromFormat('Y-m', $validated['start_date'])
                ->startOfMonth()
                ->toDateString();

            if ($validated['is_current']) {
                $validated['end_date'] = null;
            } else {
                $validated['end_date'] = !empty($validated['end_date'])
                    ? Carbon::createFromFormat('Y-m', $validated['end_date'])->startOfMonth()->toDateString()
                    : null;
            }

            $curriculumVitaeUser->educations()->create($validated);
        });

        return redirect()->route('pelamar.curriculum_vitae.education.index', $curriculumVitaeUser->id)
            ->with('success', 'Pendidikan berhasil ditambahkan.');
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

            $validated['is_current'] = $request->boolean('is_current');

            $validated['start_date'] = Carbon::createFromFormat('Y-m', $validated['start_date'])
                ->startOfMonth()
                ->toDateString();

            if ($validated['is_current']) {
                $validated['end_date'] = null;
            } else {
                $validated['end_date'] = !empty($validated['end_date'])
                    ? Carbon::createFromFormat('Y-m', $validated['end_date'])->startOfMonth()->toDateString()
                    : null;
            }

            $education->update($validated);
        });

        return redirect()->route('pelamar.curriculum_vitae.education.index', $curriculumVitaeUser->id)
            ->with('success', 'Pendidikan berhasil diperbarui.');
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
        $curriculumVitaeUser->load('templateCV', 'languages');
        $allowedKeys = $this->allowedKeysForTemplate($curriculumVitaeUser->templateCV);
        abort_unless(in_array('languages', $allowedKeys, true), 404);

        return view('pelamar.curriculum_vitae.language.index', [
            'curriculumVitaeUser' => $curriculumVitaeUser,
            'allowedKeys'         => $allowedKeys,
        ]);
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
        $curriculumVitaeUser->load('templateCV', 'skills');
        $allowedKeys = $this->allowedKeysForTemplate($curriculumVitaeUser->templateCV);
        abort_unless(in_array('skills', $allowedKeys, true), 404);

        return view('pelamar.curriculum_vitae.skill.index', [
            'curriculumVitaeUser' => $curriculumVitaeUser,
            'allowedKeys'         => $allowedKeys,
        ]);
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
        $curriculumVitaeUser->load('templateCV', 'organizations');
        $allowedKeys = $this->allowedKeysForTemplate($curriculumVitaeUser->templateCV);
        abort_unless(in_array('organizations', $allowedKeys, true), 404);

        return view('pelamar.curriculum_vitae.organization.index', [
            'curriculumVitaeUser' => $curriculumVitaeUser,
            'allowedKeys'         => $allowedKeys,
        ]);
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
        $curriculumVitaeUser->load('templateCV', 'achievements');
        $allowedKeys = $this->allowedKeysForTemplate($curriculumVitaeUser->templateCV);
        abort_unless(in_array('achievements', $allowedKeys, true), 404);

        return view('pelamar.curriculum_vitae.achievement.index', [
            'curriculumVitaeUser' => $curriculumVitaeUser,
            'allowedKeys'         => $allowedKeys,
        ]);
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
        $curriculumVitaeUser->load('templateCV', 'links');
        $allowedKeys = $this->allowedKeysForTemplate($curriculumVitaeUser->templateCV);
        abort_unless(in_array('links', $allowedKeys, true), 404);

        return view('pelamar.curriculum_vitae.social_media.index', [
            'curriculumVitaeUser' => $curriculumVitaeUser,
            'allowedKeys'         => $allowedKeys,
        ]);
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

    public function deleteSocialMedia(CurriculumVitaeUser $curriculumVitaeUser, Link $link)
    {
        if ($link->linkable_id !== $curriculumVitaeUser->id || $link->linkable_type !== CurriculumVitaeUser::class) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        DB::transaction(function () use ($link) {
            $link->delete();
        });

        return redirect()->route('pelamar.curriculum_vitae.social_media.index', $curriculumVitaeUser->id)
            ->with('success', 'Link berhasil dihapus.');
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

        if (is_array($template->layout_json)) {
            $layout = $template->layout_json;
        } elseif (is_string($template->layout_json)) {
            $layout = json_decode($template->layout_json, true) ?? [];
        } else {
            $layout = [];
        }

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

            if ($field === 'name') {
                $parts = preg_split('/\s+/', trim(strip_tags($value)), 2, PREG_SPLIT_NO_EMPTY);
                $personal->update([
                    'first_name_curriculum_vitae' => $parts[0] ?? '',
                    'last_name_curriculum_vitae'  => $parts[1] ?? '',
                ]);
                return response()->json(['success' => true, 'message' => 'Nama berhasil diperbarui']);
            }

            if (in_array($field, $allowedPersonal, true)) {
                $sanitized = in_array($field, ['personal_summary'], true) ? strip_tags($value, '<p><br><strong><em><ul><li>') : strip_tags($value);
                $personal->update([$field => $sanitized]);
                return response()->json(['success' => true, 'message' => 'Perubahan personal detail disimpan']);
            }

            return response()->json(['success' => false, 'message' => 'Field personal_detail tidak diizinkan'], 400);
        }

        if ($request->section === 'experiences') {
            $request->validate(['id' => 'required|integer']);
            $id = $request->id;

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

            if ($field === 'description_experience') {
                $sanitized = strip_tags($value, '<ul><li><br><p><strong><em><b>');
            } else {
                $sanitized = strip_tags($value);
            }

            $updated = $exp->update([$field => $sanitized]);

            if ($updated) {
                return response()->json(['success' => true, 'message' => 'Experience berhasil diperbarui', 'data' => $exp->fresh()]);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan perubahan'], 500);
            }
        }

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

    public function customIndex(CurriculumVitaeUser $curriculumVitaeUser)
    {
        $tpl = $curriculumVitaeUser->templateCurriculumVitae;
        $layout = $tpl?->layout_json ?? [];

        $customConfigs = collect($layout)->filter(function ($it) {
            $key = $it['key'] ?? '';
            return is_string($key) && Str::startsWith($key, 'custom');
        })->values();

        $existing = $curriculumVitaeUser->customSections()->get()->keyBy('section_key');

        return view('pelamar.curriculum_vitae.custom.index', [
            'curriculumVitaeUser' => $curriculumVitaeUser,
            'customConfigs' => $customConfigs,
            'existing' => $existing,
        ]);
    }

    public function customCreate(CurriculumVitaeUser $curriculumVitaeUser, string $section_key)
    {
        $tpl = $curriculumVitaeUser->templateCurriculumVitae;
        $layout = $tpl?->layout_json ?? [];
        $config = collect($layout)->firstWhere('key', $section_key);

        if (!$config) {
            abort(404, 'Section tidak ditemukan di template.');
        }

        $defaultTitle = $config['title'] ?? ($config['section_title'] ?? null);
        $defaultSubtitle = $config['subtitle'] ?? null;

        return view('pelamar.curriculum_vitae.custom.create', [
            'curriculumVitaeUser' => $curriculumVitaeUser,
            'sectionKey' => $section_key,
            'defaultTitle' => $defaultTitle,
            'defaultSubtitle' => $defaultSubtitle,
        ]);
    }

    public function customStore(Request $request, CurriculumVitaeUser $curriculumVitaeUser, string $section_key)
    {
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
        Log::info('saveCv called', [
            'headers' => $request->headers->all(),
            'files_keys' => array_keys($request->files->all()),
            'inputs' => $request->except(['cv_file'])
        ]);

        $validator = Validator::make($request->all(), [
            'cv_file'    => 'required|file|mimes:pdf|max:20480',
            'template_id' => 'required|exists:template_curriculum_vitaes,id'
        ]);

        if ($validator->fails()) {
            Log::warning('saveCv validation failed', $validator->errors()->toArray());
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $file = $request->file('cv_file');
            if (!$file) {
                Log::error('saveCv: file was null after validation (weird)', []);
                return response()->json(['success' => false, 'message' => 'File tidak tersedia setelah validasi'], 400);
            }

            $userId = auth()->id() ?? 'guest';
            $fileName = 'cv_' . $userId . '_' . time() . '.pdf';

            $filePath = $file->storeAs('applicants', $fileName, 'public');

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
