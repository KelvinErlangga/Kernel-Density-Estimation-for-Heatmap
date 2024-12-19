@extends('layouts.master')

@section('title', 'Verifikasi Email | CVRE GENERATE')

@section('content')
<!-- Login Form -->
<div class="min-h-screen flex items-center justify-center relative">
    <!-- Background Image -->
    <img src="{{asset('assets/images/background.png')}}" alt="Background Shape" class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none" />

    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md relative z-20">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <img src="{{asset('assets/homepage/logo.png')}}" alt="Logo" class="w-44 h-auto" />
        </div>

        <!-- Verifikasi Email Message -->
        <div class="text-center">
            <h2 class="text-gray-800 font-bold text-xs mb-10 my-8">
                Kami telah mengirim email verifikasi ke alamat
                <span class="text-blue-600">{{Auth::user()->email}}</span>,
                silahkan cek di spam bila perlu.
            </h2>
            <img src="{{asset('assets/images/verification.png')}}"
                alt="Illustration"
                class="w-52 h-auto mx-auto m-6" />

            <!-- Teks dan Tombol di Sebaris -->
            <div class="text-gray-600 text-sm my-8 flex items-center justify-center gap-2">
                <span>Belum dapat email verifikasi?</span>
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="text-blue-600 font-semibold hover:underline">
                        Klik disini
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection