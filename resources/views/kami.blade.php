@extends('layouts.master')

@section('title', 'Tentang Kami | CVRE GENERATE')

@section('content')
<!-- Tentang Kami Section -->
<div class="min-h-screen flex items-center justify-center relative">
    <section class="relative min-h-screen overflow-hidden">
        <!-- Background Shape -->
        <img src="{{ asset('assets/images/background.png') }}" alt="Background Shape"
            class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none">

        <!-- Content -->
        <div class="relative z-10">
            <div class="container mx-auto flex flex-col-reverse md:flex-row items-center justify-between py-12 px-6 md:px-0 mt-8">
                <div class="md:w-1/2 text-center md:text-left">
                    <h1 class="text-4xl lg:text-5xl font-extrabold text-blue-800 leading-tight mb-6">
                        Tentang Kami
                    </h1>
                    <p class="text-lg lg:text-xl text-gray-700 leading-relaxed mb-8">
                        CVRE adalah platform website yang dapat membantu serta memudahkan para pelamar untuk membuat dan mencari template CV dan Cover Letter dengan berbagai macam template yang sudah disediakan. Selain itu, di website CVRE ini para pelamar dapat mengirimkan CV dan Cover Letter nya secara langsung kepada perusahaan yang sedang membuka lowongan pekerjaan. Melalui CVRE ini, Anda tidak hanya dapat membuat template CV dan Cover Letter saja, tapi Anda juga dapat mencari pekerjaan dari berbagai perusahaan yang sudah bekerjasama dengan kami.
                    </p>
                </div>
                <div class="md:w-1/2 flex justify-end">
                    <img src="{{ asset('assets/carousel-banner/banner-2.png') }}" alt="Banner 2" class="w-4/5 lg:w-auto">
                </div>
            </div>
        </div>

        <!-- Gradients for smooth transition -->
        <div class="absolute bottom-0 inset-x-0 h-24 bg-gradient-to-t from-blue-50 via-blue-50 to-transparent"></div>
    </section>
</div>

<!-- Tim Kami Section -->
<section class="bg-blue-50 py-16">
    <div class="container mx-auto text-center">
        <h2 class="text-3xl font-bold text-blue-800 mb-12">Tim Kami</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <img src="{{ asset('assets/our-team/1.png') }}" alt="Abrar Adyasakha"
                    class="w-36 h-36 rounded-full mx-auto mb-4 object-cover">
                <h3 class="text-xl font-bold text-blue-800">Abrar Adyasakha</h3>
                <p class="text-sm text-gray-500">UI/UX Design</p>
            </div>
            <!-- Card 2 -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <img src="{{ asset('assets/our-team/1.png') }}" alt="Febriana"
                    class="w-36 h-36 rounded-full mx-auto mb-4 object-cover">
                <h3 class="text-xl font-bold text-blue-800">Febriana</h3>
                <p class="text-sm text-gray-500">UI/UX Design</p>
            </div>
            <!-- Card 3 -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <img src="{{ asset('assets/our-team/1.png') }}" alt="Muhammad Ferry K"
                    class="w-36 h-36 rounded-full mx-auto mb-4 object-cover">
                <h3 class="text-xl font-bold text-blue-800">Muhammad Ferry K</h3>
                <p class="text-sm text-gray-500">UI/UX Design</p>
            </div>
            <!-- Card 4 -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <img src="{{ asset('assets/our-team/2.png') }}" alt="Nazwa Nurfadiya R"
                    class="w-36 h-36 rounded-full mx-auto mb-4 object-cover">
                <h3 class="text-xl font-bold text-blue-800">Nazwa Nurfadiya R</h3>
                <p class="text-sm text-gray-500">Digital Marketing</p>
            </div>
            <!-- Card 5 -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <img src="{{ asset('assets/our-team/2.png') }}" alt="Widitha Fasha"
                    class="w-36 h-36 rounded-full mx-auto mb-4 object-cover">
                <h3 class="text-xl font-bold text-blue-800">Widitha Fasha</h3>
                <p class="text-sm text-gray-500">Digital Marketing</p>
            </div>
            <!-- Card 6 -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <img src="{{ asset('assets/our-team/2.png') }}" alt="Jesika Yohanna S"
                    class="w-36 h-36 rounded-full mx-auto mb-4 object-cover">
                <h3 class="text-xl font-bold text-blue-800">Jesika Yohanna S</h3>
                <p class="text-sm text-gray-500">Digital Marketing</p>
            </div>
            <!-- Card 7 -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <img src="{{ asset('assets/our-team/2.png') }}" alt="Yulianawati"
                    class="w-36 h-36 rounded-full mx-auto mb-4 object-cover">
                <h3 class="text-xl font-bold text-blue-800">Yulianawati</h3>
                <p class="text-sm text-gray-500">Digital Marketing</p>
            </div>
            <!-- Card 8 -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <img src="{{ asset('assets/our-team/1.png') }}" alt="Kelvin Erlangga S"
                    class="w-36 h-36 rounded-full mx-auto mb-4 object-cover">
                <h3 class="text-xl font-bold text-blue-800">Kelvin Erlangga S</h3>
                <p class="text-sm text-gray-500">Front End Developer</p>
            </div>
            <!-- Card 9 -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <img src="{{ asset('assets/our-team/1.png') }}" alt="Richard Diga A"
                    class="w-36 h-36 rounded-full mx-auto mb-4 object-cover">
                <h3 class="text-xl font-bold text-blue-800">Richard Diga A</h3>
                <p class="text-sm text-gray-500">Back End Developer</p>
            </div>
        </div>
    </div>
</section>
@endsection