<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Detail Pribadi | CVRE GENERATE</title>

    <!-- Quill.js CSS -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"/>
    <link rel="icon" href="{{ asset('assets/icons/logo.svg') }}" type="image/x-icon"/>
</head>
<body class="min-h-screen flex flex-col relative bg-gradient-to-b from-white via-purple-50 to-blue-50" style="font-family: 'Poppins', sans-serif">

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

    $allowed       = $allowedKeys   ?? $flow;
    $confirmedKeys = $confirmedKeys ?? [];

    $currentKey = 'personal_detail';
    $idx = array_search($currentKey, $flow, true);

    // prev allowed
    $backKey = null; for ($i = $idx - 1; $i >= 0; $i--) { if (in_array($flow[$i], $allowed, true)) { $backKey = $flow[$i]; break; } }
    // next allowed
    $nextKey = null; for ($i = $idx + 1; $i < count($flow); $i++) { if (in_array($flow[$i], $allowed, true)) { $nextKey = $flow[$i]; break; } }

    // Centang hanya untuk step yang sudah dikonfirmasi
    $useConfirmed = !empty($confirmedKeys);
    $fallbackDoneSet = [];
    if (!$useConfirmed && $idx !== false) { for ($i=0;$i<$idx;$i++) $fallbackDoneSet[$flow[$i]] = true; }
@endphp

<!-- Background -->
<img src="{{ asset('assets/images/background.png') }}" alt="" class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none"/>

<!-- Back Button -->
<div class="absolute top-10 left-10 z-50">
    @if($backKey)
        <a href="{{ route($routeOf[$backKey], $curriculumVitaeUser->id) }}"
           class="text-blue-700 hover:underline text-sm flex items-center" aria-label="Kembali">
            <svg class="w-10 h-10 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
    @else
        <a href="{{ route('pelamar.curriculum_vitae.index') }}"
           class="text-blue-700 hover:underline text-sm flex items-center" aria-label="Kembali ke daftar CV">
            <svg class="w-10 h-10 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
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

                $done = $useConfirmed ? in_array($k, $confirmedKeys, true)
                                      : isset($fallbackDoneSet[$k]);
                // Pada halaman current, tetap angka
                if ($isCurrent) { $done = false; }

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
    <div class="bg-white shadow-lg rounded-2xl p-8 mx-auto" style="max-width: 800px; width: 100%;">
        <h2 class="text-2xl text-center text-blue-800 mb-8">Detail Pribadi</h2>

        <form method="POST"
              action="{{ route('pelamar.curriculum_vitae.profile.addProfile', $curriculumVitaeUser) }}"
              enctype="multipart/form-data">
            @csrf

            @php $personalDetail = $curriculumVitaeUser->personalDetail; @endphp

            @if($personalDetail)
                <div class="grid grid-cols-4 gap-4">
                    <!-- Foto -->
                    <div class="col-span-1 flex flex-col items-center">
                        <label for="avatar_curriculum_vitae" class="cursor-pointer flex flex-col items-center border border-gray-300 rounded-lg p-4 hover:border-blue-500 transition">
                            @if($personalDetail->avatar_curriculum_vitae)
                                <img src="{{ Storage::url($personalDetail->avatar_curriculum_vitae) }}" alt="Foto" class="mb-2 w-14">
                                <span class="text-sm font-medium text-blue-700">Ubah Foto</span>
                                <input type="file" id="avatar_curriculum_vitae" name="avatar_curriculum_vitae" class="hidden">
                            @else
                                <img src="{{ asset('assets/images/photo-placeholder.png') }}" alt="Tambah Foto" class="mb-2 w-14">
                                <span class="text-sm font-medium text-blue-700">Tambahkan Foto</span>
                                <input type="file" id="avatar_curriculum_vitae" name="avatar_curriculum_vitae" class="hidden">
                            @endif
                        </label>
                    </div>

                    <!-- Nama Depan -->
                    <div class="col-span-3">
                        <input id="first_name_curriculum_vitae" type="text" name="first_name_curriculum_vitae"
                               placeholder="Nama Depan" value="{{ $personalDetail->first_name_curriculum_vitae }}"
                               class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                               style="height: 45px; padding: 0 16px" required>
                    </div>

                    <!-- Nama Belakang -->
                    <div class="col-span-3 col-start-2 -mt-16">
                        <input id="last_name_curriculum_vitae" type="text" name="last_name_curriculum_vitae"
                               value="{{ $personalDetail->last_name_curriculum_vitae }}" placeholder="Nama Belakang"
                               class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                               style="height: 45px; padding: 0 16px">
                    </div>

                    <!-- Kota -->
                    <div class="col-span-2">
                        <input id="city_curriculum_vitae" type="text" name="city_curriculum_vitae"
                               value="{{ $personalDetail->city_curriculum_vitae }}" placeholder="Kota"
                               class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                               style="height: 45px; padding: 0 16px" required>
                        @error('city_curriculum_vitae')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div class="col-span-2">
                        <input id="address_curriculum_vitae" type="text" name="address_curriculum_vitae"
                               placeholder="Alamat" value="{{ $personalDetail->address_curriculum_vitae }}"
                               class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                               style="height: 45px; padding: 0 16px">
                        @error('address_curriculum_vitae')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-span-2">
                        <input id="email_curriculum_vitae" type="email" name="email_curriculum_vitae"
                               value="{{ $personalDetail->email_curriculum_vitae }}" placeholder="Alamat Email"
                               class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                               style="height: 45px; padding: 0 16px" required>
                        @error('email_curriculum_vitae')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="col-span-2">
                        <input id="phone_curriculum_vitae" type="number" name="phone_curriculum_vitae"
                               placeholder="Nomor Telepon" value="{{ $personalDetail->phone_curriculum_vitae }}"
                               class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                               style="height: 45px; padding: 0 16px" required>
                        @error('phone_curriculum_vitae')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Ringkasan -->
                    <div class="col-span-4">
                        <div id="editor" class="bg-white rounded border border-gray-300" style="height: 150px;"></div>
                        <input type="hidden" id="personal_summary" name="personal_summary"
                               value="{{ $personalDetail->personal_summary }}">
                        @error('personal_summary')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @else
                <div class="grid grid-cols-4 gap-4">
                    <!-- Foto -->
                    <div class="col-span-1 flex flex-col items-center">
                        <label for="avatar_curriculum_vitae" class="cursor-pointer flex flex-col items-center border border-gray-300 rounded-lg p-4 hover:border-blue-500 transition">
                            <img src="{{ asset('assets/images/photo-placeholder.png') }}" alt="Tambah Foto" class="mb-2 w-14">
                            <span class="text-sm font-medium text-blue-700">Tambahkan Foto</span>
                            <input type="file" id="avatar_curriculum_vitae" name="avatar_curriculum_vitae" class="hidden">
                        </label>
                    </div>

                    <!-- Nama Depan -->
                    <div class="col-span-3">
                        <input id="first_name_curriculum_vitae" type="text" name="first_name_curriculum_vitae"
                               placeholder="Nama Depan" value="{{ old('first_name_curriculum_vitae') }}"
                               class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                               style="height: 45px; padding: 0 16px" required>
                    </div>

                    <!-- Nama Belakang -->
                    <div class="col-span-3 col-start-2 -mt-16">
                        <input id="last_name_curriculum_vitae" type="text" name="last_name_curriculum_vitae"
                               value="{{ old('last_name_curriculum_vitae') }}" placeholder="Nama Belakang"
                               class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                               style="height: 45px; padding: 0 16px">
                    </div>

                    <!-- Kota -->
                    <div class="col-span-2">
                        <input id="city_curriculum_vitae" type="text" name="city_curriculum_vitae"
                               value="{{ old('city_curriculum_vitae') }}" placeholder="Kota"
                               class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                               style="height: 45px; padding: 0 16px" required>
                        @error('city_curriculum_vitae')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div class="col-span-2">
                        <input id="address_curriculum_vitae" type="text" name="address_curriculum_vitae"
                               placeholder="Alamat" value="{{ old('address_curriculum_vitae') }}"
                               class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                               style="height: 45px; padding: 0 16px">
                        @error('address_curriculum_vitae')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-span-2">
                        <input id="email_curriculum_vitae" type="email" name="email_curriculum_vitae"
                               value="{{ old('email_curriculum_vitae') }}" placeholder="Alamat Email"
                               class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                               style="height: 45px; padding: 0 16px" required>
                        @error('email_curriculum_vitae')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="col-span-2">
                        <input id="phone_curriculum_vitae" type="number" name="phone_curriculum_vitae"
                               placeholder="Nomor Telepon" value="{{ old('phone_curriculum_vitae') }}"
                               class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                               style="height: 45px; padding: 0 16px" required>
                        @error('phone_curriculum_vitae')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Ringkasan -->
                    <div class="col-span-4">
                        <div id="editor" class="bg-white rounded border border-gray-300" style="height: 150px;"></div>
                        <input type="hidden" id="personal_summary" name="personal_summary">
                        @error('personal_summary')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @endif

            <!-- Tombol (match style Index Bahasa) -->
            <div class="mt-6 space-y-3">
                <button type="submit"
                        class="w-full py-3 md:py-4 bg-blue-700 text-white font-semibold rounded-xl shadow hover:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
                    Langkah Selanjutnya
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Quill.js -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Ringkasan Mengenai Anda',
        modules: { toolbar: [['bold','italic','underline'], [{ list: 'ordered' }, { list: 'bullet' }]] }
    });
    quill.root.style.fontFamily = 'Poppins, sans-serif';

    var summaryInput = document.querySelector('input#personal_summary');
    if (summaryInput && summaryInput.value) {
        quill.clipboard.dangerouslyPasteHTML(summaryInput.value);
    }
    quill.on('text-change', function () {
        summaryInput.value = quill.getText().trim(); // ganti ke quill.root.innerHTML kalau mau HTML
    });
});
</script>
</body>
</html>
