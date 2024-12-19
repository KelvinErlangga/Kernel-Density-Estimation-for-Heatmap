<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Preview CV</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="icon" href="{{asset('assets/icons/logo.svg')}}" type="image/x-icon">

</head>

<body>
    <div id="content">
        @php
        $personalCurriculumVitae = $curriculumVitaeUser->personalCurriculumVitae;
        @endphp
        <!-- Left Panel -->
        <div class="left-panel">
            <!-- Profile Section -->
            @if($personalCurriculumVitae)
            <div class="profile-section">
                <!-- <div class="profile-img"></div> -->
                <div class="profile-info">
                    <p class="name">{{$personalCurriculumVitae->first_name_curriculum_vitae}} {{$personalCurriculumVitae->last_name_curriculum_vitae}}</p>
                    <!-- <p class="role">Frontend Developer</p> -->
                </div>
            </div>

            <!-- Details Section -->
            <div class="details-section">
                <p class="details-title">Details</p>
                <div class="detail-item">
                    <p class="sub-menu">Address</p>
                    <p>{{$personalCurriculumVitae->city_curriculum_vitae}}</p>
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
            @if($curriculumVitaeUser->links)
            <div class="links-section">
                <p class="links-title">Links</p>
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
            @if($curriculumVitaeUser->skills)
            <div class="skills-section">
                <p class="skills-title">Skills</p>
                @foreach($curriculumVitaeUser->skills as $skill)
                <div class="skill-item">
                    <p class="sub-menu">{{$skill->skill_name}}</p>
                    <div class="skill-divider"></div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            @if($personalCurriculumVitae->personal_summary)
            <div>
                <h1>Profile</h1>
                <p>
                    {{$personalCurriculumVitae->personal_summary}}
                </p>
            </div>
            @endif

            @if($curriculumVitaeUser->experiences)
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

            @if($curriculumVitaeUser->educations)
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

            @if($curriculumVitaeUser->languages)
            <div>
                <h1>Languages</h1>
                @foreach($curriculumVitaeUser->languages as $language)
                <p>{{$language->language_name}}</p>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div class="download-buttons">
        <button onclick="downloadAsImage('png')">Download as PNG</button>
        <button onclick="downloadAsImage('jpeg')">Download as JPEG</button>
        <button onclick="downloadAsPDF()">Download as PDF</button>
        <!-- <button><a href="{{ route('export-cv.pdf', $curriculumVitaeUser) }}">Download as PDF</a></button> -->
        <!-- <button><a href="{{ route('print-cv', $curriculumVitaeUser) }}">Print</a></button> -->
    </div>

    <script>
        async function downloadAsImage(type) {
            const content = document.getElementById('content');
            const canvas = await html2canvas(content);
            const image = canvas.toDataURL(`image/${type}`);
            const link = document.createElement('a');
            link.href = image;
            link.download = `cv.${type}`;
            link.click();
        }

        async function downloadAsPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const content = document.getElementById('content');
            const canvas = await html2canvas(content);
            const image = canvas.toDataURL('image/png');
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'px',
                format: [595, 842], // A4 dimensions
            });
            pdf.addImage(image, 'PNG', 0, 0, 595, 842);
            pdf.save('cv.pdf');
        }
    </script>
</body>

</html>