<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTemplateCurriculumVitaeRequest;
use App\Http\Requests\UpdateTemplateCurriculumVitaeRequest;
use App\Models\TemplateCurriculumVitae;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemplateCurriculumVitaeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templateCurriculumVitaes = TemplateCurriculumVitae::getAllTemplateCV();

        return view('admin.template_curriculum_vitae.index', compact('templateCurriculumVitaes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.template_curriculum_vitae.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTemplateCurriculumVitaeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTemplateCurriculumVitaeRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            if ($request->hasFile('thumbnail_curriculum_vitae')) {
                $thumbnail_curriculum_vitaePath = $request->file('thumbnail_curriculum_vitae')->store('thumbnail_curriculum_vitae', 'public');
                $validated['thumbnail_curriculum_vitae'] = $thumbnail_curriculum_vitaePath;
            }

            $newTemplateCV = TemplateCurriculumVitae::create($validated);
        });

        return redirect()->route('admin.template_curriculum_vitae.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TemplateCurriculumVitae  $templateCurriculumVitae
     * @return \Illuminate\Http\Response
     */
    public function edit(TemplateCurriculumVitae $templateCurriculumVitae)
    {
        return view('admin.template_curriculum_vitae.edit', compact('templateCurriculumVitae'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTemplateCurriculumVitaeRequest  $request
     * @param  \App\Models\TemplateCurriculumVitae  $templateCurriculumVitae
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTemplateCurriculumVitaeRequest $request, TemplateCurriculumVitae $templateCurriculumVitae)
    {
        DB::transaction(function () use ($request, $templateCurriculumVitae) {
            $validated = $request->validated();

            if ($request->hasFile('thumbnail_curriculum_vitae')) {
                $thumbnail_curriculum_vitaePath = $request->file('thumbnail_curriculum_vitae')->store('thumbnail_curriculum_vitae', 'public');
                $validated['thumbnail_curriculum_vitae'] = $thumbnail_curriculum_vitaePath;
            }

            $templateCurriculumVitae->update($validated);
        });

        return redirect()->route('admin.template_curriculum_vitae.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TemplateCurriculumVitae  $templateCurriculumVitae
     * @return \Illuminate\Http\Response
     */
    public function destroy(TemplateCurriculumVitae $templateCurriculumVitae)
    {
        DB::transaction(function () use ($templateCurriculumVitae) {
            $templateCurriculumVitae->delete();
        });

        return redirect()->route('admin.template_curriculum_vitae.index');
    }
}
