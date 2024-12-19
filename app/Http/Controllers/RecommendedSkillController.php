<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateRecommendedSkillRequest;
use App\Models\Job;
use App\Models\RecommendedSkill;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecommendedSkillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $recommended_skills = RecommendedSkill::getRecommendedSkill();

        return view('admin.recommended_skills.index', compact('recommended_skills'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jobs = Job::getJob();
        $skills = Skill::getSkill();

        return view('admin.recommended_skills.create', compact('jobs', 'skills'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateRecommendedSkillRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $newRecommendedSkill = RecommendedSkill::create($validated);
        });

        return redirect()->route('admin.recommended_skills.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RecommendedSkill  $recommendedSkill
     * @return \Illuminate\Http\Response
     */
    public function edit(RecommendedSkill $recommendedSkill)
    {
        $jobs = Job::getJob();
        $skills = Skill::getSkill();

        return view('admin.recommended_skills.edit', compact('recommendedSkill', 'jobs', 'skills'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RecommendedSkill  $recommendedSkill
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateRecommendedSkillRequest $request, RecommendedSkill $recommendedSkill)
    {
        DB::transaction(function () use ($request, $recommendedSkill) {
            $validated = $request->validated();

            $recommendedSkill->update($validated);
        });

        return redirect()->route('admin.recommended_skills.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RecommendedSkill  $recommendedSkill
     * @return \Illuminate\Http\Response
     */
    public function destroy(RecommendedSkill $recommendedSkill)
    {
        DB::transaction(function () use ($recommendedSkill) {
            $recommendedSkill->delete();
        });

        return redirect()->route('admin.recommended_skills.index');
    }
}
