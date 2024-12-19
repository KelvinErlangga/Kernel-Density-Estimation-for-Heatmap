<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestasi | CVRE GENERATE</title>
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <link rel="icon" href="{{asset('assets/icons/logo.svg')}}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex flex-col relative bg-gradient-to-b from-white via-purple-50 to-blue-50" style="font-family: 'Poppins', sans-serif">
    <!-- Background -->
    <img src="{{asset('assets/images/background.png')}}" alt="Background Shape" class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none" />

    <!-- Back Button -->
    <div class="absolute top-10 left-10 z-50">
        <a href="{{route('pelamar.curriculum_vitae.achievement.index', $curriculumVitaeUser->id)}}" class="text-blue-700 hover:underline text-sm flex items-center">
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
            <h2 class="text-2xl text-center text-blue-800 mb-8">Prestasi</h2>

            <!-- Form -->
            <form method="POST" action="{{route('pelamar.curriculum_vitae.achievement.updateAchievement', [$curriculumVitaeUser->id, $achievement->id])}}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-4 gap-4">
                    <!-- Prestasi -->
                    <div class="col-span-2">
                        <input id="achievement_name" type="text" name="achievement_name" placeholder="Prestasi" value="{{$achievement->achievement_name}}" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height: 45px; padding: 0 16px" required />
                    </div>
                    <!-- Kota -->
                    <div class="col-span-2">
                        <input id="city_achievement" type="text" name="city_achievement" placeholder="Kota" value="{{$achievement->city_achievement}}" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height: 45px; padding: 0 16px" required />
                    </div>
                    <!-- Penyelenggara -->
                    <div class="col-span-2 mt-6">
                        <input id="organizer_achievement" type="text" name="organizer_achievement" value="{{$achievement->organizer_achievement}}" placeholder="Penyelenggara" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height: 45px; padding: 0 16px" required />
                    </div>
                    <!-- Tanggal -->
                    <div class="col-span-2">
                        <label for="date_achievement" class="block text-sm text-gray-500">Tanggal</label>
                        <input id="date_achievement" type="date" name="date_achievement" value="{{$achievement->date_achievement}}" class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height: 45px; padding: 0 16px" />
                    </div>
                    <!-- Ringkasan -->
                    <div class="col-span-4 mt-3">
                        <!-- Quill Editor -->
                        <div id="editor" class="bg-white rounded border border-gray-300" style="height: 150px;"></div>
                        <input type="hidden" id="description_achievement" value="{{$achievement->description_achievement}}" name="description_achievement">
                    </div>
                </div>

                <!-- Langkah Selanjutnya -->
                <button type="submit" class="mt-6 w-full py-4 bg-blue-700 text-white text-sm font-medium rounded shadow hover:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
                    Submit
                </button>
            </form>
        </div>
    </div>

    <!-- Quill.js CSS & JS -->
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Quill
            var quill = new Quill('#editor', {
                theme: 'snow',
                placeholder: 'Deskripsi Prestasi',
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

            // Set Font Poppins for Quill Editor
            quill.root.style.fontFamily = 'Poppins, sans-serif';

            // Simpan data Quill ke input hidden
            var summaryInput = document.querySelector('input#description_achievement');
            if (summaryInput.value) {
                quill.clipboard.dangerouslyPasteHTML(summaryInput.value); // Mengatur konten awal editor
            }

            quill.on('text-change', function() {
                summaryInput.value = quill.root.innerHTML;
            });

        });
    </script>
</body>

</html>