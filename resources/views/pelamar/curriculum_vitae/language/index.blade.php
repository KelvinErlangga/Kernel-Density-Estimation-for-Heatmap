<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bahasa | CVRE GENERATE</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="icon" href="{{ asset('assets/icons/logo.svg') }}" type="image/x-icon" />

    <style>
        /* Tooltip global di atas semua elemen */
        #global-stepper-tooltip {
            z-index: 9999;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-white via-purple-50 to-blue-50" style="font-family:'Poppins',sans-serif">

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

    $currentKey = 'languages';
    $idx = array_search($currentKey, $flow, true);

    // prev / next allowed
    $backKey = null;
    for ($i=$idx-1;$i>=0;$i--) {
        if (in_array($flow[$i], $allowed, true)) {$backKey=$flow[$i]; break;}
    }
    $nextKey = null;
    for ($i=$idx+1;$i<count($flow);$i++) {
        if (in_array($flow[$i], $allowed, true)) {$nextKey=$flow[$i]; break;}
    }

    // centang hanya setelah klik Next; fallback: centang semua sebelum current bila confirmed kosong
    $useConfirmed = !empty($confirmedKeys);
    $fallbackDoneSet = [];
    if (!$useConfirmed && $idx !== false) {
        for ($i=0;$i<$idx;$i++) $fallbackDoneSet[$flow[$i]] = true;
    }

    // ===== LEVEL CEFR =====
    $levelOptions = ['A1','A2','B1','B2','C1','C2'];
@endphp

<!-- Background shape -->
<img src="{{ asset('assets/images/background.png') }}" alt="" class="pointer-events-none fixed inset-0 w-full h-full object-cover opacity-70" />

<!-- Back Button -->
<div class="absolute top-6 left-6 z-50">
    @if($backKey)
        <a href="{{ route($routeOf[$backKey], $curriculumVitaeUser->id) }}" class="text-blue-700 hover:underline flex items-center" aria-label="Kembali">
            <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
        </a>
    @else
        <a href="{{ route('pelamar.curriculum_vitae.index') }}" class="text-blue-700 hover:underline flex items-center" aria-label="Kembali ke daftar CV">
            <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
        </a>
    @endif
</div>

<!-- Stepper -->
<div class="relative z-30 pt-8">
    <div class="flex justify-center">
        <div class="flex items-center space-x-4 overflow-x-auto px-6">
            @php $visualNum = 1; @endphp
            @foreach($flow as $k)
                @php
                    $allowedStep = in_array($k, $allowed, true);
                    $isCurrent   = $currentKey === $k;

                    $done        = $useConfirmed ? in_array($k, $confirmedKeys, true)
                                                 : isset($fallbackDoneSet[$k]);
                    // current step jangan dicentang
                    if ($isCurrent) {
                        $done = false;
                    }

                    $circleCls   = $allowedStep ? 'bg-blue-700 text-white' : 'bg-gray-300 text-gray-700';
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
                           data-tooltip-text="{{ $tooltipText }}"
                           data-step-id="{{ $k }}"
                           aria-label="Step {{ $visualNum }}">
                            @if($done)
                                <img src="{{ asset('assets/images/done.svg') }}" alt="" class="w-6 h-6" />
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
</div>

<!-- Form -->
<div class="relative z-10 mt-10 mb-20 px-4">
    <form method="POST"
          action="{{ route('pelamar.curriculum_vitae.language.addLanguage', $curriculumVitaeUser->id) }}"
          enctype="multipart/form-data"
          class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl p-6 md:p-8">
        @csrf

        <h2 class="text-2xl text-center text-blue-800 mb-8">Bahasa</h2>

        <!-- List Bahasa -->
        <ul id="language-list" class="mt-8 space-y-4">
            @forelse($curriculumVitaeUser->languages as $language)
                <li class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                    <!-- Row grid: handle | name | level | proof | remove -->
                    <div class="grid grid-cols-12 gap-3 md:gap-4 items-center">
                        <!-- Drag handle -->
                        <div class="col-span-1 flex justify-center md:justify-start">
                            <button type="button" class="cursor-move text-gray-400 hover:text-gray-500" title="Seret untuk urutkan">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Bahasa -->
                        <div class="col-span-12 md:col-span-4">
                            <input type="text" name="language_name[]"
                                   value="{{ $language->language_name }}"
                                   class="language-input w-full h-11 px-3 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Bahasa" required>
                        </div>

                        <!-- Level (CEFR) -->
                        <div class="col-span-12 md:col-span-3">
                            <select name="level[]"
                                    class="level-select w-full h-11 px-3 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                <option value="" disabled {{ empty($language->category_level) ? 'selected' : '' }}>Pilih Level (CEFR)</option>
                                @foreach($levelOptions as $lv)
                                    <option value="{{ $lv }}" {{ ($language->category_level === $lv) ? 'selected' : '' }}>
                                        {{ $lv }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Proof -->
                        <div class="col-span-12 md:col-span-3">
                            <label class="block text-sm text-gray-700 mb-1">Bukti/Sertifikat (wajib)</label>
                            <input type="file" name="proof[]" accept=".pdf,image/*"
                                   class="proof-input w-full h-11 px-2 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700" />
                            <p class="text-xs text-gray-500 mt-1">PDF/JPG/PNG. Maks 2MB</p>
                            <div class="text-xs text-gray-600 mt-1 proof-filename hidden truncate"></div>
                        </div>

                        <!-- Remove -->
                        <div class="col-span-12 md:col-span-1 flex md:justify-center">
                            <button type="button" class="remove-row text-red-500 hover:text-red-600 p-2 rounded-lg hover:bg-red-50" title="Hapus baris ini">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </li>
            @empty
                <!-- Baris awal kosong -->
                <li class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                    <div class="grid grid-cols-12 gap-3 md:gap-4 items-center">
                        <div class="col-span-1 flex justify-center md:justify-start">
                            <button type="button" class="cursor-move text-gray-300" title="Seret untuk urutkan" disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                </svg>
                            </button>
                        </div>
                        <div class="col-span-12 md:col-span-4">
                            <input type="text" name="language_name[]"
                                   class="language-input w-full h-11 px-3 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Bahasa" required>
                        </div>

                        <!-- Level (CEFR) -->
                        <div class="col-span-12 md:col-span-3">
                            <select name="level[]"
                                    class="level-select w-full h-11 px-3 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                <option value="" disabled selected>Pilih Level (CEFR)</option>
                                @foreach($levelOptions as $lv)
                                    <option value="{{ $lv }}">{{ $lv }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-12 md:col-span-3">
                            <label class="block text-sm text-gray-700 mb-1">Bukti/Sertifikat (wajib)</label>
                            <input type="file" name="proof[]" accept=".pdf,image/*"
                                   class="proof-input w-full h-11 px-2 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700" />
                            <p class="text-xs text-gray-500 mt-1">PDF/JPG/PNG. Maks ~2MB.</p>
                            <div class="text-xs text-gray-600 mt-1 proof-filename hidden truncate"></div>
                        </div>
                        <div class="col-span-12 md:col-span-1 flex md:justify-center">
                            <button type="button" class="remove-row text-red-400 cursor-not-allowed p-2" disabled title="Hapus baris ini">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7H5m0 0l1.5-4A2 2 0 008.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </li>
            @endforelse
        </ul>

        <!-- Tambah Bahasa -->
        <button type="button" id="add-language-btn"
                class="mt-6 w-full py-3 md:py-4 bg-blue-100 text-blue-700 font-semibold rounded-xl shadow hover:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
            + Tambah Bahasa Lain
        </button>

        <!-- Next -->
        @if($nextKey)
            <button type="submit"
                    class="mt-4 w-full py-3 md:py-4 bg-blue-700 text-white font-semibold rounded-xl shadow hover:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
                Langkah Selanjutnya
            </button>
        @endif
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('language-list');

    if (list) {
        Sortable.create(list, {
            animation: 160,
            handle: '.cursor-move',
            ghostClass: 'bg-blue-50',
        });
    }

    // ===== Level options (CEFR) untuk template JS =====
    const levelOptions = ['A1','A2','B1','B2','C1','C2'];

    // Template baris baru
    const template = () => `
        <li class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
            <div class="grid grid-cols-12 gap-3 md:gap-4 items-center">
                <div class="col-span-1 flex justify-center md:justify-start">
                    <button type="button" class="cursor-move text-gray-400 hover:text-gray-500" title="Seret untuk urutkan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                        </svg>
                    </button>
                </div>

                <div class="col-span-12 md:col-span-4">
                    <input type="text" name="language_name[]"
                           class="language-input w-full h-11 px-3 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Bahasa" required />
                </div>

                <div class="col-span-12 md:col-span-3">
                    <select name="level[]"
                            class="level-select w-full h-11 px-3 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                        <option value="" disabled selected>Pilih Level (CEFR)</option>
                        ${levelOptions.map(lv => `<option value="${lv}">${lv}</option>`).join('')}
                    </select>
                </div>

                <div class="col-span-12 md:col-span-3">
                    <label class="block text-sm text-gray-700 mb-1">Bukti/Sertifikat (opsional)</label>
                    <input type="file" name="proof[]" accept=".pdf,image/*"
                           class="proof-input w-full h-11 px-2 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700" />
                    <p class="text-xs text-gray-500 mt-1">PDF/JPG/PNG. Maks ~2MB.</p>
                    <div class="text-xs text-gray-600 mt-1 proof-filename hidden truncate"></div>
                </div>

                <div class="col-span-12 md:col-span-1 flex md:justify-center">
                    <button type="button" class="remove-row text-red-500 hover:text-red-600 p-2 rounded-lg hover:bg-red-50" title="Hapus baris ini">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                        </svg>
                    </button>
                </div>
            </div>
        </li>
    `;

    // Tambah baris baru
    document.getElementById('add-language-btn').addEventListener('click', function () {
        const names  = Array.from(document.querySelectorAll('input.language-input'));
        const levels = Array.from(document.querySelectorAll('select.level-select'));
        let allFilled = true;
        for (let i = 0; i < names.length; i++) {
            if (!names[i].value.trim() || !levels[i].value.trim()) { allFilled = false; break; }
        }
        if (!allFilled) {
            alert('Silakan isi kolom yang masih kosong sebelum menambahkan bahasa baru.');
            return;
        }
        list.insertAdjacentHTML('beforeend', template());
    });

    // Hapus baris
    list.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-row');
        if (!btn) return;
        const li = btn.closest('li');
        if (li) li.remove();
    });

    // Tampilkan nama file yang dipilih
    document.addEventListener('change', function (e) {
        const input = e.target.closest('input.proof-input');
        if (!input) return;
        const box = input.parentElement.querySelector('.proof-filename');
        if (input.files && input.files.length) {
            box.textContent = 'Dipilih: ' + input.files[0].name;
            box.classList.remove('hidden');
        } else {
            box.textContent = '';
            box.classList.add('hidden');
        }
    });

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
