<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Pendidikan | CVRE GENERATE</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('assets/icons/logo.svg') }}" type="image/x-icon" />

    <!-- Quill -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
</head>
<body class="min-h-screen flex flex-col relative bg-gradient-to-b from-white via-purple-50 to-blue-50" style="font-family:'Poppins',sans-serif">

@php
    // ==== FLOW & ROUTES (konsisten) ====
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
    $allowed       = $allowedKeys   ?? $flow;
    $confirmedKeys = $confirmedKeys ?? [];

    $currentKey = 'educations';
    $idx = array_search($currentKey, $flow, true);

    // prev/next allowed
    $backKey = null; $nextKey = null;
    for ($i = $idx - 1; $i >= 0; $i--) if (in_array($flow[$i], $allowed, true)) { $backKey = $flow[$i]; break; }
    for ($i = $idx + 1; $i < count($flow); $i++) if (in_array($flow[$i], $allowed, true)) { $nextKey = $flow[$i]; break; }

    // centang hanya jika di confirmedKeys, current tdk dicentang
    $useConfirmed = !empty($confirmedKeys);

    // helper format month (YYYY-MM)
    $fmtMonth = function ($dateStr) {
        if (!$dateStr) return '';
        // jika sudah YYYY-MM biarkan, kalau YYYY-MM-DD ambil 7 char
        return strlen($dateStr) >= 7 ? substr($dateStr, 0, 7) : $dateStr;
    };
@endphp

<!-- Background -->
<img src="{{ asset('assets/images/background.png') }}" alt="Background Shape" class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none" />

<!-- Back (kembali ke index pendidikan) -->
<div class="absolute top-10 left-10 z-50">
    <a href="{{ route('pelamar.curriculum_vitae.education.index', $curriculumVitaeUser->id) }}"
       class="text-blue-700 hover:underline text-sm flex items-center" aria-label="Kembali ke Pendidikan">
        <svg class="w-10 h-10 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
        </svg>
    </a>
</div>

<!-- Stepper -->
<div class="absolute top-10 left-0 right-0 z-30 flex justify-center">
    <div class="flex items-center space-x-4 overflow-x-auto">
        @php $visualNum = 1; @endphp
        @foreach($flow as $k)
            @php
                $allowedStep = in_array($k, $allowed, true);
                $isCurrent   = $currentKey === $k;
                $done        = $useConfirmed && in_array($k, $confirmedKeys, true);
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

<!-- Form Container -->
<div class="flex flex-col items-center justify-center z-10 mt-32 mb-20 w-full px-4">
    <div class="bg-white shadow-lg rounded-lg p-8 mx-auto z-10" style="max-width: 900px; width: 100%;">
        <h2 class="text-2xl text-center text-blue-800 mb-8">Edit Riwayat Pendidikan</h2>

        <form method="POST" action="{{ route('pelamar.curriculum_vitae.education.updateEducation', [$curriculumVitaeUser->id, $education->id]) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @method('PUT')

            <!-- Jenjang -->
            <div>
                <label class="block text-sm text-gray-700 mb-1">Jenjang *</label>
                <select name="education_level" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none h-11 px-3" required>
                    @php $lvl = old('education_level', $education->education_level); @endphp
                    <option value="" disabled {{ $lvl ? '' : 'selected' }}>Pilih Jenjang</option>
                    <option value="SMA/MA/SMK"   {{ $lvl==='SMA/MA/SMK' ? 'selected':'' }}>SMA / MA / SMK</option>
                    <option value="D1"           {{ $lvl==='D1' ? 'selected':'' }}>Diploma 1 (D1)</option>
                    <option value="D2"           {{ $lvl==='D2' ? 'selected':'' }}>Diploma 2 (D2)</option>
                    <option value="D3"           {{ $lvl==='D3' ? 'selected':'' }}>Diploma 3 (D3)</option>
                    <option value="D4"           {{ $lvl==='D4' ? 'selected':'' }}>Diploma 4 (D4)</option>
                    <option value="S1"           {{ $lvl==='S1' ? 'selected':'' }}>Sarjana (S1)</option>
                    <option value="S2"           {{ $lvl==='S2' ? 'selected':'' }}>Magister (S2)</option>
                    <option value="S3"           {{ $lvl==='S3' ? 'selected':'' }}>Doktor (S3)</option>
                </select>
                @error('education_level') <div class="text-sm text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Nama Sekolah/Kampus -->
            <div>
                <label class="block text-sm text-gray-700 mb-1">Nama Sekolah / Kampus *</label>
                <input type="text" name="school_name"
                       value="{{ old('school_name', $education->school_name) }}"
                       placeholder="Nama institusi pendidikan"
                       class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none h-11 px-3" required>
                @error('school_name') <div class="text-sm text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Kota -->
            <div>
                <label class="block text-sm text-gray-700 mb-1">Kota *</label>
                <input type="text" name="city_education"
                       value="{{ old('city_education', $education->city_education) }}"
                       placeholder="Kota pendidikan"
                       class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none h-11 px-3" required>
                @error('city_education') <div class="text-sm text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Jurusan/Prodi -->
            <div>
                <label class="block text-sm text-gray-700 mb-1">Jurusan / Program Studi</label>
                <input type="text" name="field_of_study"
                       value="{{ old('field_of_study', $education->field_of_study) }}"
                       placeholder="Contoh: IPA, TKJ, Informatika, Manajemen"
                       class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none h-11 px-3">
                @error('field_of_study') <div class="text-sm text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Mulai -->
            <div>
                <label class="block text-sm text-gray-700 mb-1">Mulai *</label>
                <input type="month" name="start_date"
                       value="{{ old('start_date', $fmtMonth($education->start_date)) }}"
                       class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none h-11 px-3" required>
                @error('start_date') <div class="text-sm text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Selesai -->
            <div>
                <label class="block text-sm text-gray-700 mb-1">Selesai</label>
                <input type="month" id="end_date" name="end_date"
                       value="{{ old('end_date', $fmtMonth($education->end_date)) }}"
                       class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none h-11 px-3">
                @error('end_date') <div class="text-sm text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Masih Berjalan -->
            <div class="md:col-span-2">
                @php $isCurrentVal = old('is_current', (int)($education->is_current ?? 0)); @endphp
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" id="is_current" name="is_current" value="1"
                           class="rounded border-gray-300 focus:ring-blue-500"
                           {{ $isCurrentVal ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700">Masih berstatus pelajar/mahasiswa (sedang berjalan)</span>
                </label>
            </div>

            <!-- IPK/Nilai -->
            <div>
                <label class="block text-sm text-gray-700 mb-1">IPK / Nilai Akhir <span class="text-gray-400">(opsional)</span></label>
                <input type="text" name="gpa"
                       value="{{ old('gpa', $education->gpa) }}"
                       placeholder="Contoh: 3.75 atau 90/100"
                       class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none h-11 px-3">
                @error('gpa') <div class="text-sm text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Deskripsi (Quill) -->
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-700 mb-1">Deskripsi / Kurikulum <span class="text-gray-400">(opsional)</span></label>
                <div id="editor" class="bg-white rounded border border-gray-300" style="height:150px;"></div>
                <input type="hidden" id="description" name="description" value="{{ old('description', $education->description) }}">
                @error('description') <div class="text-sm text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Submit -->
            <div class="md:col-span-2">
                <div class="flex gap-3">
                    <a href="{{ route('pelamar.curriculum_vitae.education.index', $curriculumVitaeUser->id) }}"
                       class="w-1/3 py-3 text-center rounded border border-gray-300 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="w-2/3 py-3 bg-blue-700 text-white text-sm font-bold rounded shadow hover:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
                        Perbarui Pendidikan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Quill -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Quill init
    var quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Ringkas perkuliahan/mata pelajaran inti, organisasi kampus, prestasi terkait, dsb.',
        modules: { toolbar: [['bold','italic','underline'], [{ list:'ordered' }, { list:'bullet' }]] }
    });
    quill.root.style.fontFamily = 'Poppins, sans-serif';

    // preload (HTML)
    var hiddenDesc = document.getElementById('description');
    if (hiddenDesc.value) quill.root.innerHTML = hiddenDesc.value;

    // sync to hidden (HTML)
    quill.on('text-change', function () {
        hiddenDesc.value = quill.root.innerHTML;
    });

    // toggle end_date by is_current
    const isCurrent = document.getElementById('is_current');
    const endDate   = document.getElementById('end_date');

    function toggleEndDate() {
        if (!isCurrent || !endDate) return;
        if (isCurrent.checked) {
            endDate.value = '';
            endDate.setAttribute('disabled', 'disabled');
            endDate.classList.add('bg-gray-100');
        } else {
            endDate.removeAttribute('disabled');
            endDate.classList.remove('bg-gray-100');
        }
    }
    if (isCurrent && endDate) {
        isCurrent.addEventListener('change', toggleEndDate);
        toggleEndDate(); // initial state
    }
});
</script>
</body>
</html>
