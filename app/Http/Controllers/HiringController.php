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

    // API untuk heatmap (hanya ambil latitude & longitude)
    // public function heatmapData()
    // {
    //     $data = Hiring::select('latitude', 'longitude')->get();
    //     return response()->json($data);
    // }

    // API untuk ambil semua lowongan (detail)
    // public function heatmapData()
    // {
    //     $data = Hiring::with('personalCompany')->get();
    //     return response()->json($data);
    // }

    public function heatmapData(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([]);
        }

        // ====== Ambil skills user (tetap pakai logika Anda sekarang) ======
        $cvIds = CurriculumVitaeUser::where('user_id', $user->id)->pluck('id');
        $skills = \App\Models\SkillUser::whereIn('curriculum_vitae_user_id', $cvIds)
            ->pluck('skill_name')
            ->filter()
            ->map(fn($s) => strtolower(trim($s)))
            ->unique()
            ->values()
            ->all();

        if (empty($skills)) {
            return response()->json([]);
        }

        // normalisasi kolom (pakai pembatas koma)
        $normTeknis = "LOWER(CONCAT(',', REPLACE(REPLACE(REPLACE(keterampilan_teknis, ', ', ','), ' ,', ','), ',,', ','), ','))";
        $normNonTek = "LOWER(CONCAT(',', REPLACE(REPLACE(REPLACE(keterampilan_non_teknis, ', ', ','), ' ,', ','), ',,', ','), ','))";

        $query = Hiring::with('personalCompany')
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('deadline_hiring')->orWhere('deadline_hiring', '>=', now());
            })
            ->where(function ($q) use ($skills, $normTeknis, $normNonTek) {
                foreach ($skills as $s) {
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

        // Optional: filter teks sederhana (dari search bar)
        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($qq) use ($q) {
                $qq->where('position_hiring', 'like', "%{$q}%")
                    ->orWhere('kota', 'like', "%{$q}%")
                    ->orWhere('provinsi', 'like', "%{$q}%");
            });
        }

        // ====== MODE NEARBY: filter by distance dari lat/lon user ======
        $mode = $request->query('mode', 'default');
        if ($mode === 'nearby' && $request->filled(['origin_lat', 'origin_lon'])) {
            $originLat = (float) $request->query('origin_lat');
            $originLon = (float) $request->query('origin_lon');
            $radiusKm  = (float) $request->query('radius_km', 30);

            // pastikan punya koordinat
            $query->whereNotNull('latitude')->whereNotNull('longitude');

            // Haversine/ACOS – MySQL/MariaDB friendly
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

        return response()->json($data);
    }

    // public function heatmapData(Request $request)
    // {
    //     $user = Auth::user();
    //     Log::info('heatmapData() called');

    //     if (!$user) {
    //         Log::warning('No authenticated user');
    //         return response()->json([]);
    //     }

    //     Log::info('User login:', ['id' => $user->id, 'email' => $user->email ?? null]);

    //     // Ambil CV IDs milik user
    //     $cvIds = CurriculumVitaeUser::where('user_id', $user->id)->pluck('id');
    //     Log::info('CV IDs:', $cvIds->toArray());

    //     // Ambil skills
    //     $skills = SkillUser::whereIn('curriculum_vitae_user_id', $cvIds)
    //         ->pluck('skill_name')
    //         ->filter()
    //         ->map(fn($s) => strtolower(string: trim($s)))
    //         ->unique()
    //         ->values()
    //         ->all();

    //     Log::info('Skills:', $skills);

    //     if (empty($skills)) {
    //         Log::warning('No skills found for user, returning empty data');
    //         return response()->json([]);
    //     }

    //     // NORMALISASI KOLOM – boundary koma
    //     $normTeknis = "LOWER(CONCAT(',', REPLACE(REPLACE(REPLACE(keterampilan_teknis, ', ', ','), ' ,', ','), ',,', ','), ','))";
    //     $normNonTek = "LOWER(CONCAT(',', REPLACE(REPLACE(REPLACE(keterampilan_non_teknis, ', ', ','), ' ,', ','), ',,', ','), ','))";

    //     $query = Hiring::with('personalCompany')
    //         ->whereNull('deleted_at')
    //         ->where(function ($q) {
    //             $q->whereNull('deadline_hiring')->orWhere('deadline_hiring', '>=', now());
    //         })
    //         ->where(function ($q) use ($skills, $normTeknis, $normNonTek) {
    //             foreach ($skills as $s) {
    //                 $alts = match ($s) {
    //                     'javascript', 'js'          => ['javascript', 'js'],
    //                     'vue', 'vuejs', 'vue.js'     => ['vue', 'vuejs', 'vue.js'],
    //                     'node', 'nodejs', 'node.js'  => ['node', 'nodejs', 'node.js'],
    //                     'postgres', 'postgresql'    => ['postgresql', 'postgres', 'psql'],
    //                     'power bi', 'powerbi'       => ['power bi', 'powerbi'],
    //                     'microsoft office', 'ms office', 'office' => ['microsoft office', 'ms office', 'office'],
    //                     'sql server', 'mssql'       => ['sql server', 'mssql'],
    //                     default                    => [$s],
    //                 };

    //                 foreach ($alts as $a) {
    //                     $needle = '%,' . strtolower($a) . ',%';
    //                     $q->orWhereRaw("$normTeknis LIKE ?", [$needle])
    //                         ->orWhereRaw("$normNonTek LIKE ?", [$needle]);
    //                 }
    //             }
    //         });

    //     // (Opsional) log SQL terakhir yang dieksekusi
    //     // DB::listen(function ($query) { \Log::debug('SQL', ['sql' => $query->sql, 'bindings' => $query->bindings]); });

    //     $data = $query->get();

    //     Log::info('Result count:', ['count' => $data->count()]);
    //     if ($data->count() > 0) {
    //         Log::info('Sample hiring:', [
    //             'id' => $data[0]->id ?? null,
    //             'position' => $data[0]->position_hiring ?? null,
    //             'kota' => $data[0]->kota ?? null,
    //             'teknis' => $data[0]->keterampilan_teknis ?? null,
    //             'non_teknis' => $data[0]->keterampilan_non_teknis ?? null,
    //         ]);
    //     }

    //     return response()->json($data);
    // }

    // public function heatmapData(Request $request)
    // {
    //     $user = Auth::user();
    //     if (!$user) return response()->json([]);

    //     // ========= ambil skills user =========
    //     $cvIds  = CurriculumVitaeUser::where('user_id', $user->id)->pluck('id');
    //     $skills = SkillUser::whereIn('curriculum_vitae_user_id', $cvIds)
    //         ->pluck('skill_name')
    //         ->map(function ($s) {
    //             $s = strtolower(trim($s));
    //             // normalisasi: hapus spasi, titik, dan dash agar "vue.js" ~ "vuejs"
    //             $s = str_replace([' ', '.', '-'], '', $s);
    //             return $s;
    //         })
    //         ->filter()
    //         ->unique()
    //         ->values()
    //         ->all();

    //     if (empty($skills)) {
    //         return response()->json([]); // tidak ada skill → kosongkan heatmap
    //     }

    //     // ========= buang token generik (opsional tapi disarankan) =========
    //     // ini list bisa Anda sesuaikan
    //     $stopwords = ['api', 'git', 'html', 'css', 'office', 'msword', 'excel', 'ppt', 'microsoftoffice'];
    //     $skills = array_values(array_filter($skills, fn($s) => !in_array($s, $stopwords, true)));

    //     // Jika semua terbuang & jadi kosong, kita tetap pakai minimal 1 (biar tidak 0 hasil)
    //     if (empty($skills)) {
    //         // fallback: ambil 1-2 skill teratas awal sebelum dibuang (dari log user Anda)
    //         $skills = ['php', 'laravel', 'javascript'];
    //     }

    //     // ========= parameter ambang skor =========
    //     // bisa diatur via query ?min_score=2 (default 2)
    //     $minScore = max(1, (int) $request->query('min_score', 2));

    //     // ========= normalisasi kolom DB jadi "lowercase + tak ber-spasi/titik/dash + dibatasi koma" =========
    //     // 1) hapus spasi/dot/dash → REPLACE(REPLACE(REPLACE(col, ' ', ''), '.', ''), '-', '')
    //     // 2) pastikan ada koma pembatas di pinggir → CONCAT(',', ..., ',')
    //     $normTeknis = "LOWER(CONCAT(',', REPLACE(REPLACE(REPLACE(REPLACE(keterampilan_teknis, ' ', ''), '.', ''), '-', ''), ',,', ','), ','))";
    //     $normNonTek = "LOWER(CONCAT(',', REPLACE(REPLACE(REPLACE(REPLACE(keterampilan_non_teknis, ' ', ''), '.', ''), '-', ''), ',,', ','), ','))";

    //     // ========= bangun ekspresi skor dinamis =========
    //     // total_score = SUM( case when normTeknis like '%,php,%' or normNonTek like '%,php,%' then 1 else 0 end ) + ...
    //     $scorePieces = [];
    //     foreach ($skills as $s) {
    //         // needle dibatasi koma agar match token utuh
    //         $needle = '%,' . $s . ',%';
    //         $piece = "(CASE WHEN $normTeknis LIKE ? OR $normNonTek LIKE ? THEN 1 ELSE 0 END)";
    //         $scorePieces[] = $piece;
    //         // kita butuh 2 binding per skill (untuk teknis & non-teknis)
    //         $bindings[] = $needle;
    //         $bindings[] = $needle;
    //     }
    //     $scoreSql = implode(' + ', $scorePieces);
    //     if ($scoreSql === '') {
    //         // jaga-jaga (tidak terjadi karena sudah fallback di atas)
    //         return response()->json([]);
    //     }

    //     $query = Hiring::with('personalCompany')
    //         ->whereNull('deleted_at')
    //         ->where(function ($q) {
    //             $q->whereNull('deadline_hiring')->orWhere('deadline_hiring', '>=', now());
    //         })
    //         ->select('*') // kolom asli
    //         ->selectRaw("$scoreSql AS total_score", $bindings ?? [])
    //         ->havingRaw('total_score >= ?', [$minScore])  // <<< ambang skor
    //         ->orderByDesc('total_score')
    //         ->orderBy('deadline_hiring', 'asc');

    //     $data = $query->get();

    //     // ---- log ringkas (boleh dihapus) ----
    //     Log::info('Skill used for scoring:', $skills);
    //     Log::info('Min score:', ['min' => $minScore]);
    //     Log::info('Result count (scored):', ['count' => $data->count()]);

    //     return response()->json($data);
    // }

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
