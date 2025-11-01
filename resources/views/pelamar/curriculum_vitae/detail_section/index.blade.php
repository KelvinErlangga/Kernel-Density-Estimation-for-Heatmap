<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail CV | CVRE GENERATE</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/icons/logo.svg') }}" type="image/x-icon">
</head>

<body class="bg-gradient-to-b from-white via-purple-50 to-blue-50" style="font-family: 'Poppins', sans-serif">

    <!-- Form Container -->
    <h2 class="text-2xl font-bold text-center text-blue-800 mb-8">Detail CV</h2>
        <div class="flex flex-col items-center justify-center z-10 mt-32 mb-20">
            <div class="bg-white shadow-lg rounded-lg p-8 mx-auto" style="max-width: 800px; width: 100%;">

                <!-- Form Title -->
                <h2 class="text-2xl text-center text-blue-800 mb-8">Pengalaman Kerja</h2>

                <!-- List Pengalaman Kerja (Dragable) -->
                <ul id="experience-list" class="space-y-4">
                    @foreach($curriculumVitaeUser->experiences as $experience)
                    <!-- Pengalaman Kerja 1 -->
                    <a class="p-4" href="{{route('pelamar.curriculum_vitae.experience.deleteExperience', [$curriculumVitaeUser->id, $experience->id])}}">
                        <li class="border border-gray- rounded flex items-center justify-between p-4 shadow">
                            <div class="flex items-center space-x-4">
                                <!-- Icon Drag (Dua Garis) -->
                                <div class="cursor-move text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                    </svg>
                                </div>

                                <!-- Detail Pengalaman -->
                                <div>
                                    <h3 class="text-blue-700 font-semibold">{{$experience->position_experience}} | {{$experience->company_experience}}</h3>

                                    @if($experience->end_date)
                                    <p class="text-gray-500 text-sm">{{$experience->city_experience}} | {{date('M Y', strtotime($experience->start_date))}} - {{date('M Y', strtotime($experience->end_date))}}</p>
                                    @else
                                    <p class="text-gray-500 text-sm">{{$experience->city_experience}} | {{date('M Y', strtotime($experience->start_date))}} - Sekarang</p>
                                    @endif

                                </div>
                            </div>

                            <!-- Tombol Hapus -->
                            <form action="{{route('pelamar.curriculum_vitae.experience.deleteExperience', [$curriculumVitaeUser->id, $experience->id])}}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                                    </svg>
                                </button>
                            </form>

                        </li>
                    </a>

                    @endforeach
                </ul>

                <!-- Tombol Tambah Pengalaman Kerja -->
                <a href="{{route('pelamar.curriculum_vitae.experience.create', $curriculumVitaeUser->id)}}" class="mt-6 w-full py-4 bg-blue-100 text-blue-700 text-sm font-bold rounded shadow hover:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition text-center block">+ Tambah Pengalaman Kerja</a>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-8 mx-auto z-10 mb-20" style="max-width: 800px; width: 100%;">
            <h2 class="text-2xl text-center text-blue-800 mb-8">Pendidikan</h2>

            <!-- List Pendidikan (Draggable) -->
            <ul id="experience-list" class="space-y-4">
                @foreach($curriculumVitaeUser->educations as $education)
                <!-- Pendidikan 1 -->
                <a class="p-4" href="{{route('pelamar.curriculum_vitae.education.editEducation', [$curriculumVitaeUser->id, $education->id])}}">
                    <li class="border border-gray-300 rounded flex items-center justify-between p-4 shadow">
                        <div class="flex items-center space-x-4">
                            <!-- Icon Drag -->
                            <div class="cursor-move text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                </svg>
                            </div>

                            <!-- Detail Pengalaman -->
                            <div>
                                <h3 class="text-blue-700 font-semibold">{{$education->school_name}} | {{$education->city_education}}</h3>
                                @if($education->end_date)
                                <p class="text-gray-500 text-sm">{{$education->field_of_study}} | {{date('M Y', strtotime($education->start_date))}} - {{date('M Y', strtotime($education->end_date))}}</p>
                                @else
                                <p class="text-gray-500 text-sm">{{$education->field_of_study}} | {{date('M Y', strtotime($education->start_date))}} - Sekarang</p>
                                @endif
                            </div>
                        </div>

                        <!-- Tombol Hapus -->
                        <form action="{{route('pelamar.curriculum_vitae.education.deleteEducation', [$curriculumVitaeUser->id, $education->id])}}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                                </svg>
                            </button>
                        </form>
                    </li>
                </a>
                @endforeach
            </ul>

            <!-- Tombol Tambah Pendidikan -->
            <a href="{{route('pelamar.curriculum_vitae.education.createEducation', $curriculumVitaeUser->id)}}" class="mt-6 w-full py-4 bg-blue-100 text-blue-700 text-sm font-bold rounded shadow hover:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition text-center block">+ Tambah Pendidikan</a>
        </div>

        <form method="POST" action="{{route('pelamar.curriculum_vitae.language.addLanguage', $curriculumVitaeUser->id)}}" class="bg-white shadow-lg rounded-lg p-8 mx-auto z-10 mb-20" style="max-width: 800px; width: 100%;">
            @csrf
            <h2 class="text-2xl text-center text-blue-800 mb-8">Bahasa</h2>

            <!-- Swiper Container (Drag and Drop List) -->
            <ul id="language-list" class="space-y-4">
                <!-- Language 1 -->
                @forelse($curriculumVitaeUser->languages as $language)
                <li class="rounded flex items-center justify-between">
                    <div class="flex items-center space-x-4 w-full">
                        <!-- Drag Icon (Two Lines) -->
                        <div class="cursor-move text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                            </svg>
                        </div>

                        <!-- Language and Level Fields -->
                        <div class="grid grid-cols-2 gap-4 w-full">
                            <div class="col-span-1">
                                <input type="text" name="language_name[]" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-3" placeholder="Bahasa" value="{{$language->language_name}}" required />
                                @error('language_name')
                                <div class="text-sm font-thin text-red-500">Bahasa harus diisi</div>
                                @enderror
                            </div>
                            <div class="col-span-1">
                                <select name="level[]" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height: 45px; padding: 0 10px;" required>
                                    <option value="{{$language->category_level}}">{{$language->category_level}}</option>
                                    <option value="Beginer">Beginer</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Expert">Expert</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Hapus -->
                    <button class="text-red-500 hover:text-red-700 transition ml-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                        </svg>
                    </button>
                </li>
                @empty
                <li class="rounded flex items-center justify-between">
                    <div class="flex items-center space-x-4 w-full">
                        <!-- Drag Icon (Two Lines) -->
                        <div class="cursor-move text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                            </svg>
                        </div>

                        <!-- Language and Level Fields -->
                        <div class="grid grid-cols-2 gap-4 w-full">
                            <div class="col-span-1">
                                <input type="text" name="language_name[]" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-3" placeholder="Bahasa" required />
                                @error('language_name')
                                <div class="text-sm font-thin text-red-500">Bahasa harus diisi</div>
                                @enderror
                            </div>
                            <div class="col-span-1">
                                <select name="level[]" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height: 45px; padding: 0 10px;" required>
                                    <option value="" disabled selected>Pilih Level</option>
                                    <option value="Beginer">Beginer</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Expert">Expert</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Hapus -->
                    <button class="text-red-500 hover:text-red-700 transition ml-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                        </svg>
                    </button>
                </li>
                @endforelse
            </ul>

            <!-- Add Another Language Button -->
            <button type="button" id="add-language-btn" class="mt-6 w-full py-4 bg-blue-100 text-blue-700 text-sm font-bold rounded shadow hover:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition text-center block">
                + Tambah Bahasa Lain
            </button>
        </form>

        <form action="{{route('pelamar.curriculum_vitae.skill.addSkill', $curriculumVitaeUser->id)}}" method="POST" class="bg-white shadow-lg rounded-lg p-8 mx-auto z-10 mb-20 grid grid-cols-1 md:grid-cols-2 gap-8" style="max-width: 1000px; width: 100%;">
            @csrf
            <h2 class="text-2xl text-center text-blue-800 md:col-span-2 mb-8">Keahlian</h2>

            <!-- Left Section: Keahlian dan Level -->
            <div class="space-y-6">
                <!-- Additional Skills -->
                <ul id="language-list" class="space-y-4">
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
                                    <input type="text" name="skill_name[]" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-3" placeholder="Keahlian" value="{{$skill->skill_name}}" required />
                                    @error('skill_name')
                                    <div class="text-sm font-thin text-red-500">Keahlian harus diisi</div>
                                    @enderror
                                </div>
                                <div class="col-span-1">
                                    <select name="level[]" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height: 50px; padding: 0 10px;" required>
                                        <option value="{{$skill->category_level}}">{{$skill->category_level}}</option>
                                        <option value="Beginer">Beginer</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Expert">Expert</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="text-red-500 hover:text-red-700 transition ml-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                            </svg>
                        </button>
                    </li>
                    @empty
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
                                    <input type="text" name="skill_name[]" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-3" placeholder="Keahlian" required />
                                    @error('skill_name')
                                    <div class="text-sm font-thin text-red-500">Keahlian harus diisi</div>
                                    @enderror
                                </div>
                                <div class="col-span-1">
                                    <select name="level[]" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height: 50px; padding: 0 10px;" required>
                                        <option value="" disabled selected>Pilih Level</option>
                                        <option value="Beginer">Beginer</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Expert">Expert</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="text-red-500 hover:text-red-700 transition ml-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                            </svg>
                        </button>
                    </li>
                    @endforelse
                </ul>
                <button type="button" id="add-language-btn" class="mt-6 w-full py-4 bg-blue-100 text-blue-700 text-sm font-bold rounded shadow hover:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition text-center block">
                    + Tambah Keahlian Lain
                </button>
            </div>

            <!-- Right Section: Pencarian Bidang Pekerjaan -->
            <div class="space-y-6 border-2 border-gray-300 rounded-lg p-4">
                <!-- Input Search -->
                <input
                    type="text"
                    name="search_job"
                    id="search-job"
                    placeholder="Cari Berdasarkan Bidang Pekerjaan"
                    class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-3"
                    oninput="filterJobs()" />

                <!-- Job Search Results (Initially Hidden) -->
                <ul id="job-list" class="space-y-2" style="display: none;">
                    <!-- Example Job Items (dynamic content will be injected) -->
                </ul>
            </div>
        </form>

        <div class="bg-white shadow-lg rounded-lg p-8 mx-auto z-10 mb-20" style="max-width: 800px; width: 100%;">

            <!-- Form Title -->
            <h2 class="text-2xl text-center text-blue-800 mb-8">Pengalaman Organisasi(opsional)</h2>

            <!-- List Pengalaman Organisasi (Dragable) -->
            <ul id="experience-list" class="space-y-4">
                <!-- Pengalaman Organisasi 1 -->
                @foreach($curriculumVitaeUser->organizations as $organization)
                <a class="p-4" href="{{route('pelamar.curriculum_vitae.organization.EditOrganization', [$curriculumVitaeUser->id, $organization->id])}}">
                    <li class="border border-gray- rounded flex items-center justify-between p-4 shadow">
                        <div class="flex items-center space-x-4">
                            <!-- Icon Drag (Dua Garis) -->
                            <div class="cursor-move text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                </svg>
                            </div>

                            <!-- Detail Pengalaman -->
                            <div>
                                <h3 class="text-blue-700 font-semibold">{{$organization->position_organization}} | {{$organization->city_organization}}</h3>
                                @if($organization->end_date)
                                <p class="text-gray-500 text-sm">{{$organization->organization_name}} | {{date('M Y', strtotime($organization->start_date))}} - {{date('M Y', strtotime($organization->end_date))}}</p>
                                @else
                                <p class="text-gray-500 text-sm">{{$organization->organization_name}} | {{date('M Y', strtotime($organization->start_date))}} - Sekarang</p>
                                @endif
                            </div>
                        </div>

                        <!-- Tombol Hapus -->
                        <form action="{{route('pelamar.curriculum_vitae.organization.deleteOrganization', [$curriculumVitaeUser->id, $organization->id])}}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                                </svg>
                            </button>
                        </form>
                    </li>
                </a>
                @endforeach
            </ul>

            <!-- Tombol Tambah Pengalaman Organisasi -->
            <a href="{{route('pelamar.curriculum_vitae.organization.createOrganization', $curriculumVitaeUser->id)}}" class="mt-6 w-full py-4 bg-blue-100 text-blue-700 text-sm font-bold rounded shadow hover:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition text-center block">+ Tambah Pengalaman Organisasi
            </a>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-8 mx-auto z-10 mb-20" style="max-width: 800px; width: 100%;">
            <h2 class="text-2xl text-center text-blue-800 mb-8">Prestasi(opsional)</h2>

            <!-- List Pendidikan (Draggable) -->
            <ul id="experience-list" class="space-y-4">
                @foreach($curriculumVitaeUser->achievements as $achievement)
                <!-- Pendidikan 1 -->
                <a class="p-4" href="{{route('pelamar.curriculum_vitae.achievement.editAchievement', [$curriculumVitaeUser->id, $achievement->id])}}">
                    <li class="border border-gray-300 rounded flex items-center justify-between p-4 shadow">
                        <div class="flex items-center space-x-4">
                            <!-- Icon Drag -->
                            <div class="cursor-move text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                </svg>
                            </div>

                            <!-- Detail Pengalaman -->
                            <div>
                                <h3 class="text-blue-700 font-semibold">{{$achievement->achievement_name}} | {{$achievement->city_achievement}}</h3>
                                <p class="text-gray-500 text-sm">{{$achievement->organizer_achievement}} | {{date('d M Y', strtotime($achievement->date_achievement))}}
                            </div>
                        </div>

                        <!-- Tombol Hapus -->
                        <form action="{{route('pelamar.curriculum_vitae.achievement.deleteAchievement', [$curriculumVitaeUser->id, $achievement->id])}}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                                </svg>
                            </button>
                        </form>
                    </li>
                </a>
                @endforeach
            </ul>

            <!-- Tombol Tambah Pendidikan -->
            <a href="{{route('pelamar.curriculum_vitae.achievement.createAchievement', $curriculumVitaeUser->id)}}" class="mt-6 w-full py-4 bg-blue-100 text-blue-700 text-sm font-bold rounded shadow hover:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition text-center block">+ Tambah Prestasi</a>
        </div>

        <form id="linkForm" method="POST" action="{{route('pelamar.curriculum_vitae.social_media.addSocialMedia', $curriculumVitaeUser->id)}}" class="bg-white shadow-lg rounded-lg p-8 mx-auto z-10 mb-20" style="max-width: 800px; width: 100%;">
            @csrf
            <h2 class="text-2xl text-center text-blue-800 mb-8">Link Informasi</h2>

            <!-- Swiper Container (Drag and Drop List) -->
            <ul id="link-list" class="space-y-4">
                <!-- Link Informasi 1 -->
                @forelse($curriculumVitaeUser->links as $link)
                <li class="rounded flex items-center justify-between">
                    <div class="flex items-center space-x-4 w-full">
                        <!-- Drag Icon (Two Lines) -->
                        <div class="cursor-move text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                            </svg>
                        </div>

                        <!-- Link Informasi dan Field Level -->
                        <div class="grid grid-cols-2 gap-4 w-full">
                            <div class="col-span-1">
                                <input type="text" name="link_name[]" value="{{$link->link_name}}" class="link-input block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height: 45px; padding: 0 10px;" placeholder="Masukkan Nama Link" required />
                                @error('link_name')
                                <div class="text-sm font-thin text-red-500">Link harus diisi</div>
                                @enderror
                            </div>
                            <div class="col-span-1">
                                <input type="text" name="url[]" value="{{$link->url}}" class="desc-input block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height: 45px; padding: 0 10px;" placeholder="Masukkan Link Informasi" required />
                            </div>
                        </div>
                    </div>

                    <!-- Delete Button -->
                    <button class="text-red-500 hover:text-red-700 transition ml-4 delete-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                        </svg>
                    </button>
                </li>
                @empty
                <li class="rounded flex items-center justify-between">
                    <div class="flex items-center space-x-4 w-full">
                        <!-- Drag Icon (Two Lines) -->
                        <div class="cursor-move text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                            </svg>
                        </div>

                        <!-- Link Informasi dan Field Level -->
                        <div class="grid grid-cols-2 gap-4 w-full">
                            <div class="col-span-1">
                                <input type="text" name="link_name[]" class="link-input block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height: 45px; padding: 0 10px;" placeholder="Masukkan Nama Link" required />
                                @error('link_name')
                                <div class="text-sm font-thin text-red-500">Link harus diisi</div>
                                @enderror
                            </div>
                            <div class="col-span-1">
                                <input type="text" name="url[]" class="desc-input block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height: 45px; padding: 0 10px;" placeholder="Masukkan Link Informasi" required />
                            </div>
                        </div>
                    </div>

                    <!-- Delete Button -->
                    <button class="text-red-500 hover:text-red-700 transition ml-4 delete-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                        </svg>
                    </button>
                </li>
                @endforelse
            </ul>

            <!-- Add Another Link Button -->
            <button type="button" id="add-link-btn" class="mt-6 w-full py-4 bg-blue-100 text-blue-700 text-sm font-bold rounded shadow hover:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition text-center block">
                + Tambah Link Informasi Lain
            </button>

            <!-- Submit Button -->
            <button type="submit" class="mt-6 w-full py-4 bg-blue-700 text-white text-sm font-medium rounded shadow hover:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
                Langkah Selanjutnya
            </button>
        </form>

        <script src="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const experienceList = document.getElementById('experience-list');
                Sortable.create(experienceList, {
                    animation: 150,
                    handle: '.cursor-move', // Hanya bagian ikon drag yang
                    ghostClass: 'bg-blue-200', // Gaya saat item sedang di-drag
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
            const experienceList = document.getElementById('experience-list');
            Sortable.create(experienceList, {
                animation: 150, // Animasi saat drag
                handle: '.cursor-move', // Hanya bagian ikon drag yang bisa digunakan untuk drag
                ghostClass: 'bg-blue-200', // Gaya saat item sedang di-drag
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const languageList = document.getElementById('language-list');
            Sortable.create(languageList, {
                animation: 150,
                handle: '.cursor-move',
                ghostClass: 'bg-blue-200',
            });

            document.getElementById('add-language-btn').addEventListener('click', function() {
                let allFilled = true;
                const languageSelects = document.querySelectorAll('select[name="language_name[]"]');
                const levelSelects = document.querySelectorAll('select[name="level[]"]');

                languageSelects.forEach((select, index) => {
                    if (!select.value || !levelSelects[index].value) {
                        allFilled = false;
                    }
                });

                if (allFilled) {
                    const languageForm = `
                        <li class="rounded flex items-center justify-between">
                            <div class="flex items-center space-x-4 w-full">
                                <div class="cursor-move text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                    </svg>
                                </div>
                                <div class="grid grid-cols-2 gap-4 w-full">
                                    <div class="col-span-1">
                                        <input type="text" name="language_name[]" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-3" placeholder="Bahasa" required />
                                    </div>
                                    <div class="col-span-1">
                                        <select name="level[]" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height: 45px; padding: 0 10px;" required>
                                            <option value="" disabled selected>Pilih Level</option>
                                            <option value="Beginer">Beginer</option>
                                            <option value="Medium">Medium</option>
                                            <option value="Expert">Expert</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button class="text-red-500 hover:text-red-700 transition ml-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7H5m0 0l1.5 13A2 2 0 008.5 22h7a2 2 0 002-1.87L19 7M5 7l1.5-4A2 2 0 018.5 2h7a2 2 0 012 1.87L19 7M10 11v6m4-6v6" />
                    </svg>
                </button>
                        </li>
                    `;

                    languageList.insertAdjacentHTML('beforeend', languageForm);
                } else {
                    alert('Silakan isi kolom yang masih kosong sebelum menambahkan bahasa baru.');
                }
            });

            languageList.addEventListener('click', function(e) {
                if (e.target.closest('button')) {
                    e.target.closest('li').remove();
                }
            });
        });
        </script>
</body>
</html>
