<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTemplateCoverLetterRequest;
use App\Http\Requests\UpdateTemplateCoverLetterRequest;
use App\Models\TemplateCoverLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemplateCoverLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templateCoverLetters = TemplateCoverLetter::getAllTemplateCoverLetter();

        return view('admin.template_cover_letter.index', compact('templateCoverLetters'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.template_cover_letter.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTemplateCoverLetterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTemplateCoverLetterRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            if ($request->hasFile('thumbnail_cover_letter')) {
                $thumbnail_cover_letterPath = $request->file('thumbnail_cover_letter')->store('thumbnail_cover_letter', 'public');
                $validated['thumbnail_cover_letter'] = $thumbnail_cover_letterPath;
            }

            $newTemplateCV = TemplateCoverLetter::create($validated);
        });

        return redirect()->route('admin.template_cover_letter.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TemplateCoverLetter  $templateCoverLetter
     * @return \Illuminate\Http\Response
     */
    public function edit(TemplateCoverLetter $templateCoverLetter)
    {
        return view('admin.template_cover_letter.edit', compact('templateCoverLetter'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTemplateCoverLetterRequest  $request
     * @param  \App\Models\TemplateCoverLetter  $templateCoverLetter
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTemplateCoverLetterRequest $request, TemplateCoverLetter $templateCoverLetter)
    {
        DB::transaction(function () use ($request, $templateCoverLetter) {
            $validated = $request->validated();

            if ($request->hasFile('thumbnail_cover_letter')) {
                $thumbnail_cover_letterPath = $request->file('thumbnail_cover_letter')->store('thumbnail_cover_letter', 'public');
                $validated['thumbnail_cover_letter'] = $thumbnail_cover_letterPath;
            }

            $templateCoverLetter->update($validated);
        });

        return redirect()->route('admin.template_cover_letter.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TemplateCoverLetter  $templateCoverLetter
     * @return \Illuminate\Http\Response
     */
    public function destroy(TemplateCoverLetter $templateCoverLetter)
    {
        DB::transaction(function () use ($templateCoverLetter) {
            $templateCoverLetter->delete();
        });

        return redirect()->route('admin.template_cover_letter.index');
    }
}
