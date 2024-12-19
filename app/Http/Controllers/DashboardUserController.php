<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddSubmitAplicationRequest;
use App\Models\Applicant;
use App\Models\CoverLetterUser;
use App\Models\CurriculumVitaeUser;
use App\Models\Hiring;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardUserController extends Controller
{
    public function index()
    {
        $hirings = Hiring::with('personalCompany')->get();

        $user = Auth::user();

        $pending = Applicant::where('status', 'Pending')->where('user_id', $user->id)->count();
        $diterima = Applicant::where('status', 'Diterima')->where('user_id', $user->id)->count();
        $ditolak = Applicant::where('status', 'Ditolak')->where('user_id', $user->id)->count();

        return view('pelamar.dashboard.dashboard_user', compact('hirings', 'pending', 'diterima', 'ditolak'));
    }

    // lowongan
    public function getLowongan()
    {
        $hirings = Hiring::all();

        return view('pelamar.dashboard.lowongan.index', compact('hirings'));
    }

    public function getShowLowongan($id)
    {
        $user = Auth::user();
        $hiring = Hiring::with('personalCompany')->findOrFail($id);

        // Periksa apakah user sudah melamar
        $hasApplied = $user->applicants()->where('hiring_id', $id)->exists();

        // Periksa apakah deadline sudah lewat
        $isClosed = now()->greaterThan($hiring->deadline_hiring);

        return response()->json([
            'id' => $hiring->id,
            'position_hiring' => $hiring->position_hiring,
            'address_hiring' => $hiring->address_hiring,
            'type_of_work' => $hiring->type_of_work,
            'deadline_hiring' => $hiring->deadline_hiring,
            'gaji' => $hiring->gaji,
            'description_hiring' => $hiring->description_hiring,
            'company_name' => $hiring->personalCompany->name_company ?? 'Tidak Ada Nama Perusahaan',
            'has_applied' => $hasApplied,
            'is_closed' => $isClosed, // Kirim status apakah lowongan sudah ditutup
        ]);
    }

    public function submitApplication(AddSubmitAplicationRequest $request)
    {
        $user = Auth::user();

        DB::transaction(function () use ($request, $user) {

            $validated = $request->validated();

            if ($request->hasFile('file_applicant')) {
                $file_applicantPath = $request->file('file_applicant')->store('applicants', 'public');
                $validated['file_applicant'] = $file_applicantPath;
            }

            $validated['user_id'] = $user->id;
            $validated['status'] = 'Pending';

            $newAplicant = Applicant::create($validated);
        });

        return redirect()->route('pelamar.dashboard.lowongan.index');
    }

    // Curriculum Vitae
    public function getCurriculumVitae()
    {
        $user = Auth::user();

        $curriculumVitaes = CurriculumVitaeUser::getCurriculumVitaeUser($user->id);

        return view('pelamar.dashboard.curriculum_vitae.index', compact('curriculumVitaes'));
    }

    public function deleteCurriculumVitae(CurriculumVitaeUser $curriculumVitaeUser)
    {
        DB::transaction(function () use ($curriculumVitaeUser) {
            $curriculumVitaeUser->delete();
        });

        return redirect()->route('pelamar.dashboard.curriculum_vitae.index');
    }

    // Cover Letter
    public function getCoverLetter()
    {
        $coverLetters = CoverLetterUser::getCoverLetter();

        return view('pelamar.dashboard.cover_letter.index', compact('coverLetters'));
    }

    public function deleteCoverLetter(CoverLetterUser $coverLetterUser)
    {
        DB::transaction(function () use ($coverLetterUser) {
            $coverLetterUser->delete();
        });

        return redirect()->route('pelamar.dashboard.cover_letter.index');
    }

    // akun
    public function getAkun()
    {
        return view('pelamar.dashboard.akun.index');
    }
}
