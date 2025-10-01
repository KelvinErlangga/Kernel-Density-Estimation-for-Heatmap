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
use App\Mail\InterviewInvitationMail;
use Illuminate\Support\Facades\Mail;

class DashboardCompanyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $personalCompany = $user->personalCompany;

        if (!$personalCompany) {
            $applicants = collect();
            $applicantCount = 0;
            $hiringCount = 0;
        } else {
            // Ambil kandidat yang hanya melamar ke lowongan perusahaan ini
            $applicants = Applicant::whereHas('hiring', function ($query) use ($personalCompany) {
                $query->where('personal_company_id', $personalCompany->id);
            })
                ->with(['user.personalPelamar', 'hiring'])
                ->get();

            // Hitung jumlah kandidat
            $applicantCount = $applicants->count();

            // Hitung jumlah lowongan aktif perusahaan ini
            $hiringCount = Hiring::where('personal_company_id', $personalCompany->id)->count();
        }

        return view('perusahaan.dashboard_perusahaan', [
            'personalCompany' => $personalCompany,
            'applicants' => $applicants,
            'applicantCount' => $applicantCount,
            'hiring' => $hiringCount
        ]);
    }

    public function getLowongan()
    {
        $user = Auth::user();
        $personalCompany = $user->personalCompany;

        if ($personalCompany) {
            // ambil semua lowongan termasuk yang sudah dihapus (soft delete)
            $hirings = $personalCompany->hirings()->withTrashed()->get();
        } else {
            $hirings = collect();
        }

        return view('perusahaan.lowongan.index', compact('hirings'));
    }


    public function editLowongan(Hiring $hiring)
    {
        return view('perusahaan.lowongan.edit', compact('hiring'));
    }

    // public function deleteLowongan(Hiring $hiring)
    // {
    //     DB::transaction(function () use ($hiring) {
    //         $hiring->delete();
    //     });

    //     return redirect()->route('perusahaan.lowongan.index');
    // }

    public function nonaktifkanLowongan(Hiring $hiring)
    {
        DB::transaction(function () use ($hiring) {
            // kode lama (tetap dipertahankan)
            $hiring->delete();

            // tambahan: jika ada relasi dengan tabel lain bisa ikut dihapus
            // contoh: hapus semua pelamar yang terkait dengan lowongan ini
            if ($hiring->applicants()->exists()) {
                $hiring->applicants()->delete();
            }
        });

        // tambahan: flash message sukses
        return redirect()
            ->route('perusahaan.lowongan.index')
            ->with('success', 'Lowongan berhasil dinonaktifkan.');
    }

    public function restoreLowongan($id)
    {
        $hiring = Hiring::onlyTrashed()->findOrFail($id);
        $hiring->restore();

        return redirect()->route('perusahaan.lowongan.index')
            ->with('success', 'Lowongan berhasil diaktifkan kembali.');
    }

    public function addLowongan(AddHiringRequest $request)
    {
        $user = Auth::user();

        $personalCompany = $user->personalCompany;

        DB::transaction(function () use ($request, $personalCompany) {
            $validated = $request->validated();

            $personalCompany->hirings()->create($validated);
        });

        return redirect()->route('perusahaan.lowongan.index')
            ->with('success', 'Lowongan berhasil dibuat!');
    }

    public function updateLowongan(AddHiringRequest $request, Hiring $hiring)
    {
        DB::transaction(function () use ($request, $hiring) {
            $validated = $request->validated();

            $hiring->update($validated);
        });

        return redirect()->route('perusahaan.lowongan.index');
    }

    // public function getKandidat()
    // {
    //     $applicants = Applicant::with(['user', 'hiring'])->get();

    //     return view('perusahaan.kandidat.index', compact('applicants'));
    // }

    public function getKandidat()
    {
        $user = Auth::user();
        $personalCompany = $user->personalCompany;

        if (!$personalCompany) {
            $applicants = collect(); // Kosong kalau perusahaan belum punya profil
        } else {
            $applicants = Applicant::whereHas('hiring', function ($query) use ($personalCompany) {
                $query->where('personal_company_id', $personalCompany->id);
            })
                ->with(['user.personalPelamar', 'hiring'])
                ->get();
        }

        return view('perusahaan.kandidat.index', compact('applicants'));
    }


    // public function updateStatus(Request $request, $id)
    // {
    //     // Validasi request
    //     $request->validate([
    //         'status' => 'required|in:Pending,Diterima,Ditolak',
    //     ]);

    //     // Cari kandidat berdasarkan ID
    //     $applicant = Applicant::find($id);

    //     // Update status
    //     $applicant->status = $request->status;
    //     $applicant->save();

    //     // Redirect kembali dengan pesan sukses
    //     return redirect()->route('perusahaan.kandidat.index');
    // }

    // public function updateStatus(Request $request, $id)
    // {
    //     $rules = [
    //         'status' => 'required|in:Pending,Diterima,Ditolak,Proses Seleksi',
    //     ];

    //     if ($request->status === 'Proses Seleksi') {
    //         $rules['undangan_via'] = 'required|in:email,whatsapp';
    //     }

    //     $validated = $request->validate($rules);

    //     $applicant = Applicant::findOrFail($id);
    //     $applicant->status = $validated['status'];

    //     $waLink = null;

    //     if ($validated['status'] === 'Proses Seleksi') {
    //         if ($validated['undangan_via'] === 'email' && $request->filled('interview_note')) {
    //             try {
    //                 Mail::to($applicant->user->email)
    //                     ->send(new InterviewInvitationMail(
    //                         $request->interview_note,
    //                         auth()->user()->name
    //                     ));
    //             } catch (\Exception $e) {
    //                 \Log::error('Gagal kirim email: ' . $e->getMessage());
    //             }
    //         } elseif ($validated['undangan_via'] === 'whatsapp') {
    //             $phone = preg_replace('/[^0-9]/', '', $applicant->user->personalPelamar->phone_pelamar);
    //             $waLink = "https://wa.me/{$phone}";
    //         }
    //     }

    //     if ($validated['status'] === 'Diterima' && $request->filled('interview_note')) {
    //         try {
    //             Mail::to($applicant->user->email)
    //                 ->send(new InterviewInvitationMail(
    //                     $request->interview_note,
    //                     auth()->user()->name
    //                 ));
    //         } catch (\Exception $e) {
    //             \Log::error('Gagal kirim email: ' . $e->getMessage());
    //         }
    //     }

    //     $applicant->save();

    //     $redirect = redirect()
    //         ->route('perusahaan.kandidat.index')
    //         ->with('success', 'Status kandidat berhasil diperbarui!');

    //     if ($waLink) {
    //         $redirect->with('wa_link', $waLink);
    //     }

    //     return $redirect;
    // }

    public function updateStatus(Request $request, $id)
{
    $rules = [
        'status' => 'required|in:Proses Seleksi,Diterima,Ditolak',
    ];

    if ($request->status === 'Proses Seleksi') {
        $rules['undangan_via'] = 'required|in:email,whatsapp';
    }

    $validated = $request->validate($rules);

    $applicant = Applicant::findOrFail($id);
    $applicant->status = $validated['status'];

    // Kirim email jika proses seleksi via email
    if ($validated['status'] === 'Proses Seleksi'
        && $validated['undangan_via'] === 'email'
        && $request->filled('interview_note'))
    {
        try {
            \Mail::to($applicant->user->email)->send(
                new \App\Mail\InterviewInvitationMail(
                    $request->interview_note,
                    auth()->user()->name
                )
            );
        } catch (\Exception $e) {
            \Log::error('Gagal kirim email undangan: '.$e->getMessage());
        }
    }

    // Jika diterima dan ada interview note → kirim email juga
    if ($validated['status'] === 'Diterima' && $request->filled('interview_note')) {
        try {
            \Mail::to($applicant->user->email)->send(
                new \App\Mail\InterviewInvitationMail(
                    $request->interview_note,
                    auth()->user()->name
                )
            );
        } catch (\Exception $e) {
            \Log::error('Gagal kirim email penerimaan: '.$e->getMessage());
        }
    }

    $applicant->save();

    return redirect()
        ->route('perusahaan.kandidat.index')
        ->with('success', 'Status kandidat berhasil diperbarui!')
        ->with('flash_clear', true);
}



    public function deleteKandidat(Applicant $applicant)
    {
        DB::transaction(function () use ($applicant) {
            $applicant->delete();
        });

        return redirect()->route('perusahaan.kandidat.index');
    }

    // akun
    public function getAkun()
    {
        return view('perusahaan.akun.index');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name_company'      => 'required|string|max:255',
            'phone_company'     => 'required|string|max:20',
            'city_company'      => 'nullable|string|max:100',
            'type_of_company'   => 'nullable|string|max:100',
            'description_company' => 'nullable|string',
            'jumlah_karyawan'   => 'nullable|integer|min:1',
            'jumlah_divisi'     => 'nullable|integer|min:1',
            'tahun_berdiri'     => 'nullable|integer|min:1900|max:' . date('Y'),
        ]);

        $company = Auth::user()->personalCompany;

        $company->update($request->only([
            'name_company',
            'phone_company',
            'city_company',
            'type_of_company',
            'description_company',
            'jumlah_karyawan',
            'jumlah_divisi',
            'tahun_berdiri',
        ]));

        return redirect()->back()->with('success', 'Profil perusahaan berhasil diperbarui.');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $company = $user->personalCompany;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/company_logo', $filename);

            $company->logo = $filename;
            $company->save();
        }

        return redirect()->back()->with('success', 'Logo berhasil diupload!');
    }
}
