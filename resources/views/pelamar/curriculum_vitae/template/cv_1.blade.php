<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Preview CV | CVRE GENERATE</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{asset('css/cv_1.css')}}" />
    <link rel="icon" href="{{asset('assets/icons/logo.svg')}}" type="image/x-icon">


    <style>
        a {
            text-decoration: none;
        }
    </style>
</head>

<body>
    <!-- Kontainer Utama -->
    <div class="main-container">
        <!-- Panel Editor -->
        <div class="editor-panel">
            <div style="text-align: center; color: #3538cd; font-size: 32px; font-family: Poppins; font-weight: 400; line-height: 41px;">
                Editor
            </div>
            <div class="editor-item font-dropdown">
                <img src="{{asset('assets/images/font.svg')}}" alt="Font Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
                <div class="custom-select-wrapper">
                    <select class="editor-btn" onchange="changeFont(this)">
                        <option value="Poppins, sans-serif">Poppins</option>
                        <option value="Arial, sans-serif">Arial</option>
                        <option value="Times New Roman, serif">Times New Roman</option>
                        <option value="Roboto, sans-serif">Roboto</option>
                        <option value="Georgia, serif">Georgia</option>
                        <option value="Courier New, monospace">Courier New</option>
                    </select>
                </div>
            </div>
            <div class="editor-item">
                <img src="{{asset('assets/images/color.svg')}}" alt="Color Icon" />
                <input type="color" onchange="changeBackgroundColor(this.value)" />
            </div>
            <div class="editor-item">
                <img src="{{asset('assets/images/download.svg')}}" alt="Download Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
                <div style="display: flex; gap: 8px;">
                    <button class="editor-btn" onclick="downloadAsImage('png')">PNG</button>
                    <button class="editor-btn" onclick="downloadAsImage('jpeg')">JPEG</button>
                    <button class="editor-btn" onclick="downloadAsPDF()">PDF</button>
                </div>
            </div>

            <div class="editor-item">
                <img src="{{asset('assets/images/print.svg')}}" alt="Print Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
                <button class="editor-btn" onclick="printCV()">Print</button>
            </div>

            <div class="editor-item">
                <img src="{{asset('assets/images/edit.svg')}}" alt="Edit Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
                <a href="{{route('pelamar.curriculum_vitae.profile.index', $curriculumVitaeUser->id)}}" class="editor-btn">Edit Data</a>
            </div>

            {{-- <div class="editor-item">
                <img src="{{asset('assets/images/download.svg')}}" alt="Save Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
                <button class="editor-btn" onclick="savePdfToDashboard()">Save to Dashboard</button>
            </div> --}}
        
            <div class="editor-item">
                <img src="{{asset('assets/images/download.svg')}}" alt="Save Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
                <button id="saveToDashboardBtn" data-template-id="{{ $curriculumVitaeUser->template_curriculum_vitae_id ?? 1 }}">Save to Dashboard</button>
            </div>

            <div class="editor-item">
                <img src="{{asset('assets/images/home.svg')}}" alt="Home Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
                <a href="{{route('home')}}" class="editor-btn">Back to Home</a>
            </div>
        </div>


        <!-- Panel Konten (CV Template) -->
        <div class="container">
            <div id="content">
                @php
                $personalDetail = $curriculumVitaeUser->personalDetail;
                @endphp
                <!-- Left Panel -->
                <div class="left-panel">
                    <!-- Profile Section -->
                    @if($personalDetail)
                    <a href="{{route('pelamar.curriculum_vitae.profile.index', $curriculumVitaeUser->id)}}">

                        <div class="profile-section">
                            @if($personalDetail->avatar_curriculum_vitae)
                            <div class="profile-img">
                                <img src="{{Storage::url($personalDetail->avatar_curriculum_vitae)}}" alt="foto">
                            </div>
                            @endif
                            <div class="profile-info">
                                <p class="name">{{$personalDetail->first_name_curriculum_vitae}} {{$personalDetail->last_name_curriculum_vitae}}</p>
                                <!-- <p class="role">Frontend Developer</p> -->
                            </div>
                        </div>
                    </a>
                    <!-- Details Section -->
                    <div class="details-section">
                        <p class="details-title">Details</p>
                        <div class="detail-item">
                            <p class="sub-menu">Address</p>
                            <p>{{$personalDetail->city_curriculum_vitae}}, {{$personalDetail->address_curriculum_vitae}}</p>
                        </div>
                        <div class="detail-item">
                            <p class="sub-menu">Phone</p>
                            <p>{{$personalDetail->phone_curriculum_vitae}}</p>
                        </div>
                        <div class="detail-item">
                            <p class="sub-menu">Email</p>
                            <p>{{$personalDetail->email_curriculum_vitae}}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Links Section -->
                    @if($curriculumVitaeUser->links->isNotEmpty())
                    <div class="links-section">
                        <a href="{{route('pelamar.curriculum_vitae.social_media.index', $curriculumVitaeUser->id)}}">
                            <p class="links-title">Links</p>
                        </a>
                        <div class="link-item">
                            @foreach($curriculumVitaeUser->links as $link)
                            <a href="{{$link->url}}">
                                <p class="sub-menu">{{$link->link_name}}</p>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Skills Section -->
                    @if($curriculumVitaeUser->skills->isNotEmpty())
                    <div class="skills-section">
                        <a href="{{route('pelamar.curriculum_vitae.skill.index', $curriculumVitaeUser->id)}}">
                            <p class="skills-title">Skills</p>
                            @foreach($curriculumVitaeUser->skills as $skill)
                            <div class="skill-item">
                                <p class="sub-menu">{{$skill->skill_name}} - {{$skill->category_level}}</p>
                                <div class="skill-divider"></div>
                            </div>
                            @endforeach
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Right Panel -->
                <div class="right-panel">
                    @if($personalDetail->personal_summary)
                    <div>
                        <a href="{{route('pelamar.curriculum_vitae.profile.index', $curriculumVitaeUser->id)}}">
                            <h1 style="color: black;">Profile</h1>
                            <p style="color: black;">
                                {{$personalDetail->personal_summary}}
                            </p>
                        </a>
                    </div>
                    @endif

                    @if($curriculumVitaeUser->experiences->isNotEmpty())
                    <div>
                        <h1>Experience</h1>
                        @foreach($curriculumVitaeUser->experiences as $experience)
                        <div>
                            <h2>{{$experience->company_experience}}</h2>
                            <h3>{{$experience->position_experience}}</h3>

                            @if($experience->end_date)
                            <p>{{date('M Y', strtotime($experience->start_date))}} - {{date('M Y', strtotime($experience->end_date))}}</p>
                            @else
                            <p>{{date('M Y', strtotime($experience->start_date))}} - Present</p>
                            @endif

                            @if($experience->description_experience)
                            {!! $experience->description_experience !!}
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($curriculumVitaeUser->educations->isNotEmpty())
                    <div>
                        <h1>Education</h1>
                        @foreach($curriculumVitaeUser->educations as $education)
                        <div>
                            <h2>{{$education->school_name}}</h2>
                            @if($education->end_date)
                            <p>{{$education->field_of_study}}, {{date('M Y', strtotime($education->start_date))}} - {{date('M Y', strtotime($education->end_date))}}</p>
                            @else
                            <p>{{$education->field_of_study}}, {{date('M Y', strtotime($education->start_date))}} - Present</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($curriculumVitaeUser->languages->isNotEmpty())
                    <div>
                        <h1>Languages</h1>
                        @foreach($curriculumVitaeUser->languages as $language)
                        <p>{{$language->language_name}}</p>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <script>
            // Mengubah font sesuai pilihan dropdown
            function changeFont(selectElement) {
                const content = document.getElementById("content");
                const selectedFont = selectElement.value;
                content.style.fontFamily = selectedFont;
            }

            // Mengubah warna latar belakang pada Left Panel
            function changeBackgroundColor(color) {
                const leftPanel = document.querySelector(".left-panel");
                leftPanel.style.backgroundColor = color;

                // Hitung luminance (cek terang/gelap)
                const rgb = hexToRgb(color);
                const brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;

                // Atur warna teks di left-panel supaya kontras
                if (brightness > 128) {
                    leftPanel.style.color = "#000000";
                    leftPanel.querySelectorAll("p, h1, h2, h3, a").forEach(el => el.style.color = "#000000");
                } else {
                    leftPanel.style.color = "#FFFFFF";
                    leftPanel.querySelectorAll("p, h1, h2, h3, a").forEach(el => el.style.color = "#FFFFFF");
                }
            }

            function hexToRgb(hex) {
                hex = hex.replace(/^#/, "");
                if (hex.length === 3) {
                    hex = hex.split("").map(x => x + x).join("");
                }
                const num = parseInt(hex, 16);
                return {
                    r: (num >> 16) & 255,
                    g: (num >> 8) & 255,
                    b: num & 255
                };
            }


            // Fungsi download PNG, JPEG, dan PDF (sudah ada di kode awal)
            async function downloadAsImage(type) {
                const content = document.getElementById("content");
                const canvas = await html2canvas(content);
                const image = canvas.toDataURL(`image/${type}`);
                const link = document.createElement("a");
                link.href = image;
                link.download = `cv.${type}`;
                link.click();
            }

            async function downloadAsPDF() {
                const content = document.getElementById("content");

                // 1) render halaman jadi canvas (scale tinggi supaya hasil PDF tajam)
                const canvas = await html2canvas(content, { scale: 2, useCORS: true, allowTaint: true });

                // ukuran canvas (pixel)
                const imgWidthPx = canvas.width;
                const imgHeightPx = canvas.height;

                // 2) siapkan pdf
                const pdf = new jspdf.jsPDF("p", "mm", "a4");
                const pageWidthMm = pdf.internal.pageSize.getWidth();   // ~210
                const pageHeightMm = pdf.internal.pageSize.getHeight(); // ~297

                // 3) setting margin (ubah sesuai kebutuhan)
                const marginTop = 10;    // mm atas setiap halaman
                const marginBottom = 10; // mm bawah setiap halaman
                const marginLeft = 8;   // mm kiri
                const marginRight = 8;  // mm kanan

                const availableWidthMm = pageWidthMm - marginLeft - marginRight;
                const availableHeightMm = pageHeightMm - marginTop - marginBottom;

                // 4) konversi px <-> mm berdasarkan lebar canvas yang kita map ke pageWidth
                const pxPerMm = imgWidthPx / pageWidthMm; // pixels per mm
                const sliceHeightPx = Math.floor(availableHeightMm * pxPerMm); // tinggi tiap slice dalam px

                // 5) potong canvas per-slice dan tambahkan ke pdf
                let positionY = 0;
                let pageIndex = 0;

                while (positionY < imgHeightPx) {
                    const currentSliceHeightPx = Math.min(sliceHeightPx, imgHeightPx - positionY);

                    // buat canvas sementara untuk slice
                    const sliceCanvas = document.createElement("canvas");
                    sliceCanvas.width = imgWidthPx;
                    sliceCanvas.height = currentSliceHeightPx;
                    const ctx = sliceCanvas.getContext("2d");

                    // draw slice dari canvas utama ke sliceCanvas
                    ctx.drawImage(
                    canvas,
                    0, positionY,                // sumber: x,y
                    imgWidthPx, currentSliceHeightPx, // sumber: width,height
                    0, 0,                        // tujuan: x,y
                    imgWidthPx, currentSliceHeightPx  // tujuan: width,height
                    );

                    const imgData = sliceCanvas.toDataURL("image/png");

                    // convert tinggi slice ke mm agar sesuai ukuran pada pdf
                    const sliceHeightMm = currentSliceHeightPx / pxPerMm;

                    if (pageIndex > 0) pdf.addPage();
                    // taruh slice di posisi marginLeft, marginTop dengan ukuran availableWidthMm x sliceHeightMm
                    pdf.addImage(imgData, "PNG", marginLeft, marginTop, availableWidthMm, sliceHeightMm);

                    // next slice
                    positionY += currentSliceHeightPx;
                    pageIndex++;
                }

                pdf.save("cv.pdf");
            }

            // Fungsi untuk mengirim CV melalui email
            function sendByEmail() {
                const emailBody = "Silakan temukan CV terlampir di bawah ini.";
                const mailtoLink = `mailto:?subject=CV&body=${encodeURIComponent(emailBody)}`;
                window.location.href = mailtoLink;
            }

            // Fungsi untuk mencetak CV
            function printCV() {
                window.print();
            }

            document.getElementById('saveToDashboardBtn').addEventListener('click', savePdfToDashboard);

            async function savePdfToDashboard() {
                const btn = document.getElementById('saveToDashboardBtn');
                btn.disabled = true;
                btn.innerText = 'Saving...';

                try {
                    const content = document.getElementById('content');
                    const canvas = await html2canvas(content, { scale: 2, useCORS: true, allowTaint: true });
                    const imgData = canvas.toDataURL('image/png');

                    const pdf = new jspdf.jsPDF('p','mm','a4');
                    const pageWidth = pdf.internal.pageSize.getWidth();
                    const imgProps = pdf.getImageProperties(imgData);
                    const pdfHeight = (imgProps.height * pageWidth) / imgProps.width;
                    pdf.addImage(imgData, 'PNG', 0, 0, pageWidth, pdfHeight);

                    const pdfBlob = pdf.output('blob');

                    const formData = new FormData();
                    formData.append('cv_file', pdfBlob, 'cv.pdf');

                    // ambil template id dari data attribute (pastikan ada)
                    const templateId = btn.dataset.templateId;
                    if (!templateId) {
                        alert('Template ID tidak ditemukan, tidak bisa menyimpan. Periksa data-template-id pada tombol.');
                        btn.disabled = false;
                        btn.innerText = 'Save to Dashboard';
                        return;
                    }
                    formData.append('template_id', templateId);

                    // CSRF token (kirim juga sebagai _token supaya tidak gagal CSRF)
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    formData.append('_token', csrfToken);

                    const res = await fetch("{{ route('pelamar.curriculum_vitae.save') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                            // jangan set Content-Type: fetch otomatis set boundary untuk FormData
                        },
                        body: formData
                    });

                    const text = await res.text();
                    // coba parse JSON — kalau server return HTML (error page) kita lihat text
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (err) {
                        console.error('Server returned non-JSON response:', text);
                        alert('Server error — lihat console dan laravel.log. Response bukan JSON.');
                        btn.disabled = false;
                        btn.innerText = 'Save to Dashboard';
                        return;
                    }

                    if (data.success) {
                        // pakai SweetAlert2 jika ada, kalau tidak fallback ke alert
                        if (window.Swal) {
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1800, showConfirmButton: false });
                        } else {
                            alert('CV berhasil disimpan');
                        }
                        console.log('saved:', data.data);
                    } else {
                        const msg = (data.message || JSON.stringify(data.errors) || 'Unknown error');
                        if (window.Swal) {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                        } else {
                            alert('Gagal menyimpan CV: ' + msg);
                        }
                        console.error('server error payload:', data);
                    }
                } catch (err) {
                    console.error('JS exception', err);
                    alert('Terjadi error saat proses penyimpanan (lihat console).');
                } finally {
                    btn.disabled = false;
                    btn.innerText = 'Save to Dashboard';
                }
            }

        </script>
</body>

</html>
