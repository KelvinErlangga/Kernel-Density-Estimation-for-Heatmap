<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddSubmitAplicationRequest;
use App\Models\Applicant;
use App\Models\CurriculumVitaeUser;
use App\Models\Hiring;
use App\Models\Use;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\PersonalPelamar;

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
    // public function getLowongan()
    // {
    //     $hirings = Hiring::all();

    //     return view('pelamar.dashboard.lowongan.index', compact('hirings'));
    // }

    public function getLowongan()
    {
        $hirings = Hiring::all();

        // Ambil CV user (bisa kosong)
        $user = Auth::user();
        $cvs = collect();
        if ($user) {
            $cvs = CurriculumVitaeUser::where('user_id', $user->id)->get();
        }

        return view('pelamar.dashboard.lowongan.index', compact('hirings', 'cvs'));
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
            'work_system' => $hiring->work_system,
            'pola_kerja' => $hiring->pola_kerja,
            'education_hiring' => $hiring->education_hiring,
            'deadline_hiring' => $hiring->deadline_hiring,
            'description_hiring' => $hiring->description_hiring,
            'ukuran_perusahaan' => $hiring->ukuran_perusahaan,
            'sektor_industri' => $hiring->sektor_industri,
            'kualifikasi' => $hiring->kualifikasi,
            'pengalaman_minimal_tahun' => $hiring->pengalaman_minimal_tahun,
            'usia_maksimal' => $hiring->usia_maksimal,
            'keterampilan_teknis' => $hiring->keterampilan_teknis,
            'keterampilan_non_teknis' => $hiring->keterampilan_non_teknis,
            'jenis_pekerjaan' => $hiring->jenis_pekerjaan,
            'gaji_min' => $hiring->gaji_min,
            'gaji_max' => $hiring->gaji_max,
            'kota' => $hiring->kota,
            'provinsi' => $hiring->provinsi,

            // Info perusahaan
            'company_name' => $hiring->personalCompany->name_company ?? 'Tidak Ada Nama Perusahaan',
            'personal_company_logo' => $hiring->personalCompany && $hiring->personalCompany->logo
                ? asset('storage/company_logo/' . $hiring->personalCompany->logo)
                : asset('images/default-company.png'),
            'type_of_company' => $hiring->personalCompany->type_of_company ?? 'Tidak Ada Nama Perusahaan',

            // Status lamaran
            'has_applied' => $hasApplied,
            'is_closed' => $isClosed,
        ]);
    }

    // public function submitApplication(AddSubmitAplicationRequest $request)
    // {
    //     $user = Auth::user();

    //     DB::transaction(function () use ($request, $user) {

    //         $validated = $request->validated();

    //         if ($request->hasFile('file_applicant')) {
    //             $file_applicantPath = $request->file('file_applicant')->store('applicants', 'public');
    //             $validated['file_applicant'] = $file_applicantPath;
    //         }

    //         $validated['user_id'] = $user->id;
    //         $validated['status'] = 'Pending';

    //         $newAplicant = Applicant::create($validated);
    //     });

    //     return redirect()->route('pelamar.dashboard.lowongan.index');
    // }

    // public function submitApplication(AddSubmitAplicationRequest $request)
    // {
    //     $user = Auth::user();

    //     DB::transaction(function () use ($request, $user) {
    //         $validated = $request->validated();

    //         if ($request->cv_option === 'upload') {
    //             if ($request->hasFile('file_applicant')) {
    //                 $file_applicantPath = $request->file('file_applicant')->store('applicants', 'public');
    //                 $validated['file_applicant'] = $file_applicantPath;
    //             }
    //         } elseif ($request->cv_option === 'dashboard') {
    //             $cv = CurriculumVitaeUser::where('user_id', $user->id)
    //                 ->where('id', $request->dashboard_cv)
    //                 ->firstOrFail();

    //             // Simpan ID CV, bukan file path
    //             $validated['curriculum_vitae_user_id'] = $cv->id;
    //         }

    //         $validated['user_id'] = $user->id;
    //         $validated['status'] = 'Pending';

    //         Applicant::create($validated);
    //     });

    //     return redirect()->route('pelamar.dashboard.lowongan.index')->with('success', 'Lamaran berhasil dikirim');
    // }

    public function submitApplication(AddSubmitAplicationRequest $request)
    {
        $user = Auth::user();

        DB::transaction(function () use ($request, $user) {
            $validated = $request->validated();

            if ($request->cv_option === 'upload') {
                if ($request->hasFile('file_applicant')) {
                    $file_applicantPath = $request->file('file_applicant')->store('applicants', 'public');
                    $validated['file_applicant'] = $file_applicantPath;
                } else {
                    throw new \Exception('File CV wajib diunggah ketika memilih upload.');
                }
            } elseif ($request->cv_option === 'dashboard') {
                $cv = CurriculumVitaeUser::where('user_id', $user->id)
                    ->where('id', $request->dashboard_cv)
                    ->firstOrFail();

                $validated['curriculum_vitae_user_id'] = $cv->id;
                $validated['file_applicant'] = $cv->pdf_file;
            }

            $validated['user_id'] = $user->id;
            $validated['status'] = 'Pending';

            Applicant::create($validated);
        });

        // Cek redirect tujuan
        if ($request->redirect_to === 'heatmap') {
            return redirect()->route('pelamar.dashboard.heatmap.index')
                ->with('success', 'Lamaran berhasil dikirim');
        }

        return redirect()->route('pelamar.dashboard.lowongan.index')
            ->with('success', 'Lamaran berhasil dikirim');
    }

    // heatmap
    public function getHeatmap()
    {
        $hirings = Hiring::all();

        return view('pelamar.dashboard.heatmap.index', compact('hirings'));
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

    // akun
    public function getAkun()
    {
        return view('pelamar.dashboard.akun.index');
    }

    public function updateProfile(Request $request)
    {
        // Validasi input
        $data = $request->validate([
            'name_pelamar'            => ['required', 'string', 'max:255'],
            'phone_pelamar'           => ['required', 'string', 'max:50'],
            'city_pelamar'            => ['nullable', 'string', 'max:255'],
            'date_of_birth_pelamar'   => ['nullable', 'date'],
            'gender'                  => ['nullable', 'in:laki-laki,perempuan'],
            'alamat_domisili'         => ['nullable', 'string', 'max:500'],
            'latitude'                => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'               => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $user = Auth::user();

        // Pastikan record personal_pelamars ada
        $pelamar = $user->personalPelamar()->firstOrCreate(['user_id' => $user->id]);

        // Fallback: jika ada alamat tapi belum ada koordinat, coba geocode di server
        if (!empty($data['alamat_domisili']) && (empty($data['latitude']) || empty($data['longitude']))) {
            try {
                $resp = Http::withHeaders([
                    'User-Agent' => 'CVRE-GENERATE/1.0 (contact: admin@example.com)',
                    'Accept'     => 'application/json',
                ])->get('https://nominatim.openstreetmap.org/search', [
                    'format'         => 'json',
                    'limit'          => 1,
                    'addressdetails' => 1,
                    'countrycodes'   => 'id',
                    'q'              => $data['alamat_domisili'],
                ])->json();

                if (is_array($resp) && count($resp)) {
                    $data['latitude']  = $resp[0]['lat'] ?? null;
                    $data['longitude'] = $resp[0]['lon'] ?? null;
                }
            } catch (\Throwable $e) {
                // optional: \Log::warning('Geocode gagal: '.$e->getMessage());
            }
        }

        // Update kolom-kolom personal pelamar
        $pelamar->fill([
            'name_pelamar'          => $data['name_pelamar'],
            'phone_pelamar'         => $data['phone_pelamar'],
            'city_pelamar'          => $data['city_pelamar'] ?? null,
            'date_of_birth_pelamar' => $data['date_of_birth_pelamar'] ?? null,
            'gender'                => $data['gender'] ?? null,
            'alamat_domisili'       => $data['alamat_domisili'] ?? null,
            'latitude'              => $data['latitude'] ?? null,
            'longitude'             => $data['longitude'] ?? null,
        ])->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }
}
