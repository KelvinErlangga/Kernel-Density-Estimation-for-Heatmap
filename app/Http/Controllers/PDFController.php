<?php

namespace App\Http\Controllers;

use App\Models\CurriculumVitaeUser;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PDFController extends Controller
{
    public function exportPDFCurriculumVitae(CurriculumVitaeUser $curriculumVitaeUser)
    {
        set_time_limit(300);

        $curriculumVitaeUser->load(['personalCurriculumVitae', 'links', 'skills', 'experiences', 'educations', 'languages']);

        // Kirim data ke view PDF
        $pdf = Pdf::loadView('pelamar.curriculum_vitae.template.cv_' . $curriculumVitaeUser->template_curriculum_vitae_id, compact('curriculumVitaeUser'));

        // Unduh PDF
        return $pdf->download('CV-' . $curriculumVitaeUser->personalCurriculumVitae->first_name_curriculum_vitae . '.pdf');
    }

    public function printCurriculumVitae(CurriculumVitaeUser $curriculumVitaeUser)
    {
        return view('pelamar.curriculum_vitae.template.cv_' . $curriculumVitaeUser->template_curriculum_vitae_id, compact('curriculumVitaeUser'));
    }
}
