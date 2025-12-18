<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateRecommendedSkillRequest;
use App\Models\Job;
use App\Models\RecommendedSkill;
use App\Models\Skill;
use Illuminate\Support\Facades\DB;

class RecommendedSkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua termasuk soft deleted + eager load relasi biar tidak N+1
        $recommended_skills = RecommendedSkill::withTrashed()
            ->with(['job', 'skill'])
            ->get();

        return view('admin.recommended_skills.index', compact('recommended_skills'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jobs = Job::getJob();
        $skills = Skill::getSkill();

        return view('admin.recommended_skills.create', compact('jobs', 'skills'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUpdateRecommendedSkillRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $job_id = $validated['job_id'];
            $skills = $validated['skill_id']; // array (karena STORE)

            foreach ($skills as $skillId) {
                // opsional: cegah duplikat
                RecommendedSkill::withTrashed()->updateOrCreate(
                    ['job_id' => $job_id, 'skill_id' => $skillId],
                    ['deleted_at' => null]
                );
            }
        });

        return redirect()
            ->route('admin.recommended_skills.index')
            ->with('success', 'Rekomendasi keahlian berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RecommendedSkill $recommendedSkill)
    {
        $jobs = Job::getJob();
        $skills = Skill::getSkill();

        // pastikan relasi kebaca
        $recommendedSkill->load(['job', 'skill']);

        return view('admin.recommended_skills.edit', compact('recommendedSkill', 'jobs', 'skills'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUpdateRecommendedSkillRequest $request, RecommendedSkill $recommendedSkill)
    {
        DB::transaction(function () use ($request, $recommendedSkill) {
            $validated = $request->validated();

            // UPDATE: skill_id single value (bukan array)
            $recommendedSkill->update([
                'job_id' => $validated['job_id'],
                'skill_id' => $validated['skill_id'],
            ]);
        });

        return redirect()
            ->route('admin.recommended_skills.index')
            ->with('success', 'Rekomendasi keahlian berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(RecommendedSkill $recommendedSkill)
    {
        $recommendedSkill->delete();

        return redirect()
            ->route('admin.recommended_skills.index')
            ->with('success', 'Rekomendasi keahlian berhasil dinonaktifkan.');
    }

    /**
     * Restore soft deleted resource.
     */
    public function restore($id)
    {
        $recommendedSkill = RecommendedSkill::withTrashed()->findOrFail($id);
        $recommendedSkill->restore();

        return redirect()
            ->route('admin.recommended_skills.index')
            ->with('success', 'Rekomendasi keahlian berhasil diaktifkan kembali.');
    }
}
