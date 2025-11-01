<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pendidikan | CVRE GENERATE</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="icon" href="{{ asset('assets/icons/logo.svg') }}" type="image/x-icon" />
</head>
<body class="min-h-screen flex flex-col relative bg-gradient-to-b from-white via-purple-50 to-blue-50" style="font-family:'Poppins',sans-serif">

@php
    // ==== FLOW & ROUTES ====
    $flow = [
        'personal_detail',
        'experiences',
        'educations',
        'languages',
        'skills',
        'organizations',
        'achievements',
        'links',
    ];

    $routeOf = [
        'personal_detail' => 'pelamar.curriculum_vitae.profile.index',
        'experiences'     => 'pelamar.curriculum_vitae.experience.index',
        'educations'      => 'pelamar.curriculum_vitae.education.index',
        'languages'       => 'pelamar.curriculum_vitae.language.index',
        'skills'          => 'pelamar.curriculum_vitae.skill.index',
        'organizations'   => 'pelamar.curriculum_vitae.organization.index',
        'achievements'    => 'pelamar.curriculum_vitae.achievement.index',
        'links'           => 'pelamar.curriculum_vitae.social_media.index',
    ];

    // Controller sebaiknya mengirim:
    // $allowedKeys   -> step yang boleh dikunjungi
    // $confirmedKeys -> step yang SUDAH diklik "Langkah Selanjutnya"
    $allowed       = $allowedKeys   ?? $flow;
    $confirmedKeys = $confirmedKeys ?? [];

    $currentKey = 'educations';
    $idx = array_search($currentKey, $flow, true);

    // Prev allowed
    $backKey = null;
    for ($i = $idx - 1; $i >= 0; $i--) {
        if (in_array($flow[$i], $allowed, true)) { $backKey = $flow[$i]; break; }
    }

    // Next allowed
    $nextKey = null;
    for ($i = $idx + 1; $i < count($flow); $i++) {
        if (in_array($flow[$i], $allowed, true)) { $nextKey = $flow[$i]; break; }
    }

    // ==== LOGIKA DONE (centang) ====
    // done == sudah klik Next (ada di $confirmedKeys)
    // fallback: jika $confirmedKeys kosong, anggap semua step sebelum current done
    $useConfirmed = !empty($confirmedKeys);
    $fallbackDoneSet = [];
    if (!$useConfirmed) {
        for ($i = 0; $i < $idx; $i++) { $fallbackDoneSet[$flow[$i]] = true; }
    }
@endphp

<!-- Background -->
<img src="{{ asset('assets/images/background.png') }}" alt="Background Shape" class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none" />

<!-- Back Button -->
<div class="absolute top-10 left-10 z-50">
    @if($backKey)
        <a href="{{ route($routeOf[$backKey], $curriculumVitaeUser->id) }}" class="text-blue-700 hover:underline text-sm flex items-center" aria-label="Kembali">
            <svg class="w-10 h-10 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
    @else
        <a href="{{ route('pelamar.curriculum_vitae.index') }}" class="text-blue-700 hover:underline text-sm flex items-center" aria-label="Kembali ke daftar CV">
            <svg class="w-10 h-10 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
    @endif
</div>

<!-- ===== STEPPER: centang hanya jika step ada di $confirmedKeys ===== -->
<div class="absolute top-10 left-0 right-0 z-30 flex justify-center">
    <div class="flex items-center space-x-4 overflow-x-auto">
        @php $visualNum = 1; @endphp
        @foreach($flow as $k)
            @php
                $allowedStep = in_array($k, $allowed, true);
                $isCurrent   = $currentKey === $k;

                // done jika confirmed, kalau tidak ada confirmedKeys pakai fallback (semua sebelum current done)
                $done = $useConfirmed ? in_array($k, $confirmedKeys, true)
                                      : isset($fallbackDoneSet[$k]);

                $circleCls = $allowedStep ? 'bg-blue-700 text-white' : 'bg-gray-300 text-gray-700';
                if ($isCurrent && $allowedStep) $circleCls .= ' ring-2 ring-blue-300';

                // garis ke step berikutnya
                $nextK = $loop->last ? null : $flow[$loop->index + 1];
                $nextAllowed = $nextK ? in_array($nextK, $allowed, true) : false;
            @endphp

            <div class="flex items-center space-x-4">
                @if($allowedStep)
                    <a href="{{ route($routeOf[$k], $curriculumVitaeUser->id) }}"
                       class="flex justify-center items-center w-11 h-11 rounded-full {{ $circleCls }}"
                       aria-label="Step {{ $visualNum }}">
                        @if($done)
                            <img src="{{ asset('assets/images/done.svg') }}" alt="Selesai" class="w-6 h-6" />
                        @else
                            <span class="font-bold text-xl">{{ $visualNum }}</span>
                        @endif
                    </a>
                @else
                    <div class="flex justify-center items-center w-11 h-11 rounded-full {{ $circleCls }}">
                        <span class="font-bold text-xl">{{ $visualNum }}</span>
                    </div>
                @endif

                @if(!$loop->last)
                    <div class="w-14 h-px {{ $nextAllowed ? 'bg-blue-700' : 'bg-gray-300' }}"></div>
                @endif
            </div>

            @php $visualNum++; @endphp
        @endforeach
    </div>
</div>
<!-- ===== /STEPPER ===== -->

<!-- Container -->
<div class="flex flex-col items-center justify-center z-10 mt-32 mb-20 w-full px-4">
    <div class="bg-white shadow-lg rounded-lg p-8 mx-auto z-10" style="max-width: 800px; width: 100%;">
        <h2 class="text-2xl text-center text-blue-800 mb-8">Pendidikan</h2>

        {{-- List Pendidikan (Draggable) --}}
        @if(($curriculumVitaeUser->educations ?? collect())->count())
            <ul id="education-list" class="space-y-4">
                @foreach($curriculumVitaeUser->educations as $education)
                    <li class="border border-gray-300 rounded flex items-center justify-between p-4 shadow">
                        <div class="flex items-center space-x-4">
                            <!-- Icon Drag -->
                            <div class="cursor-move text-gray-400" title="Seret untuk urutkan">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                </svg>
                            </div>

                            <!-- Detail Pendidikan -->
                            <div>
                                @php
                                    $editHref = null;
                                    if (\Illuminate\Support\Facades\Route::has('pelamar.curriculum_vitae.education.editEducation')) {
                                        $editHref = route('pelamar.curriculum_vitae.education.editEducation', [$curriculumVitaeUser->id, $education->id]);
                                    } elseif (\Illuminate\Support\Facades\Route::has('pelamar.curriculum_vitae.education.edit')) {
                                        $editHref = route('pelamar.curriculum_vitae.education.edit', [$curriculumVitaeUser->id, $education->id]);
                                    }
                                @endphp

                                @if($editHref)
                                    <a href="{{ $editHref }}" class="text-blue-700 font-semibold hover:underline">
                                        {{ $education->school_name }} | {{ $education->city_education }}
                                    </a>
                                @else
                                    <div class="text-blue-700 font-semibold">
                                        {{ $education->school_name }} | {{ $education->city_education }}
                                    </div>
                                @endif

                                @if($education->end_date)
                                    <p class="text-gray-500 text-sm">
                                        {{ $education->field_of_study }} |
                                        {{ date('M Y', strtotime($education->start_date)) }} -
                                        {{ date('M Y', strtotime($education->end_date)) }}
                                    </p>
                                @else
                                    <p class="text-gray-500 text-sm">
                                        {{ $education->field_of_study }} |
                                        {{ date('M Y', strtotime($education->start_date)) }} - Sekarang
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Aksi -->
                        <div class="flex items-center gap-2">
                            @if($editHref)
                                <a href="{{ $editHref }}"
                                   class="px-3 py-1 text-sm rounded border border-blue-600 text-blue-600 hover:bg-blue-50">
                                    Edit
                                </a>
                            @endif
                            <form action="{{ route('pelamar.curriculum_vitae.education.deleteEducation', [$curriculumVitaeUser->id, $education->id]) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus data pendidikan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="rounded border border-dashed border-gray-300 p-6 text-center text-gray-600">
                Belum ada data pendidikan yang ditambahkan.
            </div>
        @endif

        <!-- Tombol Tambah Pendidikan -->
        <a href="{{ route('pelamar.curriculum_vitae.education.createEducation', $curriculumVitaeUser->id) }}"
           class="mt-6 w-full py-4 bg-blue-100 text-blue-700 text-sm font-bold rounded shadow hover:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition text-center block">
            + Tambah Pendidikan
        </a>

        <!-- Langkah Selanjutnya -->
        @if($nextKey)
            {{-- IMPORTANT: di controller tujuan, tambahkan 'educations' ke $confirmedKeys sebelum redirect --}}
            <a href="{{ route($routeOf[$nextKey], $curriculumVitaeUser->id) }}"
               class="mt-6 w-full py-4 bg-blue-700 text-white text-sm font-bold rounded shadow hover:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition text-center block">
                Langkah Selanjutnya
            </a>
        @endif
    </div>
</div>

<!-- Script SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const eduList = document.getElementById('education-list');
        if (eduList) {
            Sortable.create(eduList, {
                animation: 150,
                handle: '.cursor-move',
                ghostClass: 'bg-blue-50',
            });
        }
    });
</script>
</body>
</html>
