<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Keahlian | CVRE GENERATE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" />
    <link rel="icon" href="{{ asset('assets/icons/logo.svg') }}" type="image/x-icon" />

    <style>
        /* Tooltip global harus di atas semua elemen */
        #global-stepper-tooltip {
            z-index: 9999;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col relative bg-gradient-to-b from-white via-purple-50 to-blue-50" style="font-family:'Poppins',sans-serif">

@php
    // ==== FLOW & ROUTES ====
    $flow = [
        'personal_detail','experiences','educations','languages',
        'skills','organizations','achievements','links',
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

    $allowed       = $allowedKeys   ?? $flow;
    $confirmedKeys = $confirmedKeys ?? [];

    $currentKey = 'skills';
    $idx = array_search($currentKey, $flow, true);

    // Prev
    $backKey = null;
    for ($i = $idx - 1; $i >= 0; $i--) {
        if (in_array($flow[$i], $allowed, true)) { $backKey = $flow[$i]; break; }
    }
    // Next
    $nextKey = null;
    for ($i = $idx + 1; $i < count($flow); $i++) {
        if (in_array($flow[$i], $allowed, true)) { $nextKey = $flow[$i]; break; }
    }

    // Centang (confirmed) + fallback sebelum current
    $useConfirmed = !empty($confirmedKeys);
    $fallbackDoneSet = [];
    if (!$useConfirmed && $idx !== false) {
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

<!-- ===== STEPPER + TOOLTIP ===== -->
<div class="absolute top-10 left-0 right-0 z-30 flex justify-center">
    <div class="flex items-center space-x-4 overflow-x-auto">
        @php $visualNum = 1; @endphp
        @foreach($flow as $k)
            @php
                $allowedStep = in_array($k, $allowed, true);
                $isCurrent   = $currentKey === $k;

                $done = $useConfirmed ? in_array($k, $confirmedKeys, true)
                                      : isset($fallbackDoneSet[$k]);
                // di step current tetap angka, bukan centang
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

<!-- Form Container -->
<div class="flex flex-col items-center justify-center z-10 mt-32 mb-20">
    <form action="{{ route('pelamar.curriculum_vitae.skill.addSkill', $curriculumVitaeUser->id) }}"
          method="POST"
          class="bg-white shadow-lg rounded-2xl p-8 mx-auto z-10 mb-20 grid grid-cols-1 md:grid-cols-2 gap-8"
          style="max-width: 1000px; width: 100%;">
        @csrf
        <h2 class="text-2xl text-center text-blue-800 md:col-span-2 mb-2">Keahlian</h2>

        <!-- Left: Keahlian & Level -->
        <div class="space-y-6">
            <ul id="skill-list" class="space-y-4">
                @forelse($curriculumVitaeUser->skills as $skill)
                    <li class="rounded flex items-center justify-between">
                        <div class="flex items-center space-x-4 w-full">
                            <!-- Drag Icon -->
                            <div class="cursor-move text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                </svg>
                            </div>
                            <div class="grid grid-cols-2 gap-4 w-full">
                                <div class="col-span-1">
                                    <input type="text"
                                           name="skill_name[]"
                                           value="{{ $skill->skill_name }}"
                                           class="skill-input block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-3"
                                           placeholder="Keahlian"
                                           required />
                                    @error('skill_name')
                                        <div class="text-sm font-thin text-red-500">Keahlian harus diisi</div>
                                    @enderror
                                </div>
                                <div class="col-span-1">
                                    <select name="level[]"
                                            class="level-select block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                                            style="height: 50px; padding: 0 10px;"
                                            required>
                                        <option value="{{ $skill->category_level }}">{{ $skill->category_level }}</option>
                                        <option value="Beginer">Beginer</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Expert">Expert</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="text-red-500 hover:text-red-700 transition ml-4 remove-row" title="Hapus baris ini">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012-1.87L19 7M10 11v6m4-6v6" />
                            </svg>
                        </button>
                    </li>
                @empty
                    <!-- Baris awal minimal -->
                    <li class="rounded flex items-center justify-between">
                        <div class="flex items-center space-x-4 w-full">
                            <div class="cursor-move text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                </svg>
                            </div>
                            <div class="grid grid-cols-2 gap-4 w-full">
                                <div class="col-span-1">
                                    <input type="text"
                                           name="skill_name[]"
                                           class="skill-input block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-3"
                                           placeholder="Keahlian"
                                           required />
                                    @error('skill_name')
                                        <div class="text-sm font-thin text-red-500">Keahlian harus diisi</div>
                                    @enderror
                                </div>
                                <div class="col-span-1">
                                    <select name="level[]"
                                            class="level-select block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                                            style="height: 50px; padding: 0 10px;"
                                            required>
                                        <option value="" disabled selected>Pilih Level</option>
                                        <option value="Beginer">Beginer</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Expert">Expert</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="text-red-500 hover:text-red-700 transition ml-4 remove-row" title="Hapus baris ini">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012-1.87L19 7M10 11v6m4-6v6" />
                            </svg>
                        </button>
                    </li>
                @endforelse
            </ul>

            <!-- Tombol sekunder: match index Bahasa -->
            <button type="button" id="add-skill-btn"
                    class="block w-full text-center py-3 md:py-4 bg-blue-100 text-blue-700 font-semibold rounded-xl shadow hover:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
                + Tambah Keahlian Lain
            </button>
        </div>

        <!-- Right: Pencarian Bidang Pekerjaan -->
        <div class="space-y-6 border border-gray-200 rounded-xl p-4 bg-gray-50">
            <label for="search-job" class="text-sm text-gray-600">Pencarian Rekomendasi</label>
            <input
                type="text"
                id="search-job"
                placeholder="Cari berdasarkan bidang kerja…"
                class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-3" />
            <ul id="job-list" class="space-y-2 hidden"></ul>
        </div>

        <!-- Submit (primer) -->
        <div class="md:col-span-2">
            @if($nextKey)
                <button type="submit"
                        class="block w-full text-center py-3 md:py-4 bg-blue-700 text-white font-semibold rounded-xl shadow hover:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
                    Langkah Selanjutnya
                </button>
            @endif
        </div>
    </form>
</div>

<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Sortable
    const skillList = document.getElementById('skill-list');
    if (skillList) {
        Sortable.create(skillList, {
            animation: 150,
            handle: '.cursor-move',
            ghostClass: 'bg-blue-50',
        });
    }

    // Template row
    const skillRowTemplate = () => `
        <li class="rounded flex items-center justify-between">
            <div class="flex items-center space-x-4 w-full">
                <div class="cursor-move text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                    </svg>
                </div>
                <div class="grid grid-cols-2 gap-4 w-full">
                    <div class="col-span-1">
                        <input type="text" name="skill_name[]" class="skill-input block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-3" placeholder="Keahlian" required />
                    </div>
                    <div class="col-span-1">
                        <select name="level[]" class="level-select block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height: 50px; padding: 0 10px;" required>
                            <option value="" disabled selected>Pilih Level</option>
                            <option value="Beginer">Beginer</option>
                            <option value="Medium">Medium</option>
                            <option value="Expert">Expert</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="button" class="text-red-500 hover:text-red-700 transition ml-4 remove-row" title="Hapus baris ini">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012-1.87L19 7M10 11v6m4-6v6" />
                </svg>
            </button>
        </li>
    `;

    // Add row with validation
    const addBtn = document.getElementById('add-skill-btn');
    addBtn.addEventListener('click', () => {
        const names  = Array.from(document.querySelectorAll('input.skill-input'));
        const levels = Array.from(document.querySelectorAll('select.level-select'));
        let allFilled = true;
        for (let i = 0; i < names.length; i++) {
            if (!names[i].value.trim() || !levels[i].value.trim()) { allFilled = false; break; }
        }
        if (!allFilled) {
            alert('Pastikan semua keahlian & level terisi sebelum menambahkan baris baru.');
            return;
        }
        skillList.insertAdjacentHTML('beforeend', skillRowTemplate());
    });

    // Remove row
    skillList.addEventListener('click', (e) => {
        const btn = e.target.closest('.remove-row');
        if (!btn) return;
        const li = btn.closest('li');
        if (li) li.remove();
    });

    // Job skills search (opsional)
    const jobList   = document.getElementById('job-list');
    const searchInp = document.getElementById('search-job');
    let jobSkills   = {};

    fetch('/api/job-skills')
        .then(res => res.ok ? res.json() : {})
        .then(data => { jobSkills = data || {}; })
        .catch(() => { jobSkills = {}; });

    searchInp.addEventListener('input', () => {
        const q = (searchInp.value || '').toLowerCase();
        jobList.innerHTML = '';
        if (q.length < 3) { jobList.classList.add('hidden'); return; }
        let count = 0;
        Object.keys(jobSkills).forEach(job => {
            if (job.toLowerCase().includes(q)) {
                (jobSkills[job] || []).forEach(skill => {
                    const li = document.createElement('li');
                    li.className = 'text-gray-700 flex justify-between items-center bg-white border border-gray-200 p-2 rounded shadow-sm';
                    const span = document.createElement('span');
                    span.textContent = `${job} • ${skill}`;
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'text-blue-600 hover:text-blue-800';
                    btn.textContent = 'Pilih';
                    btn.addEventListener('click', () => addSkillToForm(skill));
                    li.appendChild(span); li.appendChild(btn); jobList.appendChild(li); count++;
                });
            }
        });
        jobList.classList.toggle('hidden', count === 0);
    });

    function addSkillToForm(skill) {
        const existing = Array.from(document.querySelectorAll('#skill-list input[name="skill_name[]"]'))
                              .map(i => i.value.trim().toLowerCase());
        if (existing.includes(skill.toLowerCase())) {
            alert('Skill sudah dipilih.'); return;
        }
        const temp = document.createElement('div');
        temp.innerHTML = skillRowTemplate().trim();
        const li = temp.firstElementChild;
        li.querySelector('input[name="skill_name[]"]').value = skill;
        skillList.appendChild(li);
    }

    // === TOOLTIP GLOBAL UNTUK STEPPER ===
    const tooltip = document.createElement('span');
    tooltip.id = 'global-stepper-tooltip';
    tooltip.className = 'absolute z-[9999] opacity-0 transition-all duration-300 ease-out bg-gray-800 text-white text-xs font-medium rounded-lg px-3 py-1 shadow-lg whitespace-nowrap pointer-events-none transform -translate-x-1/2 scale-x-0 origin-center';
    document.body.appendChild(tooltip);

    const stepperLinks = document.querySelectorAll('.stepper-link');

    stepperLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            const text = this.getAttribute('data-tooltip-text');
            const rect = this.getBoundingClientRect();

            tooltip.textContent = text;
            tooltip.style.left = (rect.left + rect.width / 2) + 'px';
            tooltip.style.top  = (rect.top - 32) + 'px';

            tooltip.classList.remove('opacity-0', 'scale-x-0');
            tooltip.classList.add('opacity-100', 'scale-x-100');
        });

        link.addEventListener('mouseleave', function() {
            tooltip.classList.remove('opacity-100', 'scale-x-100');
            tooltip.classList.add('opacity-0', 'scale-x-0');
        });
    });
});
</script>
</body>
</html>
