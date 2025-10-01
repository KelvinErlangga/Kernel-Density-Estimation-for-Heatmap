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
        $validated = $request->validated();
        try {
            DB::transaction(function () use ($validated) {
                Skill::create($validated);
            });

            return redirect()->route('admin.skills.index')
                ->with('success', 'Data keahlian berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menambahkan data keahlian: ' . $e->getMessage());
        }
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
        $validated = $request->validated();
        try {
            DB::transaction(function () use ($validated, $skill) {
                $skill->update($validated);
            });

            return redirect()->route('admin.skills.index')
                ->with('success', 'Data keahlian berhasil diubah!'); // <-- disini pesan sukses untuk SweetAlert
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengubah data keahlian: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Skill $skill)
    // {
    //     DB::transaction(function () use ($skill) {
    //         $skill->delete();
    //     });

    //     return redirect()->route('admin.skills.index');
    // }

    public function destroy(Skill $skill)
    {
        try {
            $skill->delete();
            return redirect()->route('admin.skills.index')
                ->with('success', 'Data keahlian berhasil dinonaktifkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menonaktifkan data keahlian: ' . $e->getMessage());
        }
    }

    // Restore / Aktifkan kembali
    public function restore($id)
    {
        try {
            $skill = Skill::withTrashed()->findOrFail($id);
            $skill->restore();
            return redirect()->route('admin.skills.index')
                ->with('success', 'Data keahlian berhasil diaktifkan kembali!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengaktifkan data keahlian: ' . $e->getMessage());
        }
    }
}
