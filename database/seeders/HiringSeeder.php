<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HiringSeeder extends Seeder
{
    public function run()
    {
        // ===================== Zona Surabaya (tanpa Utara) =====================
        $zones = [
            ['name' => 'center', 'lat_min' => -7.34, 'lat_max' => -7.28, 'lon_min' => 112.70, 'lon_max' => 112.78],
            ['name' => 'west',   'lat_min' => -7.37, 'lat_max' => -7.28, 'lon_min' => 112.65, 'lon_max' => 112.70],
            ['name' => 'east',   'lat_min' => -7.37, 'lat_max' => -7.28, 'lon_min' => 112.78, 'lon_max' => 112.85],
            ['name' => 'south',  'lat_min' => -7.42, 'lat_max' => -7.34, 'lon_min' => 112.70, 'lon_max' => 112.80],
        ];

        $workSystems = ['Full Time', 'Part Time', 'Hybrid', 'Remote'];
        $polaKerja   = ['Shift', 'Non-shift'];
        $educations  = ['SMA/SMK', 'D3', 'S1', 'S2'];

        // ===================== Domain + Role-specific Skills (RELEVAN) =====================
        $domains = [
            'Teknologi Informasi' => [
                'salary' => [6_000_000, 18_000_000],
                'roles' => [
                    'Backend Developer (Laravel)' => [
                        'tech' => ['PHP','Laravel','MySQL','REST API','Eloquent ORM','Git','Docker','Redis','Unit Testing','Linux'],
                        'soft' => ['Problem Solving','Analytical Thinking','Attention to Detail','Komunikasi','Teamwork','Agile/Scrum','Dokumentasi','Time Management'],
                    ],
                    'Frontend Developer (React)' => [
                        'tech' => ['JavaScript','React','HTML','CSS','REST API','State Management (Redux/Context)','Git','Vite/Webpack','Testing (Jest)','Responsive Design'],
                        'soft' => ['Komunikasi','Kolaborasi lintas tim','Attention to Detail','Problem Solving','Time Management','Adaptif','Agile/Scrum'],
                    ],
                    'Fullstack Developer' => [
                        'tech' => ['JavaScript','PHP','Laravel','React/Vue.js','MySQL','REST API','Git','Docker','CI/CD','Testing'],
                        'soft' => ['Problem Solving','Komunikasi','Teamwork','Analytical Thinking','Time Management','Adaptif','Agile/Scrum','Dokumentasi'],
                    ],
                    'Mobile Developer (Flutter)' => [
                        'tech' => ['Flutter','Dart','REST API','State Management','Firebase','Git','UI Implementation','Debugging','SQLite/Local Storage'],
                        'soft' => ['Problem Solving','Attention to Detail','Komunikasi','Teamwork','Time Management','Adaptif'],
                    ],
                    'DevOps Engineer' => [
                        'tech' => ['Linux','Docker','Kubernetes','CI/CD','Git','Nginx','AWS/GCP','Monitoring (Prometheus/Grafana)','Scripting','Networking'],
                        'soft' => ['Problem Solving','Analytical Thinking','Komunikasi','Kolaborasi lintas tim','Time Management','Attention to Detail'],
                    ],
                    'QA Engineer' => [
                        'tech' => ['Test Case','Bug Tracking','API Testing','Postman','Selenium/Cypress','Regression Testing','Unit Testing','SQL Basic','Git'],
                        'soft' => ['Attention to Detail','Analytical Thinking','Komunikasi','Time Management','Teamwork','Dokumentasi'],
                    ],
                    'Data Engineer' => [
                        'tech' => ['Python','SQL','ETL','Data Warehouse','Airflow','PostgreSQL','BigQuery','Data Modeling','Git','Linux'],
                        'soft' => ['Analytical Thinking','Problem Solving','Komunikasi','Attention to Detail','Time Management','Kolaborasi lintas tim'],
                    ],
                    'Data Analyst' => [
                        'tech' => ['SQL','Excel/Spreadsheet','Data Visualization','Google Data Studio/Looker','Python (Pandas)','Google Analytics','A/B Testing','Reporting'],
                        'soft' => ['Analytical Thinking','Komunikasi','Storytelling','Attention to Detail','Time Management','Problem Solving'],
                    ],
                    'ML Engineer (Jr)' => [
                        'tech' => ['Python','Machine Learning','Data Preprocessing','Model Evaluation','TensorFlow/PyTorch','Experiment Tracking','Git','SQL Basic'],
                        'soft' => ['Analytical Thinking','Problem Solving','Adaptif','Komunikasi','Teamwork','Time Management'],
                    ],
                    'UI/UX Designer' => [
                        'tech' => ['Figma','Wireframing','Prototyping','Design System','User Research','Usability Testing','Information Architecture','Responsive Design'],
                        'soft' => ['Komunikasi','Empati','Kreativitas','Kolaborasi lintas tim','Problem Solving','Time Management','Attention to Detail'],
                    ],
                ],
            ],

            'Marketing' => [
                'salary' => [4_000_000, 12_000_000],
                'roles' => [
                    'Digital Marketing Specialist' => [
                        'tech' => ['Campaign Planning','Meta Ads','Google Ads','Copywriting','Content Marketing','Landing Page','A/B Testing','Google Analytics','UTM Tracking'],
                        'soft' => ['Komunikasi','Kreativitas','Problem Solving','Time Management','Kolaborasi','Adaptasi','Riset Pasar'],
                    ],
                    'SEO/SEM Specialist' => [
                        'tech' => ['SEO','Keyword Research','On-page SEO','Technical SEO','Link Building','Google Search Console','Google Ads','Google Analytics','Content Optimization'],
                        'soft' => ['Analytical Thinking','Problem Solving','Komunikasi','Ketekunan','Time Management','Kolaborasi'],
                    ],
                    'Content Strategist' => [
                        'tech' => ['Content Planning','Content Calendar','SEO','Copywriting','Content Marketing','Audience Research','Brand Guideline','Performance Reporting'],
                        'soft' => ['Storytelling','Kreativitas','Komunikasi','Kolaborasi','Time Management','Problem Solving'],
                    ],
                    'Social Media Specialist' => [
                        'tech' => ['Social Media Management','Content Creation','Instagram/TikTok Strategy','Community Management','Canva','Basic Video Editing','Social Analytics','Scheduling Tools'],
                        'soft' => ['Komunikasi','Kreativitas','Adaptasi','Kolaborasi','Time Management','Problem Solving'],
                    ],
                    'Performance Marketing Analyst' => [
                        'tech' => ['Meta Ads','Google Ads','Conversion Tracking','A/B Testing','Google Analytics','Data Studio/Looker','Cohort/Retention Insight','Budget Optimization'],
                        'soft' => ['Analytical Thinking','Problem Solving','Komunikasi','Attention to Detail','Time Management','Kolaborasi'],
                    ],
                    'Brand Executive' => [
                        'tech' => ['Brand Strategy','Campaign Planning','Market Research','Competitor Analysis','Briefing Creative','Brand Guideline','Reporting'],
                        'soft' => ['Komunikasi','Presentasi','Negosiasi','Kolaborasi','Kreativitas','Problem Solving'],
                    ],
                    'Copywriter' => [
                        'tech' => ['Copywriting','Content Marketing','Ad Copy','SEO Writing','Brand Tone','Storytelling','Landing Page Copy'],
                        'soft' => ['Kreativitas','Komunikasi','Attention to Detail','Time Management','Kolaborasi','Adaptasi'],
                    ],
                    'CRM Specialist' => [
                        'tech' => ['Email Marketing','CRM','Segmentation','Marketing Automation','A/B Testing','Retention Strategy','Customer Journey','Reporting'],
                        'soft' => ['Analytical Thinking','Komunikasi','Problem Solving','Attention to Detail','Kolaborasi','Time Management'],
                    ],
                    'Marketing Analyst' => [
                        'tech' => ['Market Research','Google Analytics','Data Visualization','Excel/Spreadsheet','Reporting','A/B Testing','Customer Insight','Dashboarding'],
                        'soft' => ['Analytical Thinking','Komunikasi','Problem Solving','Attention to Detail','Time Management','Kolaborasi'],
                    ],
                    'Partnership Executive' => [
                        'tech' => ['Partnership Strategy','Negotiation','Proposal Writing','Pipeline Management','Business Development','Market Research','Reporting'],
                        'soft' => ['Negosiasi','Komunikasi','Presentasi','Kolaborasi','Problem Solving','Adaptasi','Time Management'],
                    ],
                ],
            ],
        ];

        // ===================== Helper =====================
        $randFrom = fn(array $arr) => $arr[array_rand($arr)];

        $pickSkills = function (array $pool, int $min = 6, int $max = 8) {
            $pool = array_values(array_unique($pool));
            $take = rand($min, min($max, count($pool)));
            $keys = array_rand($pool, $take);
            if (!is_array($keys)) $keys = [$keys];
            $picked = array_map(fn($k) => $pool[$k], $keys);
            $picked = array_values(array_unique($picked));
            shuffle($picked);
            return implode(', ', $picked);
        };

        $randSalary = function (array $range) {
            [$min, $max] = $range;
            $gajiMin = rand($min, (int)floor(($min + $max) / 2));
            $gajiMax = rand(max($gajiMin + 500_000, $gajiMin + 1), $max);
            return [$gajiMin, $gajiMax];
        };

        $randCoord = function (array $zone) {
            $lat = $zone['lat_min'] + lcg_value() * ($zone['lat_max'] - $zone['lat_min']);
            $lon = $zone['lon_min'] + lcg_value() * ($zone['lon_max'] - $zone['lon_min']);
            return [$lat, $lon];
        };

        $makeQualification = function (string $industry, string $role) {
            // Pakai separator ";" biar cocok dengan fungsi makeBulletList() kamu (split ;)
            if ($industry === 'Teknologi Informasi') {
                return implode('; ', [
                    "Pengalaman 0–2 tahun pada posisi {$role}",
                    "Memahami dasar clean code dan version control (Git)",
                    "Terbiasa bekerja dengan API dan debugging",
                    "Mampu bekerja dalam tim dan mengikuti proses Agile/Scrum",
                    "Bersedia belajar teknologi baru dan mengikuti deadline",
                ]);
            }

            // Marketing
            return implode('; ', [
                "Pengalaman 0–2 tahun pada posisi {$role}",
                "Mampu membuat laporan dan membaca data performa kampanye/konten",
                "Memahami target audience dan strategi komunikasi brand",
                "Terbiasa koordinasi lintas tim (desain, konten, sales)",
                "Mampu bekerja dengan target dan timeline",
            ]);
        };

        $makeDescription = function (string $industry, string $role) {
            if ($industry === 'Teknologi Informasi') {
                return implode('; ', [
                    "Mengembangkan dan memelihara fitur sesuai kebutuhan bisnis",
                    "Kolaborasi dengan tim produk/QA untuk memastikan kualitas rilis",
                    "Melakukan debugging, optimasi, dan dokumentasi teknis",
                    "Berpartisipasi dalam code review dan perbaikan berkelanjutan",
                ]);
            }

            return implode('; ', [
                "Menyusun dan menjalankan aktivitas pemasaran sesuai target",
                "Melakukan evaluasi performa kampanye/konten dan membuat laporan",
                "Kolaborasi dengan tim kreatif untuk menghasilkan materi promosi",
                "Mengoptimalkan strategi berdasarkan data dan insight pasar",
            ]);
        };

        $makeOne = function (string $industryName, array $conf) use (
            $zones, $randFrom, $randCoord, $pickSkills, $randSalary,
            $workSystems, $polaKerja, $educations, $makeQualification, $makeDescription
        ) {
            $zone = $zones[array_rand($zones)];
            [$lat, $lon] = $randCoord($zone);

            $roles = array_keys($conf['roles']);
            $roleName = $randFrom($roles);

            $roleConf = $conf['roles'][$roleName];

            [$gMin, $gMax] = $randSalary($conf['salary']);

            // Skills sesuai role (relevan)
            $techSkill = $pickSkills($roleConf['tech'], 6, 8);
            $softSkill = $pickSkills($roleConf['soft'], 6, 8);

            return [
                'personal_company_id'      => 4, // sesuaikan jika perlu
                'position_hiring'          => $roleName,
                'address_hiring'           => "Jl. " . Str::title(Str::random(8)) . " No." . rand(1, 200),
                'work_system'              => $randFrom($workSystems),
                'pola_kerja'               => $randFrom($polaKerja),
                'education_hiring'         => $randFrom($educations),
                'deadline_hiring'          => Carbon::now()->addDays(rand(7, 90)),
                'description_hiring'       => $makeDescription($industryName, $roleName),
                'created_at'               => Carbon::now(),
                'updated_at'               => Carbon::now(),
                'deleted_at'               => null,
                'ukuran_perusahaan'        => rand(30, 600) . " karyawan",
                'sektor_industri'          => $industryName,
                'kualifikasi'              => $makeQualification($industryName, $roleName),
                'pengalaman_minimal_tahun' => rand(0, 2),
                'usia_maksimal'            => rand(24, 40),
                'keterampilan_teknis'      => $techSkill,
                'keterampilan_non_teknis'  => $softSkill,
                'jenis_pekerjaan'          => rand(0, 1) ? 'Kontrak' : 'Tetap',
                'gaji_min'                 => $gMin,
                'gaji_max'                 => $gMax,
                'kota'                     => 'Surabaya',
                'provinsi'                 => 'Jawa Timur',
                'latitude'                 => $lat,
                'longitude'                => $lon,
            ];
        };

        // (Opsional) biar tidak numpuk kalau seed berkali-kali:
        // DB::table('hirings')->truncate();

        $hirings = [];

        // ===================== Generate 100: 50 IT + 50 Marketing =====================
        for ($i = 0; $i < 50; $i++) {
            $hirings[] = $makeOne('Teknologi Informasi', $domains['Teknologi Informasi']);
        }

        for ($i = 0; $i < 50; $i++) {
            $hirings[] = $makeOne('Marketing', $domains['Marketing']);
        }

        DB::table('hirings')->insert($hirings);
    }
}
