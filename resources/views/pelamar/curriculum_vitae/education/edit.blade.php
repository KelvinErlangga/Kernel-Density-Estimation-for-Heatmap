<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendidikan | CVRE GENERATE</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="icon" href="{{asset('assets/icons/logo.svg')}}" type="image/x-icon">

    <!-- Quill.js CSS -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
</head>

<body class="min-h-screen flex flex-col relative bg-gradient-to-b from-white via-purple-50 to-blue-50" style="font-family: 'Poppins', sans-serif">

    <!-- Background -->
    <img src="{{asset('assets/images/background.png')}}" alt="Background Shape" class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none">

    <!-- Back Button -->
    <div class="absolute top-10 left-10 z-50">
        <a href="{{route('pelamar.curriculum_vitae.education.index', $curriculumVitaeUser->id)}}" class="text-blue-700 hover:underline text-sm flex items-center">
            <svg class="w-10 h-10 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
    </div>

    <!-- Stepper -->
    <div class="absolute top-10 left-0 right-0 z-30 flex justify-center">
        <div class="flex items-center space-x-4 overflow-x-auto">
            <!-- Step 1 -->
            <div class="flex items-center space-x-4">
            </div>
            <!-- Steps 2 to 7 -->
            <div class="flex items-center space-x-4">
            </div>
        </div>
    </div>


    <!-- Form Container -->
    <div class="flex flex-col items-center justify-center z-10 mt-32 mb-20">
        <div class="bg-white shadow-lg rounded-lg p-8 mx-auto z-10 mb-20" style="max-width: 800px; width: 100%;">

            <!-- Form Title -->
            <h2 class="text-2xl text-center text-blue-800 mb-8">Pendidikan</h2>

            <!-- Form -->
            <form method="POST" action="{{route('pelamar.curriculum_vitae.education.updateEducation', [$curriculumVitaeUser->id, $education->id])}}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-4 gap-4">
                    <!-- Bidang -->
                    <div class="col-span-2">
                        <input
                            id="field_of_study"
                            type="text"
                            name="field_of_study"
                            placeholder="Bidang"
                            value="{{$education->field_of_study}}"
                            class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px"
                            required />
                    </div>
                    <!-- Kota -->
                    <div class="col-span-2">
                        <input
                            id="city_education"
                            type="text"
                            name="city_education"
                            placeholder="Kota"
                            value="{{$education->city_education}}"
                            class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px"
                            required />
                    </div>
                    <!-- Sekolah -->
                    <div class="col-span-4">
                        <input
                            id="school_name"
                            type="text"
                            name="school_name"
                            value="{{$education->school_name}}"
                            placeholder="Sekolah"
                            class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px"
                            required />
                    </div>
                    <!-- Tanggal Mulai -->
                    <div class="col-span-2">
                        <label for="start_date" class="block text-sm text-gray-500">Tanggal Mulai</label>
                        <input
                            id="start_date"
                            type="date"
                            name="start_date"
                            value="{{$education->start_date}}"
                            placeholder="Tanggal Mulai"
                            class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px"
                            required />
                    </div>
                    <!-- Tanggal Selesai -->
                    <div class="col-span-2">
                        <label for="end_date" class="block text-sm text-gray-500">Tanggal Selesai</label>
                        <input
                            id="end_date"
                            type="date"
                            name="end_date"
                            value="{{$education->end_date}}"
                            placeholder="Tanggal Selesai"
                            class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px" />
                    </div>
                    <!-- Masih sekolah -->
                    <div class="flex items-center col-span-4">
                        <input
                            id="masih_sekolah"
                            type="checkbox"
                            name="masih_sekolah"
                            class="mr-2"
                            onclick="toggleEndDate()" />
                        <label for="masih_sekolah" class="text-sm text-gray-400">Masih sekolah di sini</label>
                    </div>
                    <!-- Tambahkan Deskripsi -->
                    <div class="flex items-center col-span-4">
                        <input
                            id="toggle_description"
                            type="checkbox"
                            name="toggle_description"
                            class="mr-2"
                            onclick="toggleDescription()" />
                        <label for="toggle_description" class="text-sm text-gray-400">Tambahkan deskripsi</label>
                    </div>

                    <!-- Deskripsi -->
                    <div class="col-span-4" id="description_form" style="display: none;">
                        <!-- Quill Editor -->
                        <div id="editor" class="bg-white rounded border border-gray-300" style="height: 150px;"></div>
                        <input id="description_education" name="description_education" type="hidden" value="{{$education->description_education}}">
                    </div>

                </div>

                <!-- Langkah Selanjutnya -->
                <button
                    type="submit"
                    class="mt-6 w-full py-4 bg-blue-700 text-white text-sm font-medium rounded shadow hover:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
                    Submit
                </button>
            </form>
        </div>
    </div>

    <!-- Quill.js JS -->
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Quill
            var quill = new Quill('#editor', {
                theme: 'snow',
                placeholder: 'Deskripsi',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'], // Bold, Italic, Underline
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }] // Ordered & Unordered List
                    ]
                }
            });

            // Set Font Poppins untuk Quill Editor
            quill.root.style.fontFamily = 'Poppins, sans-serif';

            // Simpan data Quill ke input hidden
            var summaryInput = document.querySelector('input#description_education');
            if (summaryInput.value) {
                quill.clipboard.dangerouslyPasteHTML(summaryInput.value); // Mengatur konten awal editor
            }

            quill.on('text-change', function() {
                summaryInput.value = quill.root.innerHTML;
            });
        });

        function toggleDescription() {
            const descriptionForm = document.getElementById('description_form');
            const checkbox = document.getElementById('toggle_description');

            if (checkbox.checked) {
                descriptionForm.style.display = 'block';
            } else {
                descriptionForm.style.display = 'none';
            }
        }

        function toggleEndDate() {
            const masihBekerja = document.getElementById('masih_sekolah');
            const endDate = document.getElementById('end_date');
            const endDateContainer = endDate.closest('div');

            if (masihBekerja.checked) {
                endDateContainer.style.display = 'none';
                endDate.value = '';
            } else {
                endDateContainer.style.display = 'block';
            }
        }
    </script>
</body>

</html>