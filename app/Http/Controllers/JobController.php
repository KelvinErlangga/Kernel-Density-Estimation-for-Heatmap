<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateJobRequest;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobs = Job::getJob();

        return view('admin.jobs.index', compact('jobs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateJobRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated) {
                Job::create($validated);
            });

            return redirect()->route('admin.jobs.index')
                ->with('success', 'Data pekerjaan berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data pekerjaan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function edit(Job $job)
    {
        return view('admin.jobs.edit', compact('job'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateJobRequest $request, Job $job)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $job) {
                $job->update($validated);
            });

            return redirect()->route('admin.jobs.index')
                ->with('success', 'Data pekerjaan berhasil diubah!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengubah data pekerjaan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Job $job)
    // {
    //     DB::transaction(function () use ($job) {
    //         $job->delete();
    //     });

    //     return redirect()->route('admin.jobs.index');
    // }

    public function destroy(Job $job)
    {
        try {
            DB::transaction(function () use ($job) {
                $job->delete(); // soft delete
            });

            return redirect()->route('admin.jobs.index')
                ->with('success', 'Data pekerjaan berhasil dinonaktifkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menonaktifkan data pekerjaan: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $job = Job::withTrashed()->findOrFail($id);
            $job->restore();

            return redirect()->route('admin.jobs.index')
                ->with('success', 'Data pekerjaan berhasil diaktifkan kembali.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengaktifkan data pekerjaan: ' . $e->getMessage());
        }
    }

    public function getJobSkills()
    {
        $jobs = Job::with('recommendedSkill.skill')->get();

        $jobSkills = [];
        foreach ($jobs as $job) {
            $jobSkills[$job->job_name] = $job->recommendedSkill->pluck('skill.skill_name')->toArray();
        }

        return response()->json($jobSkills);
    }
}
