@extends('layouts.master')

@section('title', 'FAQ | CVRE GENERATE')

@section('content')
<!-- FAQ -->
<div class="min-h-screen flex items-center justify-center relative">
    <!-- Background Image -->
    <img src="{{asset('assets/homepage/background.png')}}" alt="Background Shape" class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none" />

    <!-- FAQ Section -->
    <div class="w-full max-w-4xl px-6 py-12 bg-white bg-opacity-80 rounded-lg shadow-md relative z-10 mt-12 mb-16"> <!-- Menambahkan margin atas dan bawah -->
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-blue-800 mb-2">FAQ</h1>
            <p class="text-gray-600">Pertanyaan yang sering diajukan di platform CVRE Generate.</p>
        </div>

        <!-- FAQ Section -->
        <div class="space-y-4">
            <!-- FAQ Item 1 -->
            <div class="border rounded-lg border-blue-200 shadow-sm">
                <button
                    class="flex items-center justify-between w-full px-4 py-3 text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onclick="toggleAnswer(this)">
                    <span class="font-bold">Apa itu CVRE Generate?</span>
                    <svg class="w-5 h-5 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div class="answer hidden px-4 py-3 text-gray-700 transition-all duration-300 ease-in-out max-h-0 overflow-hidden">
                    CVRE adalah platform website yang dapat membantu serta memudahkan para pelamar untuk membuat CV
                    dan Cover Letter dengan berbagai macam template yang sudah disediakan. Selain itu, di website CVRE
                    ini para pelamar dapat mengirimkan CV dan Cover Letter secara langsung kepada perusahaan yang
                    sedang membuka lowongan pekerjaan.
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="border rounded-lg border-blue-200 shadow-sm">
                <button
                    class="flex items-center justify-between w-full px-4 py-3 text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onclick="toggleAnswer(this)">
                    <span class="font-bold">Apakah layanan CVRE Generate gratis?</span>
                    <svg class="w-5 h-5 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div class="answer hidden px-4 py-3 text-gray-700 transition-all duration-300 ease-in-out max-h-0 overflow-hidden">
                    CVRE Generate menyediakan pilihan gratis dan berbayar. Versi gratis menawarkan akses ke sejumlah template dan fitur dasar. Untuk fitur dan template premium, Anda bisa meng-upgrade ke paket berbayar.
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="border rounded-lg border-blue-200 shadow-sm">
                <button
                    class="flex items-center justify-between w-full px-4 py-3 text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onclick="toggleAnswer(this)">
                    <span class="font-bold">Bagaimana cara menggunakan CVRE Generate?</span>
                    <svg class="w-5 h-5 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div class="answer hidden px-4 py-3 text-gray-700 transition-all duration-300 ease-in-out max-h-0 overflow-hidden">
                    Untuk menggunakan CVRE Generate, Anda hanya perlu mendaftar atau login, pilih template yang diinginkan, isi informasi seperti data pribadi, pengalaman kerja, dan keahlian, lalu simpan atau unduh CV Anda dalam format yang Anda butuhkan.
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="border rounded-lg border-blue-200 shadow-sm">
                <button
                    class="flex items-center justify-between w-full px-4 py-3 text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onclick="toggleAnswer(this)">
                    <span class="font-bold">Apakah CVRE Generate menyediakan template ATS-friendly?</span>
                    <svg class="w-5 h-5 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div class="answer hidden px-4 py-3 text-gray-700 transition-all duration-300 ease-in-out max-h-0 overflow-hidden">
                    Tentu! Kami menyediakan beberapa template ATS-friendly yang dirancang untuk mudah dibaca oleh sistem ATS (Applicant Tracking System) yang sering digunakan oleh perusahaan untuk proses penyaringan lamaran.
                </div>
            </div>

            <!-- FAQ Item 5 -->
            <div class="border rounded-lg border-blue-200 shadow-sm">
                <button
                    class="flex items-center justify-between w-full px-4 py-3 text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onclick="toggleAnswer(this)">
                    <span class="font-bold">Format file apa saja yang tersedia untuk diunduh?</span>
                    <svg class="w-5 h-5 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div class="answer hidden px-4 py-3 text-gray-700 transition-all duration-300 ease-in-out max-h-0 overflow-hidden">
                    CVRE Generate dapat diunduh dalam format PDF dan WORD, sehingga mudah dibuka dan dibagikan kepada rekruter atau untuk keperluan lamaran kerja lainnya.
                </div>
            </div>

            <!-- FAQ Item 6 -->
            <div class="border rounded-lg border-blue-200 shadow-sm">
                <button
                    class="flex items-center justify-between w-full px-4 py-3 text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onclick="toggleAnswer(this)">
                    <span class="font-bold">Apakah data pribadi saya aman di CVRE Generate?</span>
                    <svg class="w-5 h-5 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <div class="answer hidden px-4 py-3 text-gray-700 transition-all duration-300 ease-in-out max-h-0 overflow-hidden">
                    Keamanan data Anda adalah prioritas kami. CVRE Generate menggunakan enkripsi dan sistem keamanan terbaik untuk melindungi data pribadi dan informasi yang Anda masukkan di platform kami.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleAnswer(button) {
        const answer = button.nextElementSibling; // Mendapatkan elemen jawaban setelah tombol
        const icon = button.querySelector('svg'); // Mendapatkan ikon SVG di dalam tombol

        // Tampilkan atau sembunyikan jawaban dengan animasi smooth
        if (answer.classList.contains('hidden')) {
            answer.classList.remove('hidden');
            answer.style.maxHeight = answer.scrollHeight + "px";
            icon.style.transform = "rotate(90deg)"; // Putar ikon ke bawah
        } else {
            answer.style.maxHeight = "0";
            setTimeout(() => answer.classList.add('hidden'), 300); // Menyembunyikan jawaban setelah animasi selesai
            icon.style.transform = "rotate(0deg)"; // Kembalikan ikon ke posisi semula
        }
    }
</script>

@endsection