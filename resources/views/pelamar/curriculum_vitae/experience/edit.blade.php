<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengalaman Kerja | CVRE GENERATE</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Quill.js CSS -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <link rel="icon" href="{{asset('assets/icons/logo.svg')}}" type="image/x-icon">

</head>

<body class="min-h-screen flex flex-col relative bg-gradient-to-b from-white via-purple-50 to-blue-50" style="font-family: 'Poppins', sans-serif">

    <!-- Background -->
    <img src="{{asset('assets/images/background.png')}}" alt="Background Shape" class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none" />

    <!-- Back Button -->
    <div class="absolute top-10 left-10 z-50">
        <a href="{{route('pelamar.curriculum_vitae.experience.index', $curriculumVitaeUser->id)}}" class="text-blue-700 hover:underline text-sm flex items-center">
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
            <h2 class="text-2xl text-center text-blue-800 mb-8">Pengalaman Kerja</h2>

            <!-- Form -->
            <form method="POST" action="{{route('pelamar.curriculum_vitae.experience.updateExperience', [$curriculumVitaeUser->id, $experience->id])}}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-4 gap-4">
                    <!-- Posisi Kerja -->
                    <div class="col-span-2">
                        <input
                            id="position_experience"
                            type="text"
                            name="position_experience"
                            placeholder="Posisi Kerja"
                            value="{{$experience->position_experience}}"
                            class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px"
                            required />
                    </div>
                    <!-- Kota -->
                    <div class="col-span-2">
                        <input
                            id="city_experience"
                            type="text"
                            name="city_experience"
                            placeholder="Kota"
                            value="{{$experience->city_experience}}"
                            class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px"
                            required />
                    </div>
                    <!-- Pemberi Kerja/Perusahaan -->
                    <div class="col-span-4">
                        <input
                            id="company_experience"
                            type="text"
                            name="company_experience"
                            value="{{$experience->company_experience}}"
                            placeholder="Pemberi Kerja/Perusahaan"
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
                            value="{{$experience->start_date}}"
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
                            value="{{$experience->end_date}}"
                            class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none"
                            style="height: 45px; padding: 0 16px" />
                    </div>
                    <!-- Masih Bekerja -->
                    <div class="flex items-center col-span-4">
                        <input
                            id="masih_bekerja"
                            type="checkbox"
                            name="masih_bekerja"
                            class="mr-2"
                            onclick="toggleEndDate()" />
                        <label for="masih_bekerja" class="text-sm text-gray-400">Masih bekerja di sini</label>
                    </div>
                    <!-- Ringkasan -->
                    <div class="col-span-4">
                        <!-- Quill Editor -->
                        <div id="editor" class="bg-white rounded border border-gray-300" style="height: 150px;"></div>
                        <input type="hidden" value="{{$experience->description_experience}}" id="description_experience" name="description_experience">
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
                            list: 'ordered'
                        }, {
                            list: 'bullet'
                        }] // Ordered & Unordered List
                    ]
                }
            });

            // Set Font Poppins for Quill Editor
            quill.root.style.fontFamily = 'Poppins, sans-serif';

            // Simpan data Quill ke input hidden
            var summaryInput = document.querySelector('input#description_experience');
            if (summaryInput.value) {
                quill.clipboard.dangerouslyPasteHTML(summaryInput.value); // Mengatur konten awal editor
            }

            quill.on('text-change', function() {
                summaryInput.value = quill.root.innerHTML;
            });
        });

        function toggleEndDate() {
            const masihBekerja = document.getElementById('masih_bekerja');
            const endDate = document.getElementById('end_date');
            const endDateContainer = endDate.closest('div'); // Menemukan elemen induk dari input tanggal selesai

            if (masihBekerja.checked) {
                endDateContainer.style.display = 'none'; // Sembunyikan input tanggal selesai
                endDate.value = '';
            } else {
                endDateContainer.style.display = 'block'; // Tampilkan kembali input tanggal selesai
            }
        }
    </script>
</body>

</html>