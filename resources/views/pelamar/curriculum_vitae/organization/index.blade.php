<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Pengalaman Organisasi | CVRE GENERATE</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
    <link rel="icon" href="{{ asset('assets/icons/logo.svg') }}" type="image/x-icon"/>

    <style>
        /* Tooltip global harus paling atas */
        #global-stepper-tooltip {
            z-index: 9999;
        }
    </style>
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

    // Label tooltip
    $translations = [
        'personal detail' => 'Detail Pribadi',
        'experiences'     => 'Pengalaman Kerja',
        'educations'      => 'Pendidikan',
        'languages'       => 'Bahasa',
        'skills'          => 'Keahlian',
        'organizations'   => 'Organisasi',
        'achievements'    => 'Penghargaan',
        'links'           => 'Tautan & Media Sosial',
    ];

    // Dari controller:
    // $allowedKeys   -> step yang boleh dikunjungi
    // $confirmedKeys -> step yang SUDAH klik "Langkah Selanjutnya"
    $allowed       = $allowedKeys   ?? $flow;
    $confirmedKeys = $confirmedKeys ?? [];

    $currentKey = 'organizations';
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
    // fallback: kalau $confirmedKeys kosong, anggap semua sebelum current done
    $useConfirmed = !empty($confirmedKeys);
    $fallbackDoneSet = [];
    if (!$useConfirmed && $idx !== false) {
        for ($i = 0; $i < $idx; $i++) { $fallbackDoneSet[$flow[$i]] = true; }
    }
@endphp

<!-- Background -->
<img src="{{ asset('assets/images/background.png') }}" alt="Background Shape" class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none"/>

<!-- Back Button -->
<div class="absolute top-10 left-10 z-50">
    @if($backKey)
        <a href="{{ route($routeOf[$backKey], $curriculumVitaeUser->id) }}" class="text-blue-700 hover:underline text-sm flex items-center" aria-label="Kembali">
            <svg class="w-10 h-10 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
    @else
        <a href="{{ route('pelamar.curriculum_vitae.index') }}" class="text-blue-700 hover:underline text-sm flex items-center" aria-label="Kembali ke daftar CV">
            <svg class="w-10 h-10 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
    @endif
</div>

<!-- ===== STEPPER (punya tooltip & animasi global) ===== -->
<div class="absolute top-10 left-0 right-0 z-30 flex justify-center">
    <div class="flex items-center space-x-4 overflow-x-auto">
        @php $visualNum = 1; @endphp
        @foreach($flow as $k)
            @php
                $allowedStep = in_array($k, $allowed, true);
                $isCurrent   = $currentKey === $k;

                $done = $useConfirmed ? in_array($k, $confirmedKeys, true)
                                      : isset($fallbackDoneSet[$k]);

                // Di step current tetap angka, bukan centang
                if ($isCurrent) {
                    $done = false;
                }

                $circleCls = $allowedStep ? 'bg-blue-700 text-white' : 'bg-gray-300 text-gray-700';
                if ($isCurrent && $allowedStep) $circleCls .= ' ring-2 ring-blue-300';

                $nextK = $loop->last ? null : $flow[$loop->index + 1];
                $nextAllowed = $nextK ? in_array($nextK, $allowed, true) : false;

                // tooltip
                $tooltipKey  = strtolower(str_replace('_', ' ', $k));
                $tooltipText = $translations[$tooltipKey] ?? ucwords($tooltipKey);
            @endphp

            <div class="flex items-center space-x-4 group">
                @if($allowedStep)
                    <a href="{{ route($routeOf[$k], $curriculumVitaeUser->id) }}"
                       class="stepper-link flex justify-center items-center w-11 h-11 rounded-full {{ $circleCls }} relative z-10"
                       aria-label="Step {{ $visualNum }}"
                       data-tooltip-text="{{ $tooltipText }}"
                       data-step-id="{{ $k }}">
                        @if($done)
                            <img src="{{ asset('assets/images/done.svg') }}" alt="Selesai" class="w-6 h-6" />
                        @else
                            <span class="font-bold text-xl">{{ $visualNum }}</span>
                        @endif
                    </a>
                @else
                    <div class="stepper-link flex justify-center items-center w-11 h-11 rounded-full {{ $circleCls }} relative z-10"
                         data-tooltip-text="{{ $tooltipText }}"
                         data-step-id="{{ $k }}">
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
<div class="flex flex-col items-center justify-center z-10 mt-32 mb-20 px-4">
    <div class="bg-white shadow-lg rounded-2xl p-8 mx-auto z-10 mb-20" style="max-width: 800px; width: 100%;">
        <h2 class="text-2xl text-center text-blue-800 mb-2">
            Pengalaman Organisasi <span class="text-gray-500 text-sm">(opsional)</span>
        </h2>

        <!-- List (Draggable) -->
        <ul id="organization-list" class="space-y-4">
            @forelse($curriculumVitaeUser->organizations as $organization)
                <li class="border border-gray-200 rounded-xl flex items-center justify-between p-4 shadow-sm bg-white">
                    <div class="flex items-center space-x-4">
                        <div class="cursor-move text-gray-400" title="Seret untuk mengurutkan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                            </svg>
                        </div>
                        <a href="{{ route('pelamar.curriculum_vitae.organization.EditOrganization', [$curriculumVitaeUser->id, $organization->id]) }}" class="block text-left">
                            <h3 class="text-blue-700 font-semibold">
                                {{ $organization->position_organization }} | {{ $organization->city_organization }}
                            </h3>
                            @if($organization->end_date)
                                <p class="text-gray-500 text-sm">
                                    {{ $organization->organization_name }} |
                                    {{ date('M Y', strtotime($organization->start_date)) }} -
                                    {{ date('M Y', strtotime($organization->end_date)) }}
                                </p>
                            @else
                                <p class="text-gray-500 text-sm">
                                    {{ $organization->organization_name }} |
                                    {{ date('M Y', strtotime($organization->start_date)) }} - Sekarang
                                </p>
                            @endif
                        </a>
                    </div>

                    <form action="{{ route('pelamar.curriculum_vitae.organization.deleteOrganization', [$curriculumVitaeUser->id, $organization->id]) }}"
                          method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus pengalaman organisasi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012-1.87L19 7M10 11v6m4-6v6"/>
                            </svg>
                        </button>
                    </form>
                </li>
            @empty
                <li class="rounded-xl p-6 border border-dashed border-gray-300 text-center text-gray-600 bg-white">
                    Belum ada pengalaman organisasi. Klik tombol di bawah untuk menambahkan.
                </li>
            @endforelse
        </ul>

        <!-- Tombol Tambah (sekunder — match Index Bahasa) -->
        <a href="{{ route('pelamar.curriculum_vitae.organization.createOrganization', $curriculumVitaeUser->id) }}"
           class="mt-6 block w-full text-center py-4 bg-blue-100 text-blue-700 text-sm font-bold rounded-xl shadow hover:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
            + Tambah Pengalaman Organisasi
        </a>

        <!-- Tombol Next (primer — match Index Bahasa) -->
        @if($nextKey)
            <a href="{{ route($routeOf[$nextKey], $curriculumVitaeUser->id) }}"
               class="mt-6 block w-full text-center py-4 bg-blue-700 text-white text-sm font-bold rounded-xl shadow hover:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
                Langkah Selanjutnya
            </a>
        @endif
    </div>
</div>

<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Drag & drop organisasi
    const listEl = document.getElementById('organization-list');
    if (listEl) {
        Sortable.create(listEl, {
            animation: 150,
            handle: '.cursor-move',
            ghostClass: 'bg-blue-50'
        });
    }

    // === TOOLTIP GLOBAL UNTUK STEPPER ===
    const tooltip = document.createElement('span');
    tooltip.id = 'global-stepper-tooltip';
    tooltip.className =
        'absolute z-[9999] opacity-0 transition-all duration-300 ease-out ' +
        'bg-gray-800 text-white text-xs font-medium rounded-lg px-3 py-1 ' +
        'shadow-lg whitespace-nowrap pointer-events-none transform -translate-x-1/2 scale-x-0 origin-center';
    document.body.appendChild(tooltip);

    const stepperLinks = document.querySelectorAll('.stepper-link');

    stepperLinks.forEach(link => {
        link.addEventListener('mouseenter', function () {
            const text = this.getAttribute('data-tooltip-text');
            const rect = this.getBoundingClientRect();

            tooltip.textContent = text;
            tooltip.style.left = (rect.left + rect.width / 2) + 'px';
            tooltip.style.top  = (rect.top - 32) + 'px';

            tooltip.classList.remove('opacity-0', 'scale-x-0');
            tooltip.classList.add('opacity-100', 'scale-x-100');
        });

        link.addEventListener('mouseleave', function () {
            tooltip.classList.remove('opacity-100', 'scale-x-100');
            tooltip.classList.add('opacity-0', 'scale-x-0');
        });
    });
});
</script>
</body>
</html>
