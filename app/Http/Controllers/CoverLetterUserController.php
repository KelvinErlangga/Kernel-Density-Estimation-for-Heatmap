<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddParagrafPembukaRequest;
use App\Http\Requests\AddParagrafPenutupRequest;
use App\Http\Requests\AddParagrafUtamaRequest;
use App\Http\Requests\AddProfileCompanyCoverLetterRequest;
use App\Http\Requests\AddProfileCoverLetterRequest;
use App\Http\Requests\AddTandaTanganRequest;
use App\Http\Requests\StoreCoverLetterUserRequest;
use App\Models\CoverLetterUser;
use App\Models\TemplateCoverLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CoverLetterUserController extends Controller
{
    // tampil data template cl
    public function index()
    {
        $templateCoverLetter = TemplateCoverLetter::getAllTemplateCoverLetter();

        return view('pelamar.cover_letter.index', compact('templateCoverLetter'));
    }

    // tambah database id template cl dan user id
    public function store(StoreCoverLetterUserRequest $request)
    {
        $user = Auth::user();

        // Validasi input
        $validated = $request->validated();
        $validated['user_id'] = $user->id;

        $adaCLUser = CoverLetterUser::findByUserIdAndTemplateId(
            $validated['user_id'],
            $validated['template_cover_letter_id']
        );

        if ($adaCLUser) {
            // Jika sudah ada, langsung redirect ke view selanjutnya
            return redirect()->route('pelamar.cover_letter.profile.index', $adaCLUser);
        }

        // Jika belum ada, simpan data baru
        $newCLUser = null;
        DB::transaction(function () use ($validated, &$newCLUser) {
            $newCLUser = CoverLetterUser::create($validated);
        });

        return redirect()->route('pelamar.cover_letter.profile.index', $newCLUser);
    }

    // tampil form input profile
    public function getProfile(CoverLetterUser $coverLetterUser)
    {
        return view('pelamar.cover_letter.profile.index', compact('coverLetterUser'));
    }

    // tambah data profile kedalam database
    public function addProfile(AddProfileCoverLetterRequest $request, CoverLetterUser $coverLetterUser)
    {
        DB::transaction(function () use ($request, $coverLetterUser) {
            $coverLetterUser->personalCoverLetter()->delete();

            $validated = $request->validated();

            $coverLetterUser->personalCoverLetter()->create($validated);
        });

        return redirect()->route('pelamar.cover_letter.profile_company.index', $coverLetterUser->id);
    }

    // tampil form input profile company
    public function getProfileCompany(CoverLetterUser $coverLetterUser)
    {
        return view('pelamar.cover_letter.profile_company.index', compact('coverLetterUser'));
    }

    // tambah data profile kedalam database
    public function addProfileCompany(AddProfileCompanyCoverLetterRequest $request, CoverLetterUser $coverLetterUser)
    {
        DB::transaction(function () use ($request, $coverLetterUser) {
            $coverLetterUser->personalCompanyCoverLetter()->delete();

            $validated = $request->validated();

            $coverLetterUser->personalCompanyCoverLetter()->create($validated);
        });

        return redirect()->route('pelamar.cover_letter.paragraf_pembuka.index', $coverLetterUser->id);
    }

    // tampil form input paragraf pembuka
    public function getParagrafPembuka(CoverLetterUser $coverLetterUser)
    {
        return view('pelamar.cover_letter.paragraf_pembuka.index', compact('coverLetterUser'));
    }

    // tambah data paragraf pembuka kedalam database
    public function addParagrafPembuka(AddParagrafPembukaRequest $request, CoverLetterUser $coverLetterUser)
    {
        DB::transaction(function () use ($request, $coverLetterUser) {
            $coverLetterUser->paragrafPembukaCoverLetter()->delete();

            $validated = $request->validated();

            $coverLetterUser->paragrafPembukaCoverLetter()->create($validated);
        });

        return redirect()->route('pelamar.cover_letter.paragraf_utama.index', $coverLetterUser->id);
    }

    // tampil form input paragraf utama
    public function getParagrafUtama(CoverLetterUser $coverLetterUser)
    {
        return view('pelamar.cover_letter.paragraf_utama.index', compact('coverLetterUser'));
    }

    // tambah data paragraf utama kedalam database
    public function addParagrafUtama(AddParagrafUtamaRequest $request, CoverLetterUser $coverLetterUser)
    {
        DB::transaction(function () use ($request, $coverLetterUser) {
            $coverLetterUser->paragrafUtamaCoverLetter()->delete();

            $validated = $request->validated();

            $coverLetterUser->paragrafUtamaCoverLetter()->create($validated);
        });

        return redirect()->route('pelamar.cover_letter.paragraf_penutup.index', $coverLetterUser->id);
    }

    // tampil form input paragraf penutup
    public function getParagrafPenutup(CoverLetterUser $coverLetterUser)
    {
        return view('pelamar.cover_letter.paragraf_penutup.index', compact('coverLetterUser'));
    }

    // tambah data paragraf penutup kedalam database
    public function addParagrafPenutup(AddParagrafPenutupRequest $request, CoverLetterUser $coverLetterUser)
    {
        DB::transaction(function () use ($request, $coverLetterUser) {
            $coverLetterUser->paragrafPenutupCoverLetter()->delete();

            $validated = $request->validated();

            $coverLetterUser->paragrafPenutupCoverLetter()->create($validated);
        });

        return redirect()->route('pelamar.cover_letter.tanda_tangan.index', $coverLetterUser->id);
    }

    // tampil form input tanda tangan
    public function getTandaTangan(CoverLetterUser $coverLetterUser)
    {
        return view('pelamar.cover_letter.tanda_tangan.index', compact('coverLetterUser'));
    }

    // tambah data tanda tangan kedalam database
    public function addTandaTangan(AddTandaTanganRequest $request, CoverLetterUser $coverLetterUser)
    {
        DB::transaction(function () use ($request, $coverLetterUser) {
            $coverLetterUser->tandaTanganCoverLetter()->delete();

            $validated = $request->validated();

            $coverLetterUser->tandaTanganCoverLetter()->create($validated);
        });

        return redirect()->route('pelamar.cover_letter.preview.index', $coverLetterUser->id);
    }

    // tampil preview cl
    public function previewCL(CoverLetterUser $coverLetterUser)
    {
        $templateView = 'pelamar.curriculum_vitae.template.cl_' . $coverLetterUser->template_cover_letter_id;

        // Cek apakah view ada
        if (!view()->exists($templateView)) {
            $templateView = 'pelamar.curriculum_vitae.template.cv_1';
        }

        return view($templateView, compact('coverLetterUser'));
    }
}
