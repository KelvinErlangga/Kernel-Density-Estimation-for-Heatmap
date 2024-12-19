<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keahlian | CVRE GENERATE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="icon" href="{{asset('assets/icons/logo.svg')}}" type="image/x-icon">

</head>

<body class="min-h-screen flex flex-col relative bg-gradient-to-b from-white via-purple-50 to-blue-50" style="font-family: 'Poppins', sans-serif">

    <!-- Background -->
    <img src="{{asset('assets/images/background.png')}}" alt=" Background Shape" class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none" />

    <!-- Back Button -->
    <div class="absolute top-10 left-10 z-50">
        <a href="{{route('pelamar.curriculum_vitae.language.index', $curriculumVitaeUser->id)}}" class="text-blue-700 hover:underline text-sm flex items-center">
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
                <div class="flex justify-center items-center w-11 h-11 rounded-full bg-blue-700 text-white font-bold">
                    <img src="{{asset('assets/images/done.svg')}}" alt="Check Icon" class="w-6 h-6" />
                </div>
                <div class="w-14 h-px bg-blue-700"></div>

                <div class="flex justify-center items-center w-11 h-11 rounded-full bg-blue-700 text-white font-bold">
                    <img src="{{asset('assets/images/done.svg')}}" alt="Check Icon" class="w-6 h-6" />
                </div>
                <div class="w-14 h-px bg-blue-700"></div>

                <div class="flex justify-center items-center w-11 h-11 rounded-full bg-blue-700 text-white font-bold">
                    <img src="{{asset('assets/images/done.svg')}}" alt="Check Icon" class="w-6 h-6" />
                </div>
                <div class="w-14 h-px bg-blue-700"></div>

                <div class="flex justify-center items-center w-11 h-11 rounded-full bg-blue-700 text-white font-bold">
                    <img src="{{asset('assets/images/done.svg')}}" alt="Check Icon" class="w-6 h-6" />
                </div>
                <div class="w-14 h-px bg-blue-700"></div>

                <div class="flex justify-center items-center w-11 h-11 rounded-full bg-blue-700 text-white text-3xl">
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

            <!-- Submit Button -->
            <div class="md:col-span-2">
                <button type="submit" class="mt-6 w-full py-4 bg-blue-700 text-white text-sm font-medium rounded shadow hover:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
                    Langkah Selanjutnya
                </button>
            </div>
        </form>
    </div>
</body>

<!-- SortableJS Library -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Job skills data for filtering
        const jobList = document.getElementById('job-list');
        const searchInput = document.getElementById('search-job');
        let jobSkills = {}; // To store data from the server

        // Fetch data from server
        fetch('/api/job-skills')
            .then(response => response.json())
            .then(data => {
                jobSkills = data;
            })
            .catch(error => console.error('Error fetching job skills:', error));

        // Listen to search input changes
        searchInput.addEventListener('input', filterJobs);

        function filterJobs() {
            const searchInputValue = searchInput.value.toLowerCase();
            jobList.innerHTML = ''; // Clear previous results

            if (searchInputValue.length >= 3) {
                Object.keys(jobSkills).forEach(job => {
                    if (job.toLowerCase().includes(searchInputValue)) {
                        jobList.style.display = 'block'; // Show job list

                        jobSkills[job].forEach(skill => {
                            const skillItem = document.createElement('li');
                            skillItem.classList.add('text-gray-700', 'flex', 'justify-between', 'items-center', 'bg-gray-50', 'p-2', 'rounded', 'shadow-sm', 'job-item');

                            const skillName = document.createElement('span');
                            skillName.textContent = skill;

                            const selectButton = document.createElement('button');
                            selectButton.type = 'button';
                            selectButton.classList.add('text-blue-500', 'hover:text-blue-700');
                            selectButton.textContent = 'Pilih';
                            selectButton.addEventListener('click', () => addSkillToForm(skill)); // Add skill to form

                            skillItem.appendChild(skillName);
                            skillItem.appendChild(selectButton);
                            jobList.appendChild(skillItem);
                        });
                    }
                });
            } else {
                jobList.style.display = 'none'; // Hide job list if less than 3 characters
            }
        }

        function addSkillToForm(skill) {
            const languageList = document.getElementById('language-list');

            // Cek apakah skill sudah ada di daftar
            const existingSkills = Array.from(languageList.querySelectorAll('input[name="skill_name[]"]')).map(input => input.value);
            if (existingSkills.includes(skill)) {
                alert('Skill sudah dipilih!');
                return; // Jangan tambahkan jika skill sudah ada
            }

            const newSkillItem = document.createElement('li');
            newSkillItem.classList.add('rounded', 'flex', 'items-center', 'justify-between');
            newSkillItem.innerHTML =
                `<div class="flex items-center space-x-4 w-full">
                <div class="cursor-move text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                    </svg>
                </div>
                <div class="grid grid-cols-2 gap-4 w-full">
                    <div class="col-span-1">
                        <input type="text" name="skill_name[]" value="${skill}" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-3" />
                    </div>
                    <div class="col-span-1">
                        <select name="level[]" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none" style="height: 50px; padding: 0 10px;">
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
            </button>`;

            languageList.appendChild(newSkillItem);

            // Hide job recommendations after selection
            // jobList.style.display = 'none';
            // jobList.innerHTML = ''; // Clear job list for next search
        }

        // Initialize SortableJS for Language List when DOM is loaded
        const languageList = document.getElementById('language-list');
        Sortable.create(languageList, {
            animation: 150, // Animation duration
            handle: '.cursor-move', // Only allow dragging using the drag icon
            ghostClass: 'bg-blue-200', // Style while dragging
        });

        // Add new language input when the button is clicked
        document.getElementById('add-language-btn').addEventListener('click', function() {
            // Validate if all current inputs are filled
            const inputs = document.querySelectorAll('input[name="skill_name[]"]');
            const selects = document.querySelectorAll('select[name="level[]"]');
            let allFilled = true;

            inputs.forEach((input, index) => {
                if (!input.value || !selects[index].value) {
                    allFilled = false;
                }
            });

            // Prevent adding new fields if any input is empty
            if (!allFilled) {
                alert("Pastikan semua form keahlian dan level telah diisi sebelum menambah keterampilan baru.");
                return;
            }

            // Add new skill form
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
                                <input type="text" name="skill_name[]" class="block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-3" placeholder="Keahlian" required />
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
                </li>`;

            document.getElementById('language-list').insertAdjacentHTML('beforeend', languageForm);
        });

        document.getElementById('language-list').addEventListener('click', function(e) {
            if (e.target.closest('button')) {
                e.target.closest('li').remove();
            }
        });
    });
</script>


</html>