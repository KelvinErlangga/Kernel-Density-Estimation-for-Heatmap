<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddHiringRequest;
use App\Models\Applicant;
use App\Models\Hiring;
use App\Models\PersonalCompany;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardCompanyController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $personalCompany = $user->personalCompany;

        $applicants = Applicant::with(['user', 'hiring'])->get();

        $applicantCount = Applicant::all()->count();

        $hiring = Hiring::all()->count();

        return view('perusahaan.dashboard_perusahaan', compact('personalCompany', 'applicants', 'applicantCount', 'hiring'));
    }

    public function getLowongan()
    {
        $user = Auth::user();

        $personalCompany = $user->personalCompany; // Mengambil data perusahaan dari user

        if ($personalCompany) {
            $hirings = $personalCompany->hirings; // Ambil data lowongan dari perusahaan
        } else {
            $hirings = collect(); // Kosongkan jika user tidak memiliki perusahaan
        }

        return view('perusahaan.lowongan.index', compact('hirings'));
    }

    public function editLowongan(Hiring $hiring)
    {
        return view('perusahaan.lowongan.edit', compact('hiring'));
    }

    public function deleteLowongan(Hiring $hiring)
    {
        DB::transaction(function () use ($hiring) {
            $hiring->delete();
        });

        return redirect()->route('perusahaan.lowongan.index');
    }

    public function addLowongan(AddHiringRequest $request)
    {
        $user = Auth::user();

        $personalCompany = $user->personalCompany;

        DB::transaction(function () use ($request, $personalCompany) {
            $validated = $request->validated();

            $personalCompany->hirings()->create($validated);
        });

        return redirect()->route('perusahaan.lowongan.index');
    }

    public function updateLowongan(AddHiringRequest $request, Hiring $hiring)
    {
        DB::transaction(function () use ($request, $hiring) {
            $validated = $request->validated();

            $hiring->update($validated);
        });

        return redirect()->route('perusahaan.lowongan.index');
    }

    public function getKandidat()
    {
        $applicants = Applicant::with(['user', 'hiring'])->get();

        return view('perusahaan.kandidat.index', compact('applicants'));
    }

    public function updateStatus(Request $request, $id)
    {
        // Validasi request
        $request->validate([
            'status' => 'required|in:Pending,Diterima,Ditolak',
        ]);

        // Cari kandidat berdasarkan ID
        $applicant = Applicant::find($id);

        // Update status
        $applicant->status = $request->status;
        $applicant->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('perusahaan.kandidat.index');
    }


    public function deleteKandidat(Applicant $applicant)
    {
        DB::transaction(function () use ($applicant) {
            $applicant->delete();
        });

        return redirect()->route('perusahaan.kandidat.index');
    }
}
