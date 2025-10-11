<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HiringSeeder extends Seeder
{
    // public function run()
    // {
    //     // ===================== Lokasi dasar =====================
    //     $cities = [
    //         ['kota' => 'Lamongan',   'provinsi' => 'Jawa Timur', 'lat' => -7.1167, 'lon' => 112.4167],
    //         ['kota' => 'Gresik',     'provinsi' => 'Jawa Timur', 'lat' => -7.1667, 'lon' => 112.6500],
    //         ['kota' => 'Surabaya',   'provinsi' => 'Jawa Timur', 'lat' => -7.2500, 'lon' => 112.7500],
    //         ['kota' => 'Sidoarjo',   'provinsi' => 'Jawa Timur', 'lat' => -7.4460, 'lon' => 112.7170],
    //         ['kota' => 'Mojokerto',  'provinsi' => 'Jawa Timur', 'lat' => -7.4667, 'lon' => 112.4333],
    //         ['kota' => 'Malang',     'provinsi' => 'Jawa Timur', 'lat' => -7.9667, 'lon' => 112.6333],
    //     ];

    //     $workSystems = ['Full Time', 'Part Time', 'Hybrid', 'Remote'];
    //     $polaKerja   = ['Shift', 'Non-shift'];
    //     $educations  = ['SMA/SMK', 'D3', 'S1', 'S2'];

    //     // ===================== Definisi per bidang =====================
    //     $domains = [
    //         'Teknologi Informasi' => [
    //             'positions' => [
    //                 'Backend Developer (Laravel)','Frontend Developer (React)',
    //                 'Fullstack Developer','Mobile Developer (Flutter)',
    //                 'DevOps Engineer','QA Engineer','Data Engineer',
    //                 'Data Analyst','ML Engineer (Jr)','UI/UX Designer'
    //             ],
    //             'tech' => [
    //                 'PHP','Laravel','MySQL','JavaScript','React','Vue.js','Node.js',
    //                 'Python','Django','Flask','Java','Spring Boot','Kotlin','Swift',
    //                 'Flutter','Dart','REST API','GraphQL','Docker','Kubernetes','Git',
    //                 'CI/CD','AWS','GCP','Azure','Linux','Redis','PostgreSQL','MongoDB',
    //                 'Unit Testing','Selenium','Cypress'
    //             ],
    //             'soft' => [
    //                 'Problem Solving','Komunikasi','Teamwork','Analytical Thinking',
    //                 'Time Management','Attention to Detail','Dokumentasi','Agile/Scrum',
    //                 'Adaptif','Kolaborasi lintas tim'
    //             ],
    //             // kisaran gaji (IDR)
    //             'salary' => [ 6_000_000, 18_000_000 ],
    //         ],

    //         'Marketing' => [
    //             'positions' => [
    //                 'Digital Marketing Specialist','SEO/SEM Specialist','Content Strategist',
    //                 'Social Media Specialist','Performance Marketing Analyst','Brand Executive',
    //                 'Copywriter','CRM Specialist','Marketing Analyst','Partnership Executive'
    //             ],
    //             'tech' => [
    //                 'SEO','SEM','Google Ads','Facebook Ads','Instagram Ads','Content Marketing',
    //                 'Copywriting','Email Marketing','CRM','Marketing Automation','A/B Testing',
    //                 'Google Analytics','Data Studio','Canva','Photoshop','Video Editing','Landing Page',
    //                 'Keyword Research','Influencer Marketing','Social Media Management'
    //             ],
    //             'soft' => [
    //                 'Komunikasi','Kreativitas','Negosiasi','Presentasi','Riset Pasar',
    //                 'Project Management','Storytelling','Kolaborasi','Problem Solving','Adaptasi'
    //             ],
    //             'salary' => [ 4_000_000, 12_000_000 ],
    //         ],

    //         'Kesehatan' => [
    //             'positions' => [
    //                 'Perawat','Asisten Apoteker','Analis Kesehatan','Admin Rekam Medis',
    //                 'Nutrisionis (Gizi)','Radiografer','Bidan','Petugas Lab','Staf Klaim BPJS',
    //                 'Petugas Farmasi'
    //             ],
    //             'tech' => [
    //                 'SIMRS','Rekam Medis Elektronik','ICD-10 Coding','BPJS Claim',
    //                 'Phlebotomy','Vital Signs Monitoring','Sterilisasi Alat','First Aid',
    //                 'Patient Care','Triage','Manajemen Obat','Inventory Farmasi','MS Office',
    //                 'Protokol K3 RS','Prosedur SOP Klinik'
    //             ],
    //             'soft' => [
    //                 'Empati','Komunikasi Pasien','Kerja Tim (Shift)','Ketelitian',
    //                 'Manajemen Stres','Disiplin','Tanggung Jawab','Etika Profesi',
    //                 'Pelayanan Prima','Kesabaran'
    //             ],
    //             'salary' => [ 3_500_000, 9_000_000 ],
    //         ],

    //         'Manufaktur' => [
    //             'positions' => [
    //                 'Operator Produksi','QC Inspector','Maintenance Technician',
    //                 'PPIC Staff','Warehouse Staff','Logistics Coordinator',
    //                 'HSE Officer','Line Leader','CNC Operator','Planner'
    //             ],
    //             'tech' => [
    //                 'ISO 9001','ISO 14001','5S','Kaizen','TPM','SPC','OEE','Lean Manufacturing',
    //                 'AutoCAD','CNC','PLC','Welding','Caliper/Micrometer','ERP','SAP',
    //                 'Forklift Operation','Inventory Control','Preventive Maintenance'
    //             ],
    //             'soft' => [
    //                 'Disiplin','Kesadaran K3','Problem Solving','Teamwork',
    //                 'Komunikasi','Ketelitian','Inisiatif','Kerja di Bawah Tekanan',
    //                 'Manajemen Waktu','Kepemimpinan (Line)'
    //             ],
    //             'salary' => [ 3_800_000, 10_000_000 ],
    //         ],

    //         'Pendidikan' => [
    //             'positions' => [
    //                 'Guru Matematika','Guru Bahasa Inggris','Guru Informatika',
    //                 'Asisten Dosen','Admin Akademik','Instructional Designer',
    //                 'Laboran','Pustakawan','Pengembang e-Learning','Konselor Siswa'
    //             ],
    //             'tech' => [
    //                 'Curriculum Design','Classroom Management','Assessment',
    //                 'E-Learning (Moodle)','Google Classroom','MS Office',
    //                 'Lesson Planning','Public Speaking','Research Methods','SPSS',
    //                 'Canva','Slide Design','Observasi Kelas','LMS Administration'
    //             ],
    //             'soft' => [
    //                 'Komunikasi','Kesabaran','Kreativitas','Manajemen Waktu',
    //                 'Empati','Kolaborasi','Problem Solving','Mentoring',
    //                 'Etika Profesi','Adaptasi'
    //             ],
    //             'salary' => [ 3_500_000, 8_000_000 ],
    //         ],
    //     ];

    //     // ===================== Helper =====================
    //     $randFrom = function(array $arr) {
    //         return $arr[array_rand($arr)];
    //     };

    //     $pickSkills = function(array $pool, int $min = 5, int $max = 7) {
    //         $take = rand($min, min($max, count($pool)));
    //         $keys = array_rand($pool, $take);
    //         if (!is_array($keys)) $keys = [$keys];
    //         $picked = array_map(fn($k) => $pool[$k], $keys);
    //         // jaga-jaga: hilangkan duplikat & urutkan acak
    //         $picked = array_values(array_unique($picked));
    //         shuffle($picked);
    //         return implode(', ', $picked);
    //     };

    //     $randCompanyId = function() {
    //         // sesuaikan dengan id perusahaan yang ada
    //         return rand(2, 6); // contoh: 2..6
    //     };

    //     $randSalary = function(array $range) {
    //         [$min, $max] = $range;
    //         $gajiMin = rand($min, (int)floor(($min + $max) / 2));
    //         $gajiMax = rand(max($gajiMin + 500_000, $gajiMin + 1), $max);
    //         return [$gajiMin, $gajiMax];
    //     };

    //     $hirings = [];

    //     // ===================== Generate 200 lowongan (40 per domain) =====================
    //     foreach ($domains as $industryName => $conf) {
    //         for ($i = 0; $i < 40; $i++) {
    //             $city = $cities[array_rand($cities)];

    //             // random sekitar kota (kurangi radius biar tetap sekitar)
    //             $lat = $city['lat'] + mt_rand(-700, 700) / 10000; // ±0.07°
    //             $lon = $city['lon'] + mt_rand(-700, 700) / 10000;

    //             // posisi, skill, gaji sesuai domain
    //             $position  = $randFrom($conf['positions']);
    //             $techSkill = $pickSkills($conf['tech'], 5, 7);
    //             $softSkill = $pickSkills($conf['soft'], 5, 7);
    //             [$gMin, $gMax] = $randSalary($conf['salary']);

    //             $hirings[] = [
    //                 'personal_company_id'      => 4,
    //                 'position_hiring'          => $position,
    //                 'address_hiring'           => "Jl. ".Str::title(Str::random(8))." No.".rand(1,200),
    //                 'work_system'              => $randFrom($workSystems),
    //                 'pola_kerja'               => $randFrom($polaKerja),
    //                 'education_hiring'         => $randFrom($educations),
    //                 'deadline_hiring'          => Carbon::now()->addDays(rand(7,90)),
    //                 'description_hiring'       => "Bertanggung jawab pada fungsi ".$industryName." – ".Str::title(Str::random(12)),
    //                 'created_at'               => Carbon::now(),
    //                 'updated_at'               => Carbon::now(),
    //                 'deleted_at'               => null,
    //                 'ukuran_perusahaan'        => rand(10,500)." karyawan",
    //                 'sektor_industri'          => $industryName,
    //                 'kualifikasi'              => "Pengalaman ".rand(0,5)." tahun; mampu bekerja dalam tim; siap belajar.",
    //                 'pengalaman_minimal_tahun' => rand(0,5),
    //                 'usia_maksimal'            => rand(25,40),
    //                 'keterampilan_teknis'      => $techSkill,
    //                 'keterampilan_non_teknis'  => $softSkill,
    //                 'jenis_pekerjaan'          => rand(0,1) ? 'Kontrak' : 'Tetap',
    //                 'gaji_min'                 => $gMin,
    //                 'gaji_max'                 => $gMax,
    //                 'kota'                     => $city['kota'],
    //                 'provinsi'                 => $city['provinsi'],
    //                 'latitude'                 => $lat,
    //                 'longitude'                => $lon,
    //             ];
    //         }
    //     }

    //     // ===================== Insert =====================
    //     DB::table('hirings')->insert($hirings);
    // }


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

        // ===================== Definisi per bidang =====================
        $domains = [
            'Teknologi Informasi' => [
                'positions' => [
                    'Backend Developer (Laravel)',
                    'Frontend Developer (React)',
                    'Fullstack Developer',
                    'Mobile Developer (Flutter)',
                    'DevOps Engineer',
                    'QA Engineer',
                    'Data Engineer',
                    'Data Analyst',
                    'ML Engineer (Jr)',
                    'UI/UX Designer'
                ],
                'tech' => [
                    'PHP',
                    'Laravel',
                    'MySQL',
                    'JavaScript',
                    'React',
                    'Vue.js',
                    'Node.js',
                    'Python',
                    'Django',
                    'Flask',
                    'Java',
                    'Spring Boot',
                    'Kotlin',
                    'Swift',
                    'Flutter',
                    'Dart',
                    'REST API',
                    'GraphQL',
                    'Docker',
                    'Kubernetes',
                    'Git',
                    'CI/CD',
                    'AWS',
                    'GCP',
                    'Azure',
                    'Linux',
                    'Redis',
                    'PostgreSQL',
                    'MongoDB',
                    'Unit Testing',
                    'Selenium',
                    'Cypress'
                ],
                'soft' => [
                    'Problem Solving',
                    'Komunikasi',
                    'Teamwork',
                    'Analytical Thinking',
                    'Time Management',
                    'Attention to Detail',
                    'Dokumentasi',
                    'Agile/Scrum',
                    'Adaptif',
                    'Kolaborasi lintas tim'
                ],
                'salary' => [6_000_000, 18_000_000],
            ],
            'Marketing' => [
                'positions' => [
                    'Digital Marketing Specialist',
                    'SEO/SEM Specialist',
                    'Content Strategist',
                    'Social Media Specialist',
                    'Performance Marketing Analyst',
                    'Brand Executive',
                    'Copywriter',
                    'CRM Specialist',
                    'Marketing Analyst',
                    'Partnership Executive'
                ],
                'tech' => [
                    'SEO',
                    'SEM',
                    'Google Ads',
                    'Facebook Ads',
                    'Instagram Ads',
                    'Content Marketing',
                    'Copywriting',
                    'Email Marketing',
                    'CRM',
                    'Marketing Automation',
                    'A/B Testing',
                    'Google Analytics',
                    'Data Studio',
                    'Canva',
                    'Photoshop',
                    'Video Editing',
                    'Landing Page',
                    'Keyword Research',
                    'Influencer Marketing',
                    'Social Media Management'
                ],
                'soft' => [
                    'Komunikasi',
                    'Kreativitas',
                    'Negosiasi',
                    'Presentasi',
                    'Riset Pasar',
                    'Project Management',
                    'Storytelling',
                    'Kolaborasi',
                    'Problem Solving',
                    'Adaptasi'
                ],
                'salary' => [4_000_000, 12_000_000],
            ],
            'Kesehatan' => [
                'positions' => [
                    'Perawat',
                    'Asisten Apoteker',
                    'Analis Kesehatan',
                    'Admin Rekam Medis',
                    'Nutrisionis (Gizi)',
                    'Radiografer',
                    'Bidan',
                    'Petugas Lab',
                    'Staf Klaim BPJS',
                    'Petugas Farmasi'
                ],
                'tech' => [
                    'SIMRS',
                    'Rekam Medis Elektronik',
                    'ICD-10 Coding',
                    'BPJS Claim',
                    'Phlebotomy',
                    'Vital Signs Monitoring',
                    'Sterilisasi Alat',
                    'First Aid',
                    'Patient Care',
                    'Triage',
                    'Manajemen Obat',
                    'Inventory Farmasi',
                    'MS Office',
                    'Protokol K3 RS',
                    'Prosedur SOP Klinik'
                ],
                'soft' => [
                    'Empati',
                    'Komunikasi Pasien',
                    'Kerja Tim (Shift)',
                    'Ketelitian',
                    'Manajemen Stres',
                    'Disiplin',
                    'Tanggung Jawab',
                    'Etika Profesi',
                    'Pelayanan Prima',
                    'Kesabaran'
                ],
                'salary' => [3_500_000, 9_000_000],
            ],
            'Manufaktur' => [
                'positions' => [
                    'Operator Produksi',
                    'QC Inspector',
                    'Maintenance Technician',
                    'PPIC Staff',
                    'Warehouse Staff',
                    'Logistics Coordinator',
                    'HSE Officer',
                    'Line Leader',
                    'CNC Operator',
                    'Planner'
                ],
                'tech' => [
                    'ISO 9001',
                    'ISO 14001',
                    '5S',
                    'Kaizen',
                    'TPM',
                    'SPC',
                    'OEE',
                    'Lean Manufacturing',
                    'AutoCAD',
                    'CNC',
                    'PLC',
                    'Welding',
                    'Caliper/Micrometer',
                    'ERP',
                    'SAP',
                    'Forklift Operation',
                    'Inventory Control',
                    'Preventive Maintenance'
                ],
                'soft' => [
                    'Disiplin',
                    'Kesadaran K3',
                    'Problem Solving',
                    'Teamwork',
                    'Komunikasi',
                    'Ketelitian',
                    'Inisiatif',
                    'Kerja di Bawah Tekanan',
                    'Manajemen Waktu',
                    'Kepemimpinan (Line)'
                ],
                'salary' => [3_800_000, 10_000_000],
            ],
            'Pendidikan' => [
                'positions' => [
                    'Guru Matematika',
                    'Guru Bahasa Inggris',
                    'Guru Informatika',
                    'Asisten Dosen',
                    'Admin Akademik',
                    'Instructional Designer',
                    'Laboran',
                    'Pustakawan',
                    'Pengembang e-Learning',
                    'Konselor Siswa'
                ],
                'tech' => [
                    'Curriculum Design',
                    'Classroom Management',
                    'Assessment',
                    'E-Learning (Moodle)',
                    'Google Classroom',
                    'MS Office',
                    'Lesson Planning',
                    'Public Speaking',
                    'Research Methods',
                    'SPSS',
                    'Canva',
                    'Slide Design',
                    'Observasi Kelas',
                    'LMS Administration'
                ],
                'soft' => [
                    'Komunikasi',
                    'Kesabaran',
                    'Kreativitas',
                    'Manajemen Waktu',
                    'Empati',
                    'Kolaborasi',
                    'Problem Solving',
                    'Mentoring',
                    'Etika Profesi',
                    'Adaptasi'
                ],
                'salary' => [3_500_000, 8_000_000],
            ],
        ];

        // ===================== Helper =====================
        $randFrom = fn(array $arr) => $arr[array_rand($arr)];

        $pickSkills = function (array $pool, int $min = 5, int $max = 7) {
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

        $makeOne = function (array $conf, string $industryName) use ($zones, $randFrom, $randCoord, $pickSkills, $randSalary, $workSystems, $polaKerja, $educations) {
            $zone = $zones[array_rand($zones)];
            [$lat, $lon] = $randCoord($zone);
            [$gMin, $gMax] = $randSalary($conf['salary']);

            return [
                'personal_company_id'      => 4, // sesuaikan jika perlu
                'position_hiring'          => $randFrom($conf['positions']),
                'address_hiring'           => "Jl. " . Str::title(Str::random(8)) . " No." . rand(1, 200),
                'work_system'              => $randFrom($workSystems),
                'pola_kerja'               => $randFrom($polaKerja),
                'education_hiring'         => $randFrom($educations),
                'deadline_hiring'          => Carbon::now()->addDays(rand(7, 90)),
                'description_hiring'       => "Bertanggung jawab pada fungsi " . $industryName . " – " . Str::title(Str::random(12)),
                'created_at'               => Carbon::now(),
                'updated_at'               => Carbon::now(),
                'deleted_at'               => null,
                'ukuran_perusahaan'        => rand(10, 500) . " karyawan",
                'sektor_industri'          => $industryName,
                'kualifikasi'              => "Pengalaman " . rand(0, 5) . " tahun; mampu bekerja dalam tim; siap belajar.",
                'pengalaman_minimal_tahun' => rand(0, 5),
                'usia_maksimal'            => rand(25, 40),
                'keterampilan_teknis'      => $pickSkills($conf['tech'], 5, 7),
                'keterampilan_non_teknis'  => $pickSkills($conf['soft'], 5, 7),
                'jenis_pekerjaan'          => rand(0, 1) ? 'Kontrak' : 'Tetap',
                'gaji_min'                 => $gMin,
                'gaji_max'                 => $gMax,
                'kota'                     => 'Surabaya',
                'provinsi'                 => 'Jawa Timur',
                'latitude'                 => $lat,
                'longitude'                => $lon,
            ];
        };

        $hirings = [];

        // ========== 100 lowongan awal: 20 per domain ==========
        foreach ($domains as $industryName => $conf) {
            for ($i = 0; $i < 20; $i++) {
                $hirings[] = $makeOne($conf, $industryName);
            }
        }

        // ========== Tambahan 100 lowongan khusus IT di Surabaya ==========
        $it = $domains['Teknologi Informasi'];
        for ($i = 0; $i < 100; $i++) {
            $hirings[] = $makeOne($it, 'Teknologi Informasi');
        }

        DB::table('hirings')->insert($hirings);
    }
}
