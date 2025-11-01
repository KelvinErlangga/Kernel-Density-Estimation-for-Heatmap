<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Link Informasi | CVRE GENERATE</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"/>
    <link rel="icon" href="{{ asset('assets/icons/logo.svg') }}" type="image/x-icon"/>
</head>
<body class="bg-gradient-to-b from-white via-purple-50 to-blue-50 min-h-screen" style="font-family:'Poppins',sans-serif">
@php
    // ==== FLOW & ROUTES ====
    $flow = [
        'personal_detail','experiences','educations','languages',
        'skills','organizations','achievements','links'
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

    // DARI CONTROLLER:
    // $allowedKeys   -> step yang boleh diakses
    // $confirmedKeys -> step yang SUDAH diklik "Langkah Selanjutnya"
    $allowed       = $allowedKeys   ?? $flow;
    $confirmedKeys = $confirmedKeys ?? [];

    $currentKey = 'links';
    $idx = array_search($currentKey, $flow, true);

    // Prev allowed
    $backKey = null;
    for ($i = $idx - 1; $i >= 0; $i--) {
        if (in_array($flow[$i], $allowed, true)) { $backKey = $flow[$i]; break; }
    }
    // Next allowed (untuk 'links' biasanya null)
    $nextKey = null;
    for ($i = $idx + 1; $i < count($flow); $i++) {
        if (in_array($flow[$i], $allowed, true)) { $nextKey = $flow[$i]; break; }
    }

    // ==== LOGIKA DONE (centang) ====
    // done == ada di $confirmedKeys (berarti step tsb sudah sempat klik Next).
    // fallback: jika $confirmedKeys kosong, anggap semua step sebelum current done.
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
            <svg class="w-10 h-10 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
    @else
        <a href="{{ route('pelamar.curriculum_vitae.index') }}" class="text-blue-700 hover:underline text-sm flex items-center" aria-label="Kembali ke daftar CV">
            <svg class="w-10 h-10 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
    @endif
</div>

<!-- ===== STEPPER (centang hanya setelah klik Next) ===== -->
<div class="absolute top-10 left-0 right-0 z-30 flex justify-center">
    <div class="flex items-center space-x-4 overflow-x-auto">
        @php $visualNum = 1; @endphp
        @foreach($flow as $k)
            @php
                $allowedStep = in_array($k, $allowed, true);
                $isCurrent   = $currentKey === $k;

                // Done kalau ada di confirmedKeys; jika tidak ada confirmedKeys, fallback: semua sebelum current dianggap done
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
                            <img src="{{ asset('assets/images/done.svg') }}" alt="Selesai" class="w-6 h-6"/>
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
<div class="flex flex-col items-center justify-center z-10 mt-32 mb-20">
    <form id="linkForm"
          method="POST"
          action="{{ route('pelamar.curriculum_vitae.social_media.addSocialMedia', $curriculumVitaeUser->id) }}"
          class="bg-white shadow-lg rounded-lg p-8 mx-auto z-10 mb-20"
          style="max-width:800px;width:100%;">
        @csrf

        <h2 class="text-2xl text-center text-blue-800 mb-2">Link Informasi</h2>
        <p class="text-center text-gray-500 mb-8">Tambahkan tautan profesional (LinkedIn, GitHub, Portofolio, dsb.).</p>

        <ul id="link-list" class="space-y-4">
            @forelse($curriculumVitaeUser->links as $link)
                <li class="rounded flex items-center justify-between p-4 border border-gray-300 shadow">
                    <div class="flex items-center space-x-4 w-full">
                        <div class="cursor-move text-gray-400" title="Seret untuk mengurutkan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                        </div>
                        <div class="grid grid-cols-2 gap-4 w-full">
                            <div class="col-span-1">
                                <input type="text" name="link_name[]" value="{{ $link->link_name }}"
                                       class="link-input block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                                       style="height:45px;padding:0 10px;" placeholder="Masukkan Nama Link (mis. LinkedIn)" required/>
                                @error('link_name') <div class="text-sm font-thin text-red-500">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-span-1">
                                <input type="url" name="url[]" value="{{ $link->url }}"
                                       class="desc-input block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                                       style="height:45px;padding:0 10px;" placeholder="Masukkan URL (https://...)" required/>
                            </div>
                        </div>
                    </div>

                    <!-- Hapus (server-side untuk item existing) -->
                    <form action="{{ route('pelamar.curriculum_vitae.social_media.deleteSocialMedia', [$curriculumVitaeUser->id, $link->id]) }}"
                          method="POST" onsubmit="return confirm('Yakin ingin menghapus link ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 transition ml-4" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012-1.87L19 7M10 11v6m4-6v6"/></svg>
                        </button>
                    </form>
                </li>
            @empty
                <li class="rounded flex items-center justify-between p-4 border border-dashed border-gray-300 text-gray-500">
                    Belum ada link. Tambahkan dengan tombol di bawah.
                </li>
            @endforelse
        </ul>

        <!-- Tambah field link baru -->
        <button type="button" id="add-link-btn"
                class="mt-6 w-full py-4 bg-blue-100 text-blue-700 text-sm font-bold rounded shadow hover:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition text-center block">
            + Tambah Link Informasi Lain
        </button>

        <!-- Step terakhir: hanya tombol Simpan -->
        <button type="submit"
                class="mt-6 w-full py-4 bg-blue-700 text-white text-sm font-medium rounded shadow hover:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
            Simpan Link
        </button>
    </form>
</div>

<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const linkList = document.getElementById('link-list');

    if (linkList) {
        Sortable.create(linkList, { animation: 150, handle: '.cursor-move', ghostClass: 'bg-blue-50' });
    }

    document.getElementById('add-link-btn').addEventListener('click', function () {
        const linkInputs = document.querySelectorAll('.link-input');
        const urlInputs  = document.querySelectorAll('.desc-input');

        for (let i = 0; i < linkInputs.length; i++) {
            if (linkInputs[i].value.trim() === '' || urlInputs[i].value.trim() === '') {
                alert('Silakan isi semua field yang ada sebelum menambahkan form baru!');
                return;
            }
        }

        const row = `
            <li class="rounded flex items-center justify-between p-4 border border-gray-300 shadow">
                <div class="flex items-center space-x-4 w-full">
                    <div class="cursor-move text-gray-400" title="Seret untuk mengurutkan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                        </svg>
                    </div>
                    <div class="grid grid-cols-2 gap-4 w-full">
                        <div class="col-span-1">
                            <input type="text" name="link_name[]" class="link-input block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height:45px;padding:0 10px;" placeholder="Masukkan Nama Link (mis. LinkedIn)" required/>
                        </div>
                        <div class="col-span-1">
                            <input type="url" name="url[]" class="desc-input block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height:45px;padding:0 10px;" placeholder="Masukkan URL (https://...)" required/>
                        </div>
                    </div>
                </div>
                <button type="button" class="text-red-500 hover:text-red-700 transition ml-4 delete-btn" title="Hapus">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012-1.87L19 7M10 11v6m4-6v6"/>
                    </svg>
                </button>
            </li>`;
        linkList.insertAdjacentHTML('beforeend', row);
    });

    linkList.addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-btn');
        if (btn) {
            const li = btn.closest('li');
            if (li) li.remove();
        }
    });
});
</script>
</body>
</html>
