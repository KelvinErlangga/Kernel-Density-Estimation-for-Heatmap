<?php

namespace App\Http\Controllers;

use App\Models\Hiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CurriculumVitaeUser;
use App\Models\SkillUser;
use Illuminate\Support\Facades\Log;   // optional, kalau mau pakai Log::info()
use Illuminate\Support\Facades\DB;    // optional, kalau mau log SQL


class HiringController extends Controller
{
    // View untuk halaman heatmap pelamar
    public function index()
    {
        $hirings = Hiring::with('personalCompany')->get();

        // Ambil CV user (bisa kosong)
        $user = Auth::user();
        $cvs = collect();
        if ($user) {
            $cvs = CurriculumVitaeUser::where('user_id', $user->id)->get();
        }

        return view('pelamar.dashboard.heatmap.index', compact('hirings', 'cvs'));
    }

    public function heatmapData(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([]);
        }

        // ====================== 1. AMBIL CV USER ======================
        $cvIds = CurriculumVitaeUser::where('user_id', $user->id)->pluck('id');

        // --- Skill user (dibikin lowercase & unik) ---
        $userSkills = SkillUser::whereIn('curriculum_vitae_user_id', $cvIds)
            ->pluck('skill_name')
            ->filter()
            ->map(fn($s) => strtolower(trim($s)))
            ->unique()
            ->values()
            ->all();

        if (empty($userSkills)) {
            return response()->json([]);
        }

        // --- Level pendidikan user (string mentah) ---
        $educationLevelsRaw = \DB::table('education')
            ->whereIn('curriculum_vitae_user_id', $cvIds)
            ->pluck('education_level')
            ->filter()
            ->map(fn($v) => trim($v))
            ->values();

        /**
         * NORMALISASI pendidikan agar input apa pun (misal: "s1", "S.1", "Strata 1")
         * jadi 1 format yang sama dengan key pada $levelRank.
         * SD/MI dan SMP/MTS DISENGAJA tidak di-map (dianggap tidak valid/diabaikan).
         */
        $normalizeEdu = function (?string $v): ?string {
            $v = strtoupper(trim((string) $v));
            $v = preg_replace('/\s+/', ' ', $v);

            // buang titik/strip biar lebih fleksibel
            $vClean = str_replace(['.', '-', '_'], '', $v);

            return match (true) {
                // SMA/SMK/MA (kamu minta SD & SMP dihapus, jadi tidak ada case SD/SMP di sini)
                in_array($vClean, ['SMA', 'SMK', 'MA', 'SMAMA', 'SMASMK', 'SMAMASMK', 'SMAMASMK'], true) => 'SMA/MA/SMK',
                str_contains($v, 'SMA') || str_contains($v, 'SMK') || str_contains($v, 'MA') => 'SMA/MA/SMK',

                // Diploma & Strata
                $vClean === 'D1' || str_contains($vClean, 'D1') => 'D1',
                $vClean === 'D2' || str_contains($vClean, 'D2') => 'D2',
                $vClean === 'D3' || str_contains($vClean, 'D3') => 'D3',
                $vClean === 'D4' || str_contains($vClean, 'D4') => 'D4',

                $vClean === 'S1' || str_contains($vClean, 'S1') || str_contains($v, 'STRATA 1') => 'S1',
                $vClean === 'S2' || str_contains($vClean, 'S2') || str_contains($v, 'STRATA 2') => 'S2',
                $vClean === 'S3' || str_contains($vClean, 'S3') || str_contains($v, 'STRATA 3') => 'S3',

                default => null,
            };
        };

        // Mapping jenjang -> ranking angka (SESUDAH SD/MI & SMP/MTS DIHAPUS)
        // Dibuat berurutan biar logika "beda 1 tingkat" tetap valid.
        $levelRank = [
            'SMA/MA/SMK' => 1,
            'D1'         => 2,
            'D2'         => 3,
            'D3'         => 4,
            'D4'         => 5,
            'S1'         => 6,
            'S2'         => 7,
            'S3'         => 8,
        ];

        // Cari level pendidikan user tertinggi dalam bentuk angka
        $userEduScore = 0;
        foreach ($educationLevelsRaw as $lvl) {
            $key = $normalizeEdu($lvl);
            if ($key && isset($levelRank[$key])) {
                $userEduScore = max($userEduScore, $levelRank[$key]);
            }
        }

        // Daftar jenjang yang boleh ditampilkan (<= level user)
        $allowedEduLevels = [];
        if ($userEduScore > 0) {
            foreach ($levelRank as $name => $rank) {
                if ($rank <= $userEduScore) {
                    $allowedEduLevels[] = $name;
                }
            }
        }

        // ====================== 2. QUERY LOWONGAN ======================
        $normTeknis = "LOWER(CONCAT(',', REPLACE(REPLACE(REPLACE(keterampilan_teknis, ', ', ','), ' ,', ','), ',,', ','), ','))";
        $normNonTek = "LOWER(CONCAT(',', REPLACE(REPLACE(REPLACE(keterampilan_non_teknis, ', ', ','), ' ,', ','), ',,', ','), ','))";

        $query = Hiring::with('personalCompany')
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('deadline_hiring')
                    ->orWhereDate('deadline_hiring', '>=', today());
            })
            ->where(function ($q) use ($userSkills, $normTeknis, $normNonTek) {
                foreach ($userSkills as $s) {
                    $alts = match ($s) {
                        'javascript', 'js'                      => ['javascript', 'js'],
                        'vue', 'vuejs', 'vue.js'                => ['vue', 'vuejs', 'vue.js'],
                        'node', 'nodejs', 'node.js'             => ['node', 'nodejs', 'node.js'],
                        'postgres', 'postgresql'                => ['postgresql', 'postgres', 'psql'],
                        'power bi', 'powerbi'                   => ['power bi', 'powerbi'],
                        'microsoft office', 'ms office', 'office' => ['microsoft office', 'ms office', 'office'],
                        'sql server', 'mssql'                   => ['sql server', 'mssql'],
                        default                                 => [$s],
                    };

                    foreach ($alts as $a) {
                        $needle = '%,' . strtolower($a) . ',%';
                        $q->orWhereRaw("$normTeknis LIKE ?", [$needle])
                            ->orWhereRaw("$normNonTek LIKE ?", [$needle]);
                    }
                }
            });

        // Filter text search (opsional)
        if ($request->filled('q')) {
            $qText = $request->query('q');
            $query->where(function ($qq) use ($qText) {
                $qq->where('position_hiring', 'like', "%{$qText}%")
                    ->orWhere('kota', 'like', "%{$qText}%")
                    ->orWhere('provinsi', 'like', "%{$qText}%");
            });
        }

        // Filter pendidikan: cocokkan yang "mengandung" level (bukan harus sama persis)
        if (!empty($allowedEduLevels)) {
            $query->where(function ($qq) use ($allowedEduLevels) {
                foreach ($allowedEduLevels as $lvl) {
                    if ($lvl === 'SMA/MA/SMK') {
                        // match salah satu dari SMA/SMK/MA di input perusahaan
                        $qq->orWhereRaw("UPPER(education_hiring) REGEXP 'SMA|SMK|MA'");
                    } else {
                        // S1, S2, D3, dst -> cukup mengandung "S1" dll
                        $qq->orWhereRaw("UPPER(education_hiring) LIKE ?", ['%' . $lvl . '%']);
                    }
                }
            });
        }

        // ====== MODE NEARBY (jarak) ======
        $mode = $request->query('mode', 'default');
        if ($mode === 'nearby' && $request->filled(['origin_lat', 'origin_lon'])) {
            $originLat = (float) $request->query('origin_lat');
            $originLon = (float) $request->query('origin_lon');
            $radiusKm  = (float) $request->query('radius_km', 30);

            $query->whereNotNull('latitude')->whereNotNull('longitude');

            $query->select('*')
                ->selectRaw(
                    "(6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance_km",
                    [$originLat, $originLon, $originLat]
                )
                ->having('distance_km', '<=', $radiusKm)
                ->orderBy('distance_km', 'asc');
        }

        $data = $query->get();

        // ====================== 3. HITUNG MATCHING PERCENTAGE ======================
        $result = $data->map(function ($hiring) use ($userSkills, $userEduScore, $levelRank, $normalizeEdu) {

            // --- Siapkan daftar skill lowongan (teknis + non teknis, lowercase, unik) ---
            $jobTech = array_filter(array_map(
                fn($s) => strtolower(trim($s)),
                explode(',', (string) $hiring->keterampilan_teknis)
            ));
            $jobNon = array_filter(array_map(
                fn($s) => strtolower(trim($s)),
                explode(',', (string) $hiring->keterampilan_non_teknis)
            ));

            $jobSkills = array_values(array_unique(array_merge($jobTech, $jobNon)));
            $totalSkills = count($jobSkills);

            // --- Hitung berapa skill user yang masuk di lowongan ini ---
            $matchCount = 0;
            if ($totalSkills > 0 && !empty($userSkills)) {
                $matchCount = count(array_intersect($jobSkills, $userSkills));
            }

            // 60% bobot untuk skill
            $skillScore = 0;
            if ($totalSkills > 0 && $matchCount > 0) {
                $ratio = $matchCount / $totalSkills; // 0..1
                $skillScore = round($ratio * 60);    // 0..60
            }

            // --- Pendidikan: 40% bobot ---
            $eduScore = 0;
            $reqKey = $normalizeEdu($hiring->education_hiring);
            $reqScore = $reqKey ? ($levelRank[$reqKey] ?? null) : null;

            if ($reqScore !== null && $userEduScore > 0) {
                if ($userEduScore >= $reqScore) {
                    $eduScore = 40; // memenuhi / lebih tinggi
                } elseif (($userEduScore + 1) === $reqScore) {
                    $eduScore = 25; // kurang 1 tingkat
                } else {
                    $eduScore = 10; // jauh di bawah
                }
            }

            $hiring->matching_percentage = min(100, $skillScore + $eduScore);

            return $hiring;
        });

        return response()->json($result);
    }

    // API untuk ambil detail lowongan tertentu
    public function show($id)
    {
        $hiring = Hiring::with(['personalCompany', 'applicants'])->findOrFail($id);

        $data = [
            'id' => $hiring->id,
            'position_hiring' => $hiring->position_hiring,
            'jenis_pekerjaan' => $hiring->jenis_pekerjaan,
            'work_system' => $hiring->work_system,
            'pola_kerja' => $hiring->pola_kerja,
            'description_hiring' => $hiring->description_hiring,
            'kualifikasi' => $hiring->kualifikasi,
            'education_hiring' => $hiring->education_hiring,
            'pengalaman_minimal_tahun' => $hiring->pengalaman_minimal_tahun,
            'usia_maksimal' => $hiring->usia_maksimal,
            'keterampilan_teknis' => $hiring->keterampilan_teknis,
            'keterampilan_non_teknis' => $hiring->keterampilan_non_teknis,
            'deadline_hiring' => $hiring->deadline_hiring,
            'gaji_min' => $hiring->gaji_min,
            'gaji_max' => $hiring->gaji_max,
            'kota' => $hiring->kota,
            'provinsi' => $hiring->provinsi,

            // Tambahan info perusahaan
            'company_name' => $hiring->personalCompany->name_company ?? 'Tidak Ada Nama Perusahaan',
            'personal_company_logo' => $hiring->personalCompany && $hiring->personalCompany->logo
                ? asset('storage/company_logo/' . $hiring->personalCompany->logo)
                : asset('images/default-company.png'),
            'type_of_company' => $hiring->personalCompany->type_of_company ?? '-',
        ];

        return response()->json($data);
    }

    public function jobSuggestions(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $suggestions = Hiring::query()
            ->where('position_hiring', 'LIKE', "%{$q}%")
            ->distinct()
            ->limit(10)
            ->pluck('position_hiring');

        return response()->json($suggestions);
    }
}
