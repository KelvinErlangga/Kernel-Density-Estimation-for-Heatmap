<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pengalaman Kerja | CVRE GENERATE</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="icon" href="{{ asset('assets/icons/logo.svg') }}" type="image/x-icon" />
</head>
<body class="bg-gradient-to-b from-white via-purple-50 to-blue-50" style="font-family:'Poppins',sans-serif">

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

    $allowed        = $allowedKeys ?? $flow;
    $confirmedKeys  = $confirmedKeys ?? [];
    $currentKey     = 'experiences';

    // ==== NAV: PREV/NEXT YANG DIIJINKAN ====
    $idx = array_search($currentKey, $flow, true);

    $backKey = null;
    for ($i = $idx - 1; $i >= 0; $i--) {
        if (in_array($flow[$i], $allowed, true)) { $backKey = $flow[$i]; break; }
    }

    $nextKey = null;
    for ($i = $idx + 1; $i < count($flow); $i++) {
        if (in_array($flow[$i], $allowed, true)) { $nextKey = $flow[$i]; break; }
    }

    // ==== LOGIKA DONE (centang) ====
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
        <a href="{{ route($routeOf[$backKey], $curriculumVitaeUser->id) }}" class="text-blue-700 hover:underline text-sm flex items-center" title="Kembali">
            <svg class="w-10 h-10 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
    @else
        <a href="{{ route('pelamar.curriculum_vitae.index') }}" class="text-blue-700 hover:underline text-sm flex items-center" title="Kembali ke daftar CV">
            <svg class="w-10 h-10 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
    @endif
</div>

<!-- ===== STEPPER: centang hanya jika sudah "Next" (confirmed) ===== -->
<div class="absolute top-10 left-0 right-0 z-30 flex justify-center">
    <div class="flex items-center space-x-4 overflow-x-auto">
        @php $visualNum = 1; @endphp
        @foreach($flow as $k)
            @php
                $allowedStep = in_array($k, $allowed, true);
                $isCurrent   = $currentKey === $k;

                $done = $useConfirmed ? in_array($k, $confirmedKeys, true)
                                      : isset($fallbackDoneSet[$k]);

                $circleCls = $allowedStep ? 'bg-blue-700 text-white' : 'bg-gray-300 text-gray-700';
                if ($isCurrent && $allowedStep) $circleCls .= ' ring-2 ring-blue-300';

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

<!-- Content -->
<div class="min-h-screen flex flex-col relative">
    <div class="flex flex-col items-center justify-center z-10 mt-32 mb-20 w-full px-4">
        <div class="bg-white shadow-lg rounded-2xl p-8 mx-auto" style="max-width:800px; width:100%;">
            <h2 class="text-2xl text-center text-blue-800 mb-8">Pengalaman Kerja</h2>

            @if(($curriculumVitaeUser->experiences ?? collect())->count())
                <ul id="experience-list" class="space-y-4">
                    @foreach($curriculumVitaeUser->experiences as $experience)
                        <li class="border border-gray-300 rounded flex items-center justify-between p-4 shadow">
                            <div class="flex items-center space-x-4">
                                <div class="cursor-move text-gray-400" title="Seret untuk urutkan">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                    </svg>
                                </div>
                                <div>
                                    @php
                                        $editHref = null;
                                        if (\Illuminate\Support\Facades\Route::has('pelamar.curriculum_vitae.experience.editExperience')) {
                                            $editHref = route('pelamar.curriculum_vitae.experience.editExperience', [$curriculumVitaeUser->id, $experience->id]);
                                        } elseif (\Illuminate\Support\Facades\Route::has('pelamar.curriculum_vitae.experience.edit')) {
                                            $editHref = route('pelamar.curriculum_vitae.experience.edit', [$curriculumVitaeUser->id, $experience->id]);
                                        }
                                    @endphp

                                    @if($editHref)
                                        <a href="{{ $editHref }}" class="text-blue-700 font-semibold hover:underline">
                                            {{ $experience->position_experience }} | {{ $experience->company_experience }}
                                        </a>
                                    @else
                                        <div class="text-blue-700 font-semibold">
                                            {{ $experience->position_experience }} | {{ $experience->company_experience }}
                                        </div>
                                    @endif

                                    @if($experience->end_date)
                                        <p class="text-gray-500 text-sm">
                                            {{ $experience->city_experience }} |
                                            {{ date('M Y', strtotime($experience->start_date)) }} -
                                            {{ date('M Y', strtotime($experience->end_date)) }}
                                        </p>
                                    @else
                                        <p class="text-gray-500 text-sm">
                                            {{ $experience->city_experience }} |
                                            {{ date('M Y', strtotime($experience->start_date)) }} - Sekarang
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($editHref)
                                    <a href="{{ $editHref }}" class="px-3 py-1 text-sm rounded border border-blue-600 text-blue-600 hover:bg-blue-50">Edit</a>
                                @endif
                                <form action="{{ route('pelamar.curriculum_vitae.experience.deleteExperience', [$curriculumVitaeUser->id, $experience->id]) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus pengalaman ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="rounded border border-dashed border-gray-300 p-6 text-center text-gray-600">
                    Belum ada pengalaman kerja yang ditambahkan.
                </div>
            @endif

            <!-- CTA Buttons (match style Index Bahasa) -->
            <div class="mt-6 space-y-3">
                <a href="{{ route('pelamar.curriculum_vitae.experience.create', $curriculumVitaeUser->id) }}"
                   class="block w-full text-center py-3 md:py-4 bg-blue-100 text-blue-700 font-semibold rounded-xl shadow hover:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
                    + Tambah Pengalaman Kerja
                </a>

                @if($nextKey)
                    {{-- IMPORTANT (di controller next): tambahkan 'experiences' ke $confirmedKeys lalu redirect --}}
                    <a href="{{ route($routeOf[$nextKey], $curriculumVitaeUser->id) }}"
                       class="block w-full text-center py-3 md:py-4 bg-blue-700 text-white font-semibold rounded-xl shadow hover:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
                        Langkah Selanjutnya
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const list = document.getElementById('experience-list');
        if (list) {
            Sortable.create(list, {
                animation: 150,
                handle: '.cursor-move',
                ghostClass: 'bg-blue-50',
            });
        }
    });
</script>
</body>
</html>
