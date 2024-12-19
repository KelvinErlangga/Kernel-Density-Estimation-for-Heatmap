<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateSkillRequest;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $skills = Skill::getSkill();

        return view('admin.skills.index', compact('skills'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.skills.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUpdateSkillRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateSkillRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $newSkill = Skill::create($validated);
        });

        return redirect()->route('admin.skills.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    public function edit(Skill $skill)
    {
        return view('admin.skills.edit', compact('skill'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreUpdateSkillRequest  $request
     * @param  \App\Models\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateSkillRequest $request, Skill $skill)
    {
        DB::transaction(function () use ($request, $skill) {
            $validated = $request->validated();

            $skill->update($validated);
        });

        return redirect()->route('admin.skills.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    public function destroy(Skill $skill)
    {
        DB::transaction(function () use ($skill) {
            $skill->delete();
        });

        return redirect()->route('admin.skills.index');
    }
}
