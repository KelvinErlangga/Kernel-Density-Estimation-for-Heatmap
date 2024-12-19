<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Preview CV | CVRE GENERATE</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="{{asset('css/editor.css')}}" />
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
            <div class="editor-item">
                <img src="{{asset('assets/images/font.svg')}}" alt="Font Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
                <button class="editor-btn" onclick="changeFont()">Font</button>
            </div>
            <div class="editor-item">
                <img src="{{asset('assets/images/color.svg')}}" alt="Color Icon" />
                <input type="color" onchange="changeBackgroundColor(this)" />
            </div>
            <div class="editor-item">
                <img src="{{asset('assets/images/download.svg')}}" alt="Download Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
                <button class="editor-btn" onclick="toggleDownloadOptions()">Download</button>
                <div class="download-options" id="download-options" style="display: none;">
                    <button class="editor-btn" onclick="downloadAsImage('png')">PNG</button>
                    <button class="editor-btn" onclick="downloadAsImage('jpeg')">JPEG</button>
                    <button class="editor-btn" onclick="downloadAsPDF()">PDF</button>
                </div>
            </div>
            <div class="editor-item">
                <img src="{{asset('assets/images/email.svg')}}" alt="Email Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
                <button class="editor-btn" onclick="sendByEmail()">Email</button>
            </div>
            <div class="editor-item">
                <img src="{{asset('assets/images/print.svg')}}" alt="Print Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
                <button class="editor-btn" onclick="printCV()">Print</button>
            </div>

            <div class="editor-item">
                <img src="{{asset('assets/images/edit.svg')}}" alt="Edit Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
                <a href="{{route('pelamar.curriculum_vitae.profile.index', $curriculumVitaeUser->id)}}" class="editor-btn">Edit Data</a>
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
                $personalCurriculumVitae = $curriculumVitaeUser->personalCurriculumVitae;
                @endphp
                <!-- Left Panel -->
                <div class="left-panel">
                    <!-- Profile Section -->
                    @if($personalCurriculumVitae)
                    <a href="{{route('pelamar.curriculum_vitae.profile.index', $curriculumVitaeUser->id)}}">

                        <div class="profile-section">
                            @if($personalCurriculumVitae->avatar_curriculum_vitae)
                            <div class="profile-img">
                                <img src="{{Storage::url($personalCurriculumVitae->avatar_curriculum_vitae)}}" alt="foto">
                            </div>
                            @endif
                            <div class="profile-info">
                                <p class="name">{{$personalCurriculumVitae->first_name_curriculum_vitae}} {{$personalCurriculumVitae->last_name_curriculum_vitae}}</p>
                                <!-- <p class="role">Frontend Developer</p> -->
                            </div>
                        </div>
                    </a>
                    <!-- Details Section -->
                    <div class="details-section">
                        <p class="details-title">Details</p>
                        <div class="detail-item">
                            <p class="sub-menu">Address</p>
                            <p>{{$personalCurriculumVitae->city_curriculum_vitae}}, {{$personalCurriculumVitae->address_curriculum_vitae}}</p>
                        </div>
                        <div class="detail-item">
                            <p class="sub-menu">Phone</p>
                            <p>{{$personalCurriculumVitae->phone_curriculum_vitae}}</p>
                        </div>
                        <div class="detail-item">
                            <p class="sub-menu">Email</p>
                            <p>{{$personalCurriculumVitae->email_curriculum_vitae}}</p>
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
                                <p class="sub-menu">{{$skill->skill_name}}</p>
                                <div class="skill-divider"></div>
                            </div>
                            @endforeach
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Right Panel -->
                <div class="right-panel">
                    @if($personalCurriculumVitae->personal_summary)
                    <div>
                        <a href="{{route('pelamar.curriculum_vitae.profile.index', $curriculumVitaeUser->id)}}">
                            <h1 style="color: black;">Profile</h1>
                            <p style="color: black;">
                                {{$personalCurriculumVitae->personal_summary}}
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
            // Mengubah font
            function changeFont() {
                const content = document.getElementById("content");
                const currentFont = window.getComputedStyle(content).fontFamily;
                const newFont =
                    currentFont === "Arial, sans-serif" ? "Times New Roman, serif" : "Arial, sans-serif";
                content.style.fontFamily = newFont;
            }

            // Mengubah warna latar belakang pada Left Panel
            function changeBackgroundColor(input) {
                const leftPanel = document.querySelector(".left-panel");
                leftPanel.style.backgroundColor = input.value;
            }


            // Menampilkan atau menyembunyikan opsi download
            function toggleDownloadOptions() {
                const options = document.getElementById("download-options");
                options.style.display = options.style.display === "none" ? "block" : "none";
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
                const {
                    jsPDF
                } = window.jspdf;
                const content = document.getElementById("content");
                const canvas = await html2canvas(content);
                const image = canvas.toDataURL("image/png");
                const pdf = new jsPDF({
                    orientation: "portrait",
                    unit: "px",
                    format: [595, 842], // A4 dimensions
                });
                pdf.addImage(image, "PNG", 0, 0, 595, 842);
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
        </script>
</body>

</html>