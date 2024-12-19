@extends('layouts.master')

@section('title', 'Register | CVRE GENERATE')

@section('content')
<!-- Form Pendaftaran -->
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
    <!-- Background Image -->
    <img src="{{asset('assets/homepage/background.png')}}" alt="Background Shape" class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none" />

    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-4xl z-10">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <a href="/">
                <img src="{{asset('assets/homepage/logo.png')}}" alt="Logo" style="width: 180px; height: auto" />
            </a>
        </div>

        <!-- Tab Navigation -->
        <div class="flex justify-center mb-6">
            <button id="pelamarTab" class="border-b-2 border-blue-700 text-blue-700 font-semibold px-6 py-2 tab-active">Pelamar</button>
            <button id="perusahaanTab" class="text-gray-500 font-medium px-6 py-2">Perusahaan</button>
        </div>

        <!-- Form Pelamar -->
        <div id="pelamarForm">
            <form action="{{route('register')}}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
                    <div>
                        <div class="mb-4">
                            <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input id="nama" name="name" value="{{old('name')}}" type="text" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2" />
                        </div>
                        <div class="mb-4">
                            <label for="jenis-kelamin" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                            <select id="jenis-kelamin" name="gender" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2">
                                <option value="" disabled selected>Pilih</option>
                                <option value="laki-laki">Laki-Laki</option>
                                <option value="perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="mb-3.5">
                            <label for="tanggal-lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input id="tanggal-lahir" name="date_of_birth_pelamar" value="{{old('date_of_birth_pelamar')}}" type="date" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2" />
                        </div>
                        <div class="mb-4">
                            <label for="kota-domisili" class="block text-sm font-medium text-gray-700">Kota Domisili</label>
                            <input id="kota-domisili" name="city" value="{{old('city')}}" type="text" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2" />
                        </div>
                    </div>

                    <div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                            <input id="email" name="email" type="email" value="{{old('email')}}" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2" />
                            @error('email')
                            <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="nomor-telepon" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                            <input id="nomor-telepon" name="phone" value="{{old('phone')}}" type="number" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2" />
                            @error('phone')
                            <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input id="password" name="password" type="password" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2" />
                            @error('password')
                            <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="password-konfirmasi" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                            <input id="password-konfirmasi" name="password_confirmation" type="password" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2" />
                            @error('password')
                            <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-center">
                    <button
                        type="submit"
                        style="background-color: #3538cd; color: white; padding: 12px 16px; border-radius: 5px; width: 40%; border: none; transition: background-color 0.3s"
                        onmouseover="this.style.backgroundColor='#2727a1';"
                        onmouseout="this.style.backgroundColor='#3538cd';">
                        Daftar
                    </button>
                </div>
            </form>
        </div>

        <!-- Form Perusahaan -->
        <div id="perusahaanForm" class="hidden">
            <form action="{{route('register')}}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
                    <div>
                        <div class="mb-4">
                            <label for="nama-perusahaan" class="block text-sm font-medium text-gray-700">Nama Perusahaan</label>
                            <input id="nama-perusahaan" name="name" value="{{old('name')}}" type="text" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2" />
                        </div>
                        <div class="mb-4">
                            <label for="nama-pengguna" class="block text-sm font-medium text-gray-700">Nama Pengguna</label>
                            <input id="nama-pengguna" name="name_user_company" value="{{old('name_user_company')}}" type="text" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2" />
                        </div>
                        <div class="mb-4">
                            <label for="jenis-perusahaan" class="block text-sm font-medium text-gray-700">Jenis Perusahaan</label>
                            <input id="jenis-perusahaan" name="type_of_company" value="{{old('type_of_company')}}" type="text" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2" />
                        </div>
                        <div class="mb-4">
                            <label for="domisili-perusahaan" class="block text-sm font-medium text-gray-700">Kota Domisili Perusahaan</label>
                            <input id="domisili-perusahaan" name="city" value="{{old('city')}}" type="text" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2" />
                        </div>
                    </div>

                    <div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email Perusahaan</label>
                            <input id="email" name="email" type="email" value="{{old('email')}}" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2" />
                            @error('email')
                            <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="nomor-telepon" class="block text-sm font-medium text-gray-700">Nomor Telepon Perusahaan</label>
                            <input id="nomor-telepon" name="phone" value="{{old('phone')}}" type="number" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2" />
                            @error('phone')
                            <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input id="password" name="password" type="password" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2" />
                            @error('password')
                            <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="password-konfirmasi" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                            <input id="password-konfirmasi" name="password_confirmation" type="password" required class="mt-1 block w-full rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:ring-2 focus:outline-none p-2" />
                            @error('password')
                            <div class="text-sm font-thin text-red-500">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-center">
                    <button
                        type="submit"
                        style="background-color: #3538cd; color: white; padding: 12px 16px; border-radius: 5px; width: 40%; border: none; transition: background-color 0.3s"
                        onmouseover="this.style.backgroundColor='#2727a1';"
                        onmouseout="this.style.backgroundColor='#3538cd';">
                        Daftar
                    </button>
                </div>
            </form>
        </div>

        <!-- Tautan ke login -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">Sudah punya akun? <a href="{{route('login')}}" class="text-blue-700">Masuk</a></p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script Tab Navigation (New)
    const pelamarTab = document.getElementById("pelamarTab");
    const perusahaanTab = document.getElementById("perusahaanTab");
    const pelamarForm = document.getElementById("pelamarForm");
    const perusahaanForm = document.getElementById("perusahaanForm");

    pelamarTab.addEventListener("click", () => {
        pelamarTab.classList.add("border-b-2", "border-blue-700", "text-blue-700", "font-semibold");
        perusahaanTab.classList.remove("border-b-2", "border-blue-700", "text-blue-700", "font-semibold");
        pelamarForm.classList.remove("hidden");
        perusahaanForm.classList.add("hidden");
    });

    perusahaanTab.addEventListener("click", () => {
        perusahaanTab.classList.add("border-b-2", "border-blue-700", "text-blue-700", "font-semibold");
        pelamarTab.classList.remove("border-b-2", "border-blue-700", "text-blue-700", "font-semibold");
        perusahaanForm.classList.remove("hidden");
        pelamarForm.classList.add("hidden");
    });
</script>
@endpush