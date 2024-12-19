@extends('pelamar.dashboard.master_user')
@section('title', 'Info Lowongan | CVRE GENERATE')

@section('content')
<div class="container-fluid mt-4">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h5 class="mb-0 font-weight-bold text-primary">Dashboard / <span class="text-dark">Informasi Lowongan</span></h5>
    </div>

    <div class="row">
        <!-- Left Column: Job Listings -->
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 text-center">
                    <h6 class="m-0 font-weight-bold text-dark">Lowongan Lainnya</h6>
                </div>
                <div class="card-body" style="max-height: 760px; overflow-y: auto">
                    <!-- Lowongan 1 -->
                    <div class="list-group mb-3">
                        <button class="list-group-item list-group-item-action" onclick="showJobDetail('admin1')">
                            <div class="d-flex align-items-start">
                                <img src="{{asset('assets/perusahaan/perusahaan1.png')}}" class="mr-3" alt="Admin Office" style="width: 94px; height: 94px" />
                                <div>
                                    <h5 class="mb-0 font-weight-bold">Admin Office</h5>
                                    <small>PT Suka Makmur Jaya Indonesia<br />Cakung Timur, Jakarta Timur<br />Rp 19.000.000 / Bulan</small>
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- Lowongan 2 -->
                    <div class="list-group mb-3">
                        <button class="list-group-item list-group-item-action" onclick="showJobDetail('sysadmin')">
                            <div class="d-flex align-items-start">
                                <img src="{{asset('assets/perusahaan/perusahaan2.png')}}" class="mr-3" alt="Sysadmin" style="width: 94px; height: 94px" />
                                <div>
                                    <h5 class="mb-0 font-weight-bold">Sysadmin</h5>
                                    <small>PT Suka Makmur Jaya Indonesia<br />Cakung Timur, Jakarta Timur<br />Rp 19.000.000 / Bulan</small>
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- Lowongan 3 -->
                    <div class="list-group mb-3">
                        <button class="list-group-item list-group-item-action" onclick="showJobDetail('admin2')">
                            <div class="d-flex align-items-start">
                                <img src="{{asset('assets/perusahaan/perusahaan3.png')}}" class="mr-3" alt="Admin Office" style="width: 94px; height: 94px" />
                                <div>
                                    <h5 class="mb-0 font-weight-bold">Admin Office</h5>
                                    <small>PT Suka Makmur Jaya Indonesia<br />Cakung Timur, Jakarta Timur<br />Rp 19.000.000 / Bulan</small>
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- Lowongan 4 -->
                    <div class="list-group mb-3">
                        <button class="list-group-item list-group-item-action" onclick="showJobDetail('admin3')">
                            <div class="d-flex align-items-start">
                                <img src="{{asset('assets/perusahaan/perusahaan4.png')}}" class="mr-3" alt="Sysadmin" style="width: 94px; height: 94px" />
                                <div>
                                    <h5 class="mb-0 font-weight-bold">Sysadmin</h5>
                                    <small>PT Suka Makmur Jaya Indonesia<br />Cakung Timur, Jakarta Timur<br />Rp 19.000.000 / Bulan</small>
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- Lowongan 5 -->
                    <div class="list-group mb-3">
                        <button class="list-group-item list-group-item-action" onclick="showJobDetail('admin4')">
                            <div class="d-flex align-items-start">
                                <img src="{{asset('assets/perusahaan/perusahaan5.png')}}" class="mr-3" alt="Sysadmin" style="width: 94px; height: 94px" />
                                <div>
                                    <h5 class="mb-0 font-weight-bold">Sysadmin</h5>
                                    <small>PT Suka Makmur Jaya Indonesia<br />Cakung Timur, Jakarta Timur<br />Rp 19.000.000 / Bulan</small>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Job Details -->
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 text-center">
                    <h6 class="m-0 font-weight-bold text-dark">INFORMASI PERUSAHAAN & LOWONGAN</h6>
                </div>
                <div class="card-body" id="job-detail">
                    <!-- Detail lowongan akan dimuat di sini -->
                    <div class="text-center">Silakan pilih lowongan untuk melihat detail.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const jobDetails = {
        admin1: {
            image: "/assets/perusahaan/perusahaan1.png",
            title: "Admin Office - PT Suka Makmur Jaya Indonesia (ID-20123900123)",
            location: "Cakung Timur, Jakarta Timur",
            position: "Admin Office (Accounting Office)",
            type: "Kontrak",
            salary: "Rp 10.000.000 - Rp 19.000.000 / Bulan",
            description: "PT Suka Makmur, perusahaan yang bergerak di bidang distribusi dan manufaktur, membuka lowongan untuk posisi Admin Office dengan tanggung jawab utama mengelola administrasi kantor.",
            responsibilities: ["Mengelola dokumen administrasi secara akurat dan rapi.", "Memastikan semua data keuangan tercatat dengan baik.", "Berkoordinasi dengan tim keuangan dan HR untuk kebutuhan kantor."],
            requirements: ["Pendidikan minimal D3/S1 di bidang Administrasi atau Akuntansi.", "Mampu menggunakan Microsoft Office, terutama Excel.", "Teliti, jujur, dan mampu bekerja di bawah tekanan."],
        },
        sysadmin: {
            image: "/assets/perusahaan/perusahaan2.png",
            title: "System Administrator - PT Digital Nusantara (ID-30234900124)",
            location: "Kemayoran, Jakarta Pusat",
            position: "System Administrator (IT Infrastructure)",
            type: "Full-time",
            salary: "Rp 12.000.000 - Rp 18.000.000 / Bulan",
            description: "PT Digital Nusantara membutuhkan seorang System Administrator untuk memastikan infrastruktur TI berfungsi optimal.",
            responsibilities: ["Memelihara server dan jaringan perusahaan agar selalu stabil.", "Melakukan troubleshooting terhadap masalah sistem.", "Mengembangkan solusi keamanan untuk data perusahaan."],
            requirements: ["Pendidikan minimal D3/S1 di bidang Teknologi Informasi.", "Berpengalaman di bidang jaringan dan keamanan data.", "Mampu mengelola server berbasis Linux dan Windows."],
        },
        admin2: {
            image: "/assets/perusahaan/perusahaan3.png",
            title: "HR Officer - PT Mitra Sejahtera (ID-40245900125)",
            location: "Bekasi Selatan, Jawa Barat",
            position: "HR Officer",
            type: "Kontrak",
            salary: "Rp 9.000.000 - Rp 15.000.000 / Bulan",
            description: "PT Mitra Sejahtera sedang mencari HR Officer untuk membantu pengelolaan SDM perusahaan.",
            responsibilities: ["Mengelola administrasi karyawan termasuk data absensi.", "Mendukung proses rekrutmen dan pelatihan karyawan baru.", "Menyusun laporan bulanan terkait performa SDM."],
            requirements: ["Lulusan minimal S1 Manajemen SDM atau Psikologi.", "Berpengalaman minimal 1 tahun di bidang HR.", "Memiliki kemampuan komunikasi interpersonal yang baik."],
        },
        admin3: {
            image: "/assets/perusahaan/perusahaan4.png",
            title: "Marketing Executive - PT Kreatif Mandiri (ID-50256900126)",
            location: "Surabaya, Jawa Timur",
            position: "Marketing Executive",
            type: "Freelance",
            salary: "Rp 5.000.000 - Rp 12.000.000 / Bulan",
            description: "PT Kreatif Mandiri mencari individu yang kreatif untuk bergabung sebagai Marketing Executive.",
            responsibilities: ["Menyusun strategi pemasaran produk di media digital.", "Berkomunikasi dengan klien untuk memahami kebutuhan mereka.", "Melakukan riset pasar dan memonitor tren pemasaran."],
            requirements: ["Lulusan minimal S1 Marketing atau Komunikasi.", "Mampu bekerja secara mandiri dengan orientasi hasil.", "Berpengalaman dalam menggunakan platform iklan digital."],
        },
        admin4: {
            image: "/assets/perusahaan/perusahaan5.png",
            title: "Graphic Designer - PT Kreatif Visual (ID-60267900127)",
            location: "Denpasar, Bali",
            position: "Graphic Designer",
            type: "Remote",
            salary: "Rp 8.000.000 - Rp 14.000.000 / Bulan",
            description: "PT Kreatif Visual sedang mencari Graphic Designer yang berpengalaman untuk membuat desain kreatif yang inovatif.",
            responsibilities: ["Merancang desain untuk keperluan promosi dan branding.", "Bekerja sama dengan tim marketing untuk ide visual.", "Menyusun konten grafis untuk media sosial dan website."],
            requirements: ["Pendidikan minimal D3/S1 Desain Grafis atau Seni.", "Menguasai Adobe Photoshop dan Illustrator.", "Memiliki portofolio desain yang kreatif dan menarik."],
        },
    };

    function showJobDetail(jobId) {
        const job = jobDetails[jobId];
        if (job) {
            const jobDetailContainer = document.getElementById("job-detail");
            jobDetailContainer.innerHTML = `
            <div class="col-md-3 text-center">
                <img src="${job.image}" class="img-fluid rounded" alt="Company Logo" style="max-width: 100px" />
            </div>
            <h5 class="font-weight-bold pl-4 pr-4 pt-4">${job.title}</h5>
            <div class="row mt-3 pl-4 pr-4">
                <div class="col-md-9">
                <ul class="list-unstyled mb-4">
                    <li><i class="fas fa-map-marker-alt mr-2"></i><strong>Lokasi:</strong> ${job.location}</li>
                    <li><i class="fas fa-briefcase mr-2"></i><strong>Posisi:</strong> ${job.position}</li>
                    <li><i class="fas fa-clock mr-2"></i><strong>Jenis:</strong> ${job.type}</li>
                    <li><i class="fas fa-money-bill-wave mr-2"></i><strong>Gaji:</strong> ${job.salary}</li>
                </ul>
                <p><strong>Deskripsi Pekerjaan:</strong></p>
                <p class="text-justify">${job.description}</p>
                </div>
            </div>
            <div class="pl-4 pr-4">
                <hr />
                <h6 class="font-weight-bold">Tanggung Jawab Utama:</h6>
                <ul>
                    ${job.responsibilities.map((item) => `<li>${item}</li>`).join("")}
                </ul>
                <h6 class="font-weight-bold">Persyaratan:</h6>
                <ul>
                    ${job.requirements.map((item) => `<li>${item}</li>`).join("")}
                </ul>
                <button type="submit" class="btn btn-primary d-block mt-10">Kirim Lamaran</button>
            </div>
            `;
        }
    }
</script>
@endpush