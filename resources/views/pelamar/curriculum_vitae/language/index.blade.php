<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bahasa | CVRE GENERATE</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="icon" href="{{ asset('assets/icons/logo.svg') }}" type="image/x-icon" />

    <style>
        #global-stepper-tooltip { z-index: 9999; }
        /* Tambahan agar required asterisk merah terlihat jelas */
        .required-asterisk { color: #ef4444; margin-left: 2px; }
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

    $backKey = null;
    for ($i=$idx-1;$i>=0;$i--) {
        if (in_array($flow[$i], $allowed, true)) {$backKey=$flow[$i]; break;}
    }
    $nextKey = null;
    for ($i=$idx+1;$i<count($flow);$i++) {
        if (in_array($flow[$i], $allowed, true)) {$nextKey=$flow[$i]; break;}
    }

    $useConfirmed = !empty($confirmedKeys);
    $fallbackDoneSet = [];
    if (!$useConfirmed && $idx !== false) {
        for ($i=0;$i<$idx;$i++) $fallbackDoneSet[$flow[$i]] = true;
    }

    // ===== Sistem Penguasaan Bahasa (lebih lengkap) =====
    // value = kode sistem, label = tampilan
    $systemOptions = [
        'CEFR'   => 'CEFR (A1–C2)',
        'ACTFL'  => 'ACTFL (Novice–Distinguished)',
        'ILR'    => 'ILR (0–5)',
        'CLB'    => 'CLB/NCLC (1–12)',
        'STANAG' => 'STANAG 6001 (0–5)',
        'IELTS'  => 'IELTS (Band 0–9)',
        'TOEFL'  => 'TOEFL iBT (0–120)',
        'PTE'    => 'PTE Academic (10–90)',
        'GSE'    => 'GSE Pearson (10–90)',
        'JLPT'   => 'JLPT Jepang (N5–N1)',
        'TOPIK'  => 'TOPIK Korea (1–6)',
        'HSK'    => 'HSK Mandarin (1–6)',
        // Sertifikasi yang skala levelnya mengikuti CEFR (tetap dipisah agar pilihan user jelas)
        'DELE'   => 'DELE Spanyol (A1–C2)',
        'DELF'   => 'DELF/DALF Prancis (A1–C2)',
        'GOETHE' => 'Goethe/telc Jerman (A1–C2)',
        'TORFL'  => 'TORFL/TRKI Rusia (A1–C2)',
    ];

    // Helper parse category_level (support old format "B2" -> CEFR|B2)
    $parseLevel = function ($stored) {
        $stored = (string) $stored;
        if (strpos($stored, '|') !== false) {
            [$sys, $val] = explode('|', $stored, 2);
            return [trim($sys), trim($val), $stored];
        }
        // fallback lama
        return ['CEFR', trim($stored), 'CEFR|' . trim($stored)];
    };
@endphp

<img src="{{ asset('assets/images/background.png') }}" alt="" class="pointer-events-none fixed inset-0 w-full h-full object-cover opacity-70" />

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
                    if ($isCurrent) $done = false;

                    $circleCls   = $allowedStep ? 'bg-blue-700 text-white' : 'bg-gray-300 text-gray-700';
                    if ($isCurrent && $allowedStep) $circleCls .= ' ring-2 ring-blue-300';

                    $nextK = $loop->last ? null : $flow[$loop->index + 1];
                    $nextAllowed = $nextK ? in_array($nextK, $allowed, true) : false;

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

<div class="relative z-10 mt-10 mb-20 px-4">
    <form method="POST"
          action="{{ route('pelamar.curriculum_vitae.language.addLanguage', $curriculumVitaeUser->id) }}"
          enctype="multipart/form-data"
          class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl p-6 md:p-8">
        @csrf

        <h2 class="text-2xl text-center text-blue-800 mb-8">Bahasa</h2>

        <ul id="language-list" class="mt-8 space-y-4">
            @forelse($curriculumVitaeUser->languages as $language)
                @php
                    [$sys, $val, $encoded] = $parseLevel($language->category_level);
                @endphp

                <li class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 language-row"
                    data-initial-system="{{ $sys }}"
                    data-initial-value="{{ $val }}">
                    <div class="grid grid-cols-12 gap-3 md:gap-4 items-start">

                        <div class="col-span-1 flex justify-center md:justify-start pt-8">
                            <button type="button" class="cursor-move text-gray-400 hover:text-gray-500" title="Seret untuk urutkan">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                </svg>
                            </button>
                        </div>

                        <div class="col-span-12 md:col-span-3">
                            <label class="block text-sm mb-1 invisible">Spacer</label>
                            <input type="text" name="language_name[]"
                                   value="{{ $language->language_name }}"
                                   class="language-input w-full h-11 px-3 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Bahasa" required>
                        </div>

                        <div class="col-span-12 md:col-span-2">
                            <label class="block text-sm mb-1 invisible">Spacer</label>
                            <select class="system-select w-full h-11 px-3 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                <option value="" disabled {{ empty($sys) ? 'selected' : '' }}>Sistem</option>
                                @foreach($systemOptions as $k => $label)
                                    <option value="{{ $k }}" {{ $sys === $k ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-12 md:col-span-3">
                            <label class="block text-sm mb-1 invisible">Spacer</label>
                            <div class="level-control"></div>
                            <input type="hidden" name="level[]" class="level-hidden" value="{{ $encoded }}">
                            <p class="text-xs text-gray-500 mt-1">Level/Skor sesuai sistem</p>
                        </div>

                        <div class="col-span-12 md:col-span-2">
                            <label class="block text-sm text-gray-700 mb-1">Bukti/Sertifikat <span class="required-asterisk">*</span></label>
                            <input type="file" name="proof[]" accept=".pdf,image/*" required
                                   class="proof-input w-full h-11 px-2 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700" />
                            <p class="text-xs text-gray-500 mt-1">PDF/JPG/PNG. Maks ~2MB.</p>
                            <div class="text-xs text-gray-600 mt-1 proof-filename hidden truncate"></div>
                        </div>

                        <div class="col-span-12 md:col-span-1 flex md:justify-center pt-8">
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
                <li class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 language-row"
                    data-initial-system=""
                    data-initial-value="">
                    <div class="grid grid-cols-12 gap-3 md:gap-4 items-start">

                        <div class="col-span-1 flex justify-center md:justify-start pt-8">
                            <button type="button" class="cursor-move text-gray-300" title="Seret untuk urutkan" disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                </svg>
                            </button>
                        </div>

                        <div class="col-span-12 md:col-span-3">
                            <label class="block text-sm mb-1 invisible">Spacer</label>
                            <input type="text" name="language_name[]"
                                   class="language-input w-full h-11 px-3 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Bahasa" required>
                        </div>

                        <div class="col-span-12 md:col-span-2">
                            <label class="block text-sm mb-1 invisible">Spacer</label>
                            <select class="system-select w-full h-11 px-3 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                <option value="" disabled selected>Sistem</option>
                                @foreach($systemOptions as $k => $label)
                                    <option value="{{ $k }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-12 md:col-span-3">
                            <label class="block text-sm mb-1 invisible">Spacer</label>
                            <div class="level-control"></div>
                            <input type="hidden" name="level[]" class="level-hidden" value="">
                            <p class="text-xs text-gray-500 mt-1">Level/Skor sesuai sistem</p>
                        </div>

                        <div class="col-span-12 md:col-span-2">
                            <label class="block text-sm text-gray-700 mb-1">Bukti/Sertifikat <span class="required-asterisk">*</span></label>
                            <input type="file" name="proof[]" accept=".pdf,image/*" required
                                   class="proof-input w-full h-11 px-2 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700" />
                            <p class="text-xs text-gray-500 mt-1">PDF/JPG/PNG. Maks ~2MB.</p>
                            <div class="text-xs text-gray-600 mt-1 proof-filename hidden truncate"></div>
                        </div>

                        <div class="col-span-12 md:col-span-1 flex md:justify-center pt-8">
                            <button type="button" class="remove-row text-red-400 cursor-not-allowed p-2" disabled title="Hapus baris ini">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </li>
            @endforelse
        </ul>

        <button type="button" id="add-language-btn"
                class="mt-6 w-full py-3 md:py-4 bg-blue-100 text-blue-700 font-semibold rounded-xl shadow hover:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
            + Tambah Bahasa Lain
        </button>

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
    // ==== 1. DATA MASTER DARI PHP (Untuk Filter) ====
    const allSystemOptions = @json($systemOptions);

    // ==== 2. LOGIKA MAPPING BAHASA -> SISTEM ====
    const languageRules = {
        'mandarin': ['HSK', 'CEFR'],
        'china':    ['HSK', 'CEFR'],
        'chinese':  ['HSK', 'CEFR'],
        'tiongkok': ['HSK', 'CEFR'],

        'jepang':   ['JLPT', 'CEFR'],
        'japan':    ['JLPT', 'CEFR'],

        'korea':    ['TOPIK', 'CEFR'],

        'inggris':  ['IELTS', 'TOEFL', 'PTE', 'GSE', 'CEFR'],
        'english':  ['IELTS', 'TOEFL', 'PTE', 'GSE', 'CEFR'],

        'jerman':   ['GOETHE', 'CEFR'],
        'german':   ['GOETHE', 'CEFR'],

        'prancis':  ['DELF', 'CEFR'],
        'french':   ['DELF', 'CEFR'],

        'arab':     ['ILR', 'STANAG', 'CEFR'],
        'rusia':    ['TORFL', 'CEFR'],
        'spanyol':  ['DELE', 'CEFR'],
    };

    // Fungsi Filter Dropdown
    function filterSystemOptions(textInput) {
        const row = textInput.closest('.language-row');
        const select = row.querySelector('.system-select');
        const currentVal = select.value;
        const inputVal = textInput.value.toLowerCase();

        let allowedCodes = null;

        // Cek matching keyword
        for (const [keyword, codes] of Object.entries(languageRules)) {
            if (inputVal.includes(keyword)) {
                allowedCodes = codes;
                break;
            }
        }

        // Reset Options
        select.innerHTML = '<option value="" disabled selected>Sistem</option>';

        // Re-populate options
        Object.entries(allSystemOptions).forEach(([code, label]) => {
            if (!allowedCodes || allowedCodes.includes(code)) {
                const opt = document.createElement('option');
                opt.value = code;
                opt.innerText = label;
                if (code === currentVal) opt.selected = true;
                select.appendChild(opt);
            }
        });

        // Reset jika value terpilih hilang karena filter
        if (currentVal && allowedCodes && !allowedCodes.includes(currentVal)) {
           select.value = "";
           const evt = new Event('change');
           select.dispatchEvent(evt);
        }
    }

    // Attach listener ke input yang sudah ada saat load
    document.querySelectorAll('.language-input').forEach(input => {
        input.addEventListener('input', function() {
            filterSystemOptions(this);
        });
    });

    // ==== 3. CONFIG LEVEL & RENDER ====
    const list = document.getElementById('language-list');
    if (list) {
        Sortable.create(list, {
            animation: 160,
            handle: '.cursor-move',
            ghostClass: 'bg-blue-50',
        });
    }

    const proficiencyConfig = {
        CEFR:   { type: 'select', label: 'CEFR', options: ['A1','A2','B1','B2','C1','C2'] },
        DELE:   { type: 'select', label: 'DELE', options: ['A1','A2','B1','B2','C1','C2'] },
        DELF:   { type: 'select', label: 'DELF/DALF', options: ['A1','A2','B1','B2','C1','C2'] },
        GOETHE: { type: 'select', label: 'Goethe/telc', options: ['A1','A2','B1','B2','C1','C2'] },
        TORFL:  { type: 'select', label: 'TORFL/TRKI', options: ['A1','A2','B1','B2','C1','C2'] },
        ACTFL: {
            type: 'select', label: 'ACTFL',
            options: [
                { v:'NL', l:'Novice Low' }, { v:'NM', l:'Novice Mid' }, { v:'NH', l:'Novice High' },
                { v:'IL', l:'Intermediate Low' }, { v:'IM', l:'Intermediate Mid' }, { v:'IH', l:'Intermediate High' },
                { v:'AL', l:'Advanced Low' }, { v:'AM', l:'Advanced Mid' }, { v:'AH', l:'Advanced High' },
                { v:'S',  l:'Superior' }, { v:'D',  l:'Distinguished' },
            ]
        },
        ILR:    { type: 'select', label: 'ILR', options: ['0','0+','1','1+','2','2+','3','3+','4','4+','5'] },
        STANAG: { type: 'select', label: 'STANAG 6001', options: ['0','0+','1','1+','2','2+','3','3+','4','4+','5'] },
        CLB:    { type: 'select', label: 'CLB/NCLC', options: Array.from({length: 12}, (_,i) => String(i+1)) },
        JLPT:   { type:'select', label:'JLPT', options: ['N5','N4','N3','N2','N1'] },
        TOPIK:  { type:'select', label:'TOPIK', options: ['1','2','3','4','5','6'] },
        HSK:    { type:'select', label:'HSK', options: ['1','2','3','4','5','6'] },
        IELTS:  { type:'score', label:'IELTS', min:0, max:9, step:0.5, placeholder:'0.0 – 9.0' },
        TOEFL:  { type:'score', label:'TOEFL iBT', min:0, max:120, step:1, placeholder:'0 – 120' },
        PTE:    { type:'score', label:'PTE', min:10, max:90, step:1, placeholder:'10 – 90' },
        GSE:    { type:'score', label:'GSE', min:10, max:90, step:1, placeholder:'10 – 90' },
    };

    function encode(system, value) {
        if (!system || !value) return '';
        return `${system}|${value}`;
    }

    function buildSelectOptions(system, currentValue) {
        const cfg = proficiencyConfig[system];
        if (!cfg || cfg.type !== 'select') return '';
        const opts = cfg.options;
        if (opts.length && typeof opts[0] === 'object') {
            return opts.map(o => `<option value="${o.v}" ${o.v === currentValue ? 'selected' : ''}>${o.l}</option>`).join('');
        }
        return opts.map(v => `<option value="${v}" ${v === currentValue ? 'selected' : ''}>${v}</option>`).join('');
    }

    function renderLevelControl(rowEl, system, currentValue) {
        const control = rowEl.querySelector('.level-control');
        const hidden  = rowEl.querySelector('.level-hidden');
        const cfg     = proficiencyConfig[system];

        control.innerHTML = '';
        if (!cfg) {
            hidden.value = '';
            control.innerHTML = `<div class="text-xs text-red-600">Pilih sistem terlebih dahulu</div>`;
            return;
        }

        if (cfg.type === 'select') {
            control.innerHTML = `
                <select class="level-value-select w-full h-11 px-3 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="" disabled ${currentValue ? '' : 'selected'}>Level</option>
                    ${buildSelectOptions(system, currentValue)}
                </select>
            `;
            const sel = control.querySelector('.level-value-select');
            hidden.value = encode(system, sel.value || currentValue || '');
            sel.addEventListener('change', function() { hidden.value = encode(system, this.value); });
            if (currentValue) { sel.value = currentValue; hidden.value = encode(system, currentValue); }
        }

        if (cfg.type === 'score') {
            const val = currentValue || '';
            control.innerHTML = `
                <input type="number" class="level-value-score w-full h-11 px-3 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="${cfg.placeholder || ''}" min="${cfg.min}" max="${cfg.max}" step="${cfg.step}" value="${val}" required />
            `;
            const inp = control.querySelector('.level-value-score');
            hidden.value = encode(system, inp.value);
            inp.addEventListener('input', function() { hidden.value = encode(system, this.value); });
        }
    }

    function initRow(rowEl) {
        const systemSelect = rowEl.querySelector('.system-select');
        const initialSystem = rowEl.getAttribute('data-initial-system') || systemSelect.value || '';
        const initialValue  = rowEl.getAttribute('data-initial-value') || '';

        if (initialSystem) systemSelect.value = initialSystem;
        renderLevelControl(rowEl, systemSelect.value, initialValue);

        systemSelect.addEventListener('change', function() {
            rowEl.setAttribute('data-initial-value', '');
            renderLevelControl(rowEl, this.value, '');
            rowEl.querySelector('.level-hidden').value = '';
        });
    }

    document.querySelectorAll('.language-row').forEach(initRow);

    // ==== 4. TEMPLATE ROW BARU ====
    const template = () => `
        <li class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 language-row" data-initial-system="" data-initial-value="">
            <div class="grid grid-cols-12 gap-3 md:gap-4 items-start">

                <div class="col-span-1 flex justify-center md:justify-start pt-8">
                    <button type="button" class="cursor-move text-gray-400 hover:text-gray-500" title="Seret untuk urutkan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                        </svg>
                    </button>
                </div>

                <div class="col-span-12 md:col-span-3">
                    <label class="block text-sm mb-1 invisible">Spacer</label>
                    <input type="text" name="language_name[]"
                           class="language-input w-full h-11 px-3 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Bahasa" required />
                </div>

                <div class="col-span-12 md:col-span-2">
                    <label class="block text-sm mb-1 invisible">Spacer</label>
                    <select class="system-select w-full h-11 px-3 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="" disabled selected>Sistem</option>
                        ${Object.entries(allSystemOptions).map(([k,label]) => `<option value="${k}">${label}</option>`).join('')}
                    </select>
                </div>

                <div class="col-span-12 md:col-span-3">
                    <label class="block text-sm mb-1 invisible">Spacer</label>
                    <div class="level-control"></div>
                    <input type="hidden" name="level[]" class="level-hidden" value="">
                    <p class="text-xs text-gray-500 mt-1">Level/Skor sesuai sistem</p>
                </div>

                <div class="col-span-12 md:col-span-2">
                    <label class="block text-sm text-gray-700 mb-1">Bukti/Sertifikat <span class="required-asterisk">*</span></label>
                    <input type="file" name="proof[]" accept=".pdf,image/*" required
                           class="proof-input w-full h-11 px-2 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700" />
                    <p class="text-xs text-gray-500 mt-1">PDF/JPG/PNG. Maks ~2MB.</p>
                    <div class="text-xs text-gray-600 mt-1 proof-filename hidden truncate"></div>
                </div>

                <div class="col-span-12 md:col-span-1 flex md:justify-center pt-8">
                    <button type="button" class="remove-row text-red-500 hover:text-red-600 p-2 rounded-lg hover:bg-red-50" title="Hapus baris ini">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                        </svg>
                    </button>
                </div>
            </div>
        </li>
    `;

    document.getElementById('add-language-btn').addEventListener('click', function () {
        const names  = Array.from(document.querySelectorAll('input.language-input'));
        const levels = Array.from(document.querySelectorAll('input.level-hidden'));

        let allFilled = true;
        for (let i = 0; i < names.length; i++) {
            if (!names[i].value.trim() || !levels[i].value.trim()) { allFilled = false; break; }
        }
        if (!allFilled) {
            alert('Silakan lengkapi data bahasa sebelumnya (Bahasa & Level) sebelum menambah baris baru.');
            return;
        }

        list.insertAdjacentHTML('beforeend', template());
        const newRow = list.querySelector('li.language-row:last-child');
        initRow(newRow);

        // Attach Filter Listener untuk row baru
        const newInput = newRow.querySelector('.language-input');
        newInput.addEventListener('input', function() {
            filterSystemOptions(this);
        });
    });

    list.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-row');
        if (!btn) return;
        const li = btn.closest('li');
        if (li) li.remove();
    });

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
