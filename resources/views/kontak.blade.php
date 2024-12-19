@extends('layouts.master')

@section('title', 'Kontak | CVRE GENERATE')

@section('content')
<!-- Kontak Form -->
<div class="min-h-screen flex items-center justify-center relative">
    <!-- Background Image -->
    <img src="{{ asset('assets/homepage/background.png') }}" alt="Background Shape"
        class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none" />

    <div class="bg-gradient-to-br from-white to-blue-50 shadow-lg rounded-lg p-8 w-full max-w-md relative z-10">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-4xl font-bold text-blue-800 mb-2">Kontak</h1>
            <p class="text-gray-600">Silahkan kirim pertanyaan anda <br> melalui Form dibawah ini.</p>
        </div>

        <!-- Form Kontak -->
        <form>
            <!-- Email Address -->
            <div class="mb-4">
                <input
                    id="email"
                    type="email"
                    name="email"
                    placeholder="Alamat Email"
                    required
                    autofocus
                    class="block w-full rounded border border-gray-300 bg-white text-gray-700 placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none shadow-sm"
                    style="height: 50px; padding: 0 16px;" />
            </div>

            <!-- Nama Lengkap -->
            <div class="mb-4">
                <input
                    id="name"
                    type="text"
                    name="name"
                    placeholder="Nama Lengkap"
                    required
                    class="block w-full rounded border border-gray-300 bg-white text-gray-700 placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none shadow-sm"
                    style="height: 50px; padding: 0 16px;" />
            </div>

            <!-- Pertanyaan -->
            <div class="mb-6">
                <textarea
                    id="message"
                    name="message"
                    placeholder="Pertanyaan Anda"
                    rows="4"
                    required
                    class="block w-full rounded border border-gray-300 bg-white text-gray-700 placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none shadow-sm"
                    style="padding: 16px; resize: none; height: 180px;"></textarea>
            </div>

            <!-- Submit Button -->
            <div>
                <button
                    type="submit"
                    class="w-full text-white font-bold py-3 shadow-md focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-colors"
                    style="background-color: #3538cd; height: 50px; border-radius: 5px; transition: background-color 0.3s;"
                    onmouseover="this.style.backgroundColor='#2727a1';"
                    onmouseout="this.style.backgroundColor='#3538cd';">
                    Kirim Pertanyaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection