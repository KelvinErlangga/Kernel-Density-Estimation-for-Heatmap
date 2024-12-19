<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title')</title>
    <!-- Tambahkan link font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />

    <link rel="icon" href="{{asset('assets/icons/logo.svg')}}" type="image/x-icon">

    @stack('style')

</head>

<body class="bg-gray-100" style="font-family: 'Poppins', sans-serif">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{route('welcome')}}" class="flex items-center">
                        <img src="{{asset('assets/homepage/logo.png')}}" alt="Logo" class="h-8 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex space-x-8">
                    <a href="{{route('welcome')}}" class="text-blue-700 font-medium hover:text-blue-500">Beranda</a>

                    <!-- Dropdown Curriculum Vitae -->
                    <div class="relative">
                        <button id="cv-dropdown-btn" class="text-gray-700 font-medium hover:text-blue-500 flex items-center focus:outline-none">
                            Curriculum Vitae
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="cv-dropdown-menu" class="absolute hidden bg-white shadow-lg rounded mt-2 py-2 z-50 w-full text-center">
                            <a href="{{route('pelamar.curriculum_vitae.index')}}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Generate CV</a>
                        </div>
                    </div>

                    <!-- Dropdown Cover Letter -->
                    <div class="relative">
                        <button id="cl-dropdown-btn" class="text-gray-700 font-medium hover:text-blue-500 flex items-center focus:outline-none">
                            Cover Letter
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="cl-dropdown-menu" class="absolute hidden bg-white shadow-lg rounded mt-2 py-2 z-50 w-full text-center">
                            <a href="{{route('pelamar.cover_letter.index')}}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Generate Cover Letter</a>
                        </div>
                    </div>

                    <!-- Dropdown Tentang -->
                    <div class="relative">
                        <button id="about-dropdown-btn" class="text-gray-700 font-medium hover:text-blue-500 flex items-center focus:outline-none">
                            Tentang
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="about-dropdown-menu" class="absolute hidden bg-white shadow-lg rounded mt-2 py-2 z-50 w-full text-center">
                            <a href="{{route('tentang-kami')}}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Kami</a>
                            <a href="{{route('kontak')}}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Kontak</a>
                            <a href="{{route('tanya-jawab')}}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">QNA</a>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-4">
                    @auth

                    @role('admin')
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="route('logout')" class="border border-blue-700 text-blue-700 font-medium text-sm px-4 py-2 rounded hover:bg-blue-50" onclick="event.preventDefault();
                                                this.closest('form').submit();">Logout</a>
                    </form>
                    @endrole

                    @role('perusahaan')
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="route('logout')" class="border border-blue-700 text-blue-700 font-medium text-sm px-4 py-2 rounded hover:bg-blue-50" onclick="event.preventDefault();
                                                this.closest('form').submit();">Logout</a>
                    </form>
                    @endrole

                    @role('pelamar')
                    <a href="{{route('pelamar.dashboard.index')}}" class="border border-blue-700 text-blue-700 font-medium text-sm px-4 py-2 rounded hover:bg-blue-50">Dashboard Akun</a>
                    @endrole

                    @else
                    <a href="{{route('login')}}" class="border border-blue-700 text-blue-700 font-medium text-sm px-4 py-2 rounded hover:bg-blue-50">Login</a>
                    @endauth

                    <a href="{{route('pelamar.curriculum_vitae.index')}}" class="bg-blue-700 text-white font-medium text-sm px-4 py-2 rounded hover:bg-blue-600">Buat CV</a>
                </div>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Footer -->
    <footer class="bg-white py-12">
        <div class="container mx-auto flex justify-between">
            <!-- Bagian Kiri -->
            <div class="flex space-x-16">
                <!-- Kolom Generate -->
                <div>
                    <h3 class="text-xl font-bold text-blue-600 mb-5">GENERATE</h3>
                    <ul class="space-y-3">
                        <li><a href="{{route('pelamar.curriculum_vitae.index')}}" class="text-gray-800 hover:text-blue-600">Curriculum Vitae</a></li>
                        <li><a href="{{route('pelamar.cover_letter.index')}}" class="text-gray-800 hover:text-blue-600">Cover Letter</a></li>
                    </ul>
                </div>

                <!-- Kolom Tentang -->
                <div>
                    <h3 class="text-xl font-bold text-blue-600 mb-5">TENTANG</h3>
                    <ul class="space-y-3">
                        <li><a href="{{route('tentang-kami')}}" class="text-gray-800 hover:text-blue-600">Tentang Kami</a></li>
                        <li><a href="{{route('kontak')}}" class="text-gray-800 hover:text-blue-600">Kontak Kami</a></li>
                    </ul>
                </div>

                <!-- Kolom Social Media -->
                <div>
                    <h3 class="text-xl font-bold text-blue-600 mb-5">SOCIAL MEDIA</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center space-x-2">
                            <img src="{{asset('assets/icons/tiktok.svg')}}" alt="TikTok" class="w-5 h-5" />
                            <span class="text-gray-800 hover:text-blue-600">@CVRE_generate</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <img src="{{asset('assets/icons/instagram.svg')}}" alt="Instagram" class="w-5 h-5" />
                            <span class="text-gray-800 hover:text-blue-600">@CVRE_generate</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <img src="{{asset('assets/icons/facebook.svg')}}" alt="Facebook" class="w-5 h-5" />
                            <span class="text-gray-800 hover:text-blue-600">CVRE Generate</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bagian Kanan (Logo Footer) -->
            <div class="flex justify-end items-center flex-grow">
                <img src="{{asset('assets/homepage/logo-footer.png')}}" alt="Logo" style="width: 240px; height: auto" />
            </div>
        </div>
    </footer>

    <script>
        // Toggle visibility of dropdowns
        function toggleDropdown(buttonId, menuId) {
            const button = document.getElementById(buttonId);
            const menu = document.getElementById(menuId);

            button.addEventListener("click", () => {
                const isHidden = menu.classList.contains("hidden");
                document.querySelectorAll(".dropdown-menu").forEach((dropdown) => {
                    dropdown.classList.add("hidden"); // Tutup dropdown lain
                });
                if (isHidden) {
                    menu.classList.remove("hidden");
                } else {
                    menu.classList.add("hidden");
                }
            });

            // Klik di luar dropdown untuk menutup
            document.addEventListener("click", (event) => {
                if (!button.contains(event.target) && !menu.contains(event.target)) {
                    menu.classList.add("hidden");
                }
            });
        }

        // Dropdown CV
        toggleDropdown("cv-dropdown-btn", "cv-dropdown-menu");

        // Dropdown Cover Letter
        toggleDropdown("cl-dropdown-btn", "cl-dropdown-menu");

        // Dropdown Tentang
        toggleDropdown("about-dropdown-btn", "about-dropdown-menu");
    </script>

    @stack('scripts')

</body>

</html>