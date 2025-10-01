<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\PersonalDetail;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Support\Facades\Storage;

class ApplicantController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'hiring_id' => 'required|integer',
            'personal_curriculum_vitae_id' => 'required|integer',
        ]);

        // ambil data CV
        $cv = PersonalDetail::findOrFail($request->personal_curriculum_vitae_id);

        // generate PDF dari view preview
        $pdf = PDF::loadView('pelamar.curriculum_vitae.preview.index', compact('cv'));

        // bikin nama file unik
        $fileName = 'cv_' . time() . '.pdf';
        $filePath = 'applicants/' . $fileName; // simpan di storage/applicants/

        // simpan file PDF ke storage/applicants/
        Storage::put($filePath, $pdf->output());

        // simpan ke tabel applicants
        $applicant = Applicant::create([
            'hiring_id' => $request->hiring_id,
            'personal_curriculum_vitae_id' => $cv->id,
            'file_applicant' => $filePath, // simpan path relatif
        ]);

        return redirect()->back()->with('success', 'Lamaran berhasil dikirim!');
    }
}
