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

        // Mapping jenjang -> ranking angka (semakin tinggi, semakin besar)
        $levelRank = [
            'SD/MI'      => 1,
            'SMP/MTS'    => 2,
            'SMA/MA/SMK' => 3,
            'SMA'        => 3,
            'SMK'        => 3,
            'D1'         => 4,
            'D2'         => 5,
            'D3'         => 6,
            'D4'         => 7,
            'S1'         => 8,
            'S2'         => 9,
            'S3'         => 10,
        ];

        // Cari level pendidikan user tertinggi dalam bentuk angka
        $userEduScore = 0;
        foreach ($educationLevelsRaw as $lvl) {
            $key = strtoupper($lvl);
            if (isset($levelRank[$key])) {
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
        // normalisasi kolom skill di tabel hiring
        $normTeknis = "LOWER(CONCAT(',', REPLACE(REPLACE(REPLACE(keterampilan_teknis, ', ', ','), ' ,', ','), ',,', ','), ','))";
        $normNonTek = "LOWER(CONCAT(',', REPLACE(REPLACE(REPLACE(keterampilan_non_teknis, ', ', ','), ' ,', ','), ',,', ','), ','))";

        $query = Hiring::with('personalCompany')
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('deadline_hiring')
                    ->orWhere('deadline_hiring', '>=', now());
            })
            ->where(function ($q) use ($userSkills, $normTeknis, $normNonTek) {
                foreach ($userSkills as $s) {
                    $alts = match ($s) {
                        'javascript', 'js'                => ['javascript', 'js'],
                        'vue', 'vuejs', 'vue.js'          => ['vue', 'vuejs', 'vue.js'],
                        'node', 'nodejs', 'node.js'       => ['node', 'nodejs', 'node.js'],
                        'postgres', 'postgresql'          => ['postgresql', 'postgres', 'psql'],
                        'power bi', 'powerbi'             => ['power bi', 'powerbi'],
                        'microsoft office', 'ms office', 'office' => ['microsoft office', 'ms office', 'office'],
                        'sql server', 'mssql'             => ['sql server', 'mssql'],
                        default                           => [$s],
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

        // Filter pendidikan: hanya level yang <= level user
        if (!empty($allowedEduLevels)) {
            $query->whereIn('education_hiring', $allowedEduLevels);
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
        $result = $data->map(function ($hiring) use ($userSkills, $userEduScore, $levelRank) {

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
            $req = strtoupper(trim((string) $hiring->education_hiring));
            $reqScore = $levelRank[$req] ?? null;

            if ($reqScore !== null && $userEduScore > 0) {
                if ($userEduScore >= $reqScore) {
                    // pendidikan user >= syarat -> full 40
                    $eduScore = 40;
                } elseif ($userEduScore + 1 == $reqScore) {
                    // beda satu tingkat (sedikit kurang) -> 25
                    $eduScore = 25;
                } else {
                    // jauh di bawah -> 10 aja
                    $eduScore = 10;
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
