<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pribadi | CVRE GENERATE</title>
    <!-- Quill.js CSS -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Import font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{asset('assets/icons/logo.svg')}}" type="image/x-icon">

</head>

<body class="min-h-screen flex flex-col relative bg-gradient-to-b from-white via-purple-50 to-blue-50" style="font-family: 'Poppins', sans-serif">
    <!-- Background -->
    <img src="{{asset('assets/images/background.png')}}" alt="Background Shape" class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none">

    <!-- Back Button -->
    <div class="absolute top-10 left-10 z-50">
        <a href="{{route('pelamar.curriculum_vitae.index')}}" class="text-blue-700 hover:underline text-sm flex items-center">
            <svg class="w-10 h-10 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
    </div>

    <!-- Stepper Container -->
    <div class="absolute top-10 left-0 right-0 z-30 flex justify-center">
        <div class="flex items-center space-x-4 overflow-x-auto">
            <!-- Step 1 -->
            <div class="flex items-center space-x-4">
                <div class="flex justify-center items-center w-11 h-11 rounded-full bg-blue-700 text-white font-bold">
                    <img src="{{asset('assets/images/done.svg')}}" alt="Check Icon" class="w-6 h-6" />
                </div>
                <div class="w-14 h-px bg-blue-700"></div>
            </div>
            <!-- Steps 2 to 7 -->
            <div class="flex items-center space-x-4">
                <div class="flex justify-center items-center w-11 h-11 rounded-full bg-blue-700 text-white text-3xl">
                    2
                </div>
                <div class="w-14 h-px bg-gray-300"></div>

                <div class="flex justify-center items-center w-11 h-11 rounded-full bg-gray-300 text-gray-700 text-3xl">
                    3
                </div>
                <div class="w-14 h-px bg-gray-300"></div>

                <div class="flex justify-center items-center w-11 h-11 rounded-full bg-gray-300 text-gray-700 text-3xl">
                    4
                </div>
                <div class="w-14 h-px bg-gray-300"></div>

                <div class="flex justify-center items-center w-11 h-11 rounded-full bg-gray-300 text-gray-700 text-3xl">
                    5
                </div>
                <div class="w-14 h-px bg-gray-300"></div>

                <div class="flex justify-center items-center w-11 h-11 rounded-full bg-gray-300 text-gray-700 text-3xl">
                    6
                </div>
                <div class="w-14 h-px bg-gray-300"></div>

                <div class="flex justify-center items-center w-11 h-11 rounded-full bg-gray-300 text-gray-700 text-3xl">
                    7
                </div>
            </div>
        </div>
    </div>


    <!-- Form Container -->
    <div class="flex flex-col items-center justify-center z-10 mt-32 mb-20">
        <div class="bg-white shadow-lg rounded-lg p-8 mx-auto" style="max-width: 800px; width: 100%;">
            <!-- Form Title -->
            <h2 class="text-2xl text-center text-blue-800 mb-8">Detail Pribadi</h2>

            <!-- Form -->
            <form method="POST" action="{{route('pelamar.curriculum_vitae.profile.addProfile', $curriculumVitaeUser)}}" enctype="multipart/form-data">
                @csrf

                @php
                $personalCurriculumVitae = $curriculumVitaeUser->personalCurriculumVitae;
                @endphp
                <!-- Replace with Laravel's @csrf -->
                @if($personalCurriculumVitae)
                <div class="grid grid-cols-4 gap-4">
                    <!-- Foto -->
                    <div class="col-span-1 flex flex-col items-center">
                        <label for="avatar_curriculum_vitae" class="cursor-pointer flex flex-col items-center border border-gray-300 rounded-lg p-4 hover:border-blue-500 transition">
                            @if($personalCurriculumVitae->avatar_curriculum_vitae)
                            <img src="{{Storage::url($personalCurriculumVitae->avatar_curriculum_vitae)}}" alt="Tambah Foto" class="mb-2 w-14">
                            <span class="text-sm font-medium text-blue-700">Ubah Foto</span>
                            <input type="file" value="{{$personalCurriculumVitae->avatar_curriculum_vitae}}" id="avatar_curriculum_vitae" name="avatar_curriculum_vitae" class="hidden">
                            @else
                            <img src="{{asset('assets/images/photo-placeholder.png')}}" alt="Tambah Foto" class="mb-2 w-14">
                            <span class="text-sm font-medium text-blue-700">Tambahkan Foto</span>
                            <input type="file" id="avatar_curriculum_vitae" name="avatar_curriculum_vitae" class="hidden">
                            @endif

                        </label>
                    </div>
                    <!-- Nama Depan -->
                    <div class="col-span-3">
                        <input
                            id="first_name_curriculum_vitae"
                            type="text"
                            name="first_name_curriculum_vitae"
                            placeholder="Nama Depan"
                            value="{{$personalCurriculumVitae->first_name_curriculum_vitae}}"
                            class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px"
                            required>
                    </div>
                    <!-- Nama Belakang -->
                    <div class="col-span-3 col-start-2 -mt-16">
                        <input
                            id="last_name_curriculum_vitae"
                            type="text"
                            name="last_name_curriculum_vitae"
                            value="{{$personalCurriculumVitae->last_name_curriculum_vitae}}"
                            placeholder="Nama Belakang"
                            class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px">
                    </div>
                    <!-- Kota -->
                    <div class="col-span-2">
                        <input
                            id="city_curriculum_vitae"
                            type="text"
                            name="city_curriculum_vitae"
                            value="{{$personalCurriculumVitae->city_curriculum_vitae}}"
                            placeholder="Kota"
                            class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px"
                            required>
                        @error('city_curriculum_vitae')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Alamat -->
                    <div class="col-span-2">
                        <input
                            id="address_curriculum_vitae"
                            type="text"
                            name="address_curriculum_vitae"
                            placeholder="Alamat"
                            value="{{$personalCurriculumVitae->address_curriculum_vitae}}"
                            class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px">
                        @error('address_curriculum_vitae')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Email -->
                    <div class="col-span-2">
                        <input
                            id="email_curriculum_vitae"
                            type="email"
                            name="email_curriculum_vitae"
                            value="{{$personalCurriculumVitae->email_curriculum_vitae}}"
                            placeholder="Alamat Email"
                            class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px"
                            required>
                        @error('email_curriculum_vitae')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Nomor Telepon -->
                    <div class="col-span-2">
                        <input
                            id="phone_curriculum_vitae"
                            type="number"
                            name="phone_curriculum_vitae"
                            placeholder="Nomor Telepon"
                            value="{{$personalCurriculumVitae->phone_curriculum_vitae}}"
                            class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px"
                            required>
                        @error('phone_curriculum_vitae')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Ringkasan -->
                    <div class="col-span-4">
                        <div id="editor" class="bg-white rounded border border-gray-300" style="height: 150px;"></div>
                        <input type="hidden" id="personal_summary" name="personal_summary" value="{{$personalCurriculumVitae->personal_summary}}">
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
                            <img src="{{asset('assets/images/photo-placeholder.png')}}" alt="Tambah Foto" class="mb-2 w-14">
                            <span class="text-sm font-medium text-blue-700">Tambahkan Foto</span>
                            <input type="file" id="avatar_curriculum_vitae" name="avatar_curriculum_vitae" class="hidden">
                        </label>
                    </div>
                    <!-- Nama Depan -->
                    <div class="col-span-3">
                        <input
                            id="first_name_curriculum_vitae"
                            type="text"
                            name="first_name_curriculum_vitae"
                            placeholder="Nama Depan"
                            value="{{old('first_name_curriculum_vitae')}}"
                            class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px"
                            required>
                    </div>
                    <!-- Nama Belakang -->
                    <div class="col-span-3 col-start-2 -mt-16">
                        <input
                            id="last_name_curriculum_vitae"
                            type="text"
                            name="last_name_curriculum_vitae"
                            value="{{old('last_name_curriculum_vitae')}}"
                            placeholder="Nama Belakang"
                            class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px">
                    </div>
                    <!-- Kota -->
                    <div class="col-span-2">
                        <input
                            id="city_curriculum_vitae"
                            type="text"
                            name="city_curriculum_vitae"
                            value="{{old('city_curriculum_vitae')}}"
                            placeholder="Kota"
                            class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px"
                            required>
                        @error('city_curriculum_vitae')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Alamat -->
                    <div class="col-span-2">
                        <input
                            id="address_curriculum_vitae"
                            type="text"
                            name="address_curriculum_vitae"
                            placeholder="Alamat"
                            value="{{old('address_curriculum_vitae')}}"
                            class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px">
                        @error('address_curriculum_vitae')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Email -->
                    <div class="col-span-2">
                        <input
                            id="email_curriculum_vitae"
                            type="email"
                            name="email_curriculum_vitae"
                            value="{{old('email_curriculum_vitae')}}"
                            placeholder="Alamat Email"
                            class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px"
                            required>
                        @error('email_curriculum_vitae')
                        <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Nomor Telepon -->
                    <div class="col-span-2">
                        <input
                            id="phone_curriculum_vitae"
                            type="number"
                            name="phone_curriculum_vitae"
                            placeholder="Nomor Telepon"
                            value="{{old('phone_curriculum_vitae')}}"
                            class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px"
                            required>
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

                <!-- Langkah Selanjutnya -->
                <button
                    type="submit"
                    class="mt-6 w-full py-4 bg-blue-700 text-white text-sm font-medium rounded shadow hover:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
                    Langkah Selanjutnya
                </button>
            </form>
        </div>
    </div>

    <!-- Quill.js JS -->
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var quill = new Quill('#editor', {
                theme: 'snow',
                placeholder: 'Ringkasan Mengenai Anda',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{
                            list: 'ordered'
                        }, {
                            list: 'bullet'
                        }]
                    ]
                }
            });

            quill.root.style.fontFamily = 'Poppins, sans-serif';

            var summaryInput = document.querySelector('input#personal_summary');
            if (summaryInput.value) {
                quill.clipboard.dangerouslyPasteHTML(summaryInput.value); // Mengatur konten awal editor
            }

            quill.on('text-change', function() {
                summaryInput.value = quill.getText().trim();
            });
        });
    </script>
</body>

</html>