<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bahasa | CVRE GENERATE</title>
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

    // Dari controller:
    // $allowedKeys   -> step yang boleh dikunjungi sekarang
    // $confirmedKeys -> step yang SUDAH diklik "Langkah Selanjutnya" (tanda centang)
    $allowed       = $allowedKeys   ?? $flow;
    $confirmedKeys = $confirmedKeys ?? [];

    $currentKey = 'languages';
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

    // ==== LOGIKA DONE (centang hanya setelah klik Next) ====
    $useConfirmed = !empty($confirmedKeys);
    $fallbackDoneSet = [];
    if (!$useConfirmed) {
        // Fallback UX: jika belum ada confirmedKeys sama sekali,
        // tampilkan centang untuk semua step SEBELUM current agar progres terasa.
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

<!-- ===== STEPPER (centang hanya jika step sudah dikonfirmasi/Next) ===== -->
<div class="absolute top-10 left-0 right-0 z-30 flex justify-center">
    <div class="flex items-center space-x-4 overflow-x-auto">
        @php $visualNum = 1; @endphp
        @foreach($flow as $k)
            @php
                $allowedStep = in_array($k, $allowed, true);
                $isCurrent   = $currentKey === $k;

                // done: hanya jika ada di $confirmedKeys, atau fallback untuk step sebelum current bila $confirmedKeys kosong
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

<!-- Form Container -->
<div class="flex flex-col items-center justify-center z-10 mt-32 mb-20 w-full px-4">
    <form method="POST"
          action="{{ route('pelamar.curriculum_vitae.language.addLanguage', $curriculumVitaeUser->id) }}"
          class="bg-white shadow-lg rounded-lg p-8 mx-auto z-10 mb-20"
          style="max-width: 800px; width: 100%;">
        @csrf
        <h2 class="text-2xl text-center text-blue-800 mb-8">Bahasa</h2>

        <!-- List Bahasa (Draggable) -->
        <ul id="language-list" class="space-y-4">
            @forelse($curriculumVitaeUser->languages as $language)
                <li class="rounded flex items-center justify-between">
                    <div class="flex items-center space-x-4 w-full">
                        <div class="cursor-move text-gray-400" title="Seret untuk urutkan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                            </svg>
                        </div>
                        <div class="grid grid-cols-2 gap-4 w-full">
                            <div class="col-span-1">
                                <input type="text"
                                       name="language_name[]"
                                       value="{{ $language->language_name }}"
                                       class="language-input block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-3"
                                       placeholder="Bahasa"
                                       required />
                                @error('language_name')
                                    <div class="text-sm font-thin text-red-500">Bahasa harus diisi</div>
                                @enderror
                            </div>
                            <div class="col-span-1">
                                <select name="level[]"
                                        class="level-select block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                                        style="height:45px;padding:0 10px"
                                        required>
                                    <option value="{{ $language->category_level }}">{{ $language->category_level }}</option>
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
                                  d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                        </svg>
                    </button>
                </li>
            @empty
                <li class="rounded flex items-center justify-between">
                    <div class="flex items-center space-x-4 w-full">
                        <div class="cursor-move text-gray-400" title="Seret untuk urutkan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                            </svg>
                        </div>
                        <div class="grid grid-cols-2 gap-4 w-full">
                            <div class="col-span-1">
                                <input type="text" name="language_name[]"
                                       class="language-input block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-3"
                                       placeholder="Bahasa" required />
                                @error('language_name')
                                    <div class="text-sm font-thin text-red-500">Bahasa harus diisi</div>
                                @enderror
                            </div>
                            <div class="col-span-1">
                                <select name="level[]"
                                        class="level-select block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                                        style="height:45px;padding:0 10px"
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                        </svg>
                    </button>
                </li>
            @endforelse
        </ul>

        <!-- Tambah Bahasa -->
        <button type="button" id="add-language-btn"
                class="mt-6 w-full py-4 bg-blue-100 text-blue-700 text-sm font-bold rounded shadow hover:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition text-center block">
            + Tambah Bahasa Lain
        </button>

        <!-- Langkah Selanjutnya (SUBMIT) -->
        @if($nextKey)
            {{-- Controller tujuan harus menambahkan 'languages' ke $confirmedKeys sebelum redirect --}}
            <button type="submit"
                    class="mt-6 w-full py-4 bg-blue-700 text-white text-sm font-medium rounded shadow hover:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
                Langkah Selanjutnya
            </button>
        @endif
    </form>
</div>

<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const list = document.getElementById('language-list');
        if (list) {
            Sortable.create(list, {
                animation: 150,
                handle: '.cursor-move',
                ghostClass: 'bg-blue-50',
            });
        }

        const template = () => `
            <li class="rounded flex items-center justify-between">
                <div class="flex items-center space-x-4 w-full">
                    <div class="cursor-move text-gray-400" title="Seret untuk urutkan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                        </svg>
                    </div>
                    <div class="grid grid-cols-2 gap-4 w-full">
                        <div class="col-span-1">
                            <input type="text" name="language_name[]" class="language-input block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-3" placeholder="Bahasa" required />
                        </div>
                        <div class="col-span-1">
                            <select name="level[]" class="level-select block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height:45px;padding:0 10px" required>
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
                              d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                    </svg>
                </button>
            </li>
        `;

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

        list.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-row');
            if (!btn) return;
            const li = btn.closest('li');
            if (li) li.remove();
        });
    });
</script>
</body>
</html>
