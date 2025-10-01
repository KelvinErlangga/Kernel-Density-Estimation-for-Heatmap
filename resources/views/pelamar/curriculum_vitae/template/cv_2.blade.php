<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CV Template 2</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
  <link rel="stylesheet" href="{{asset('css/cv_2.css')}}" />
  <link rel="icon" href="{{asset('assets/icons/logo.svg')}}" type="image/x-icon">
  <style>
      a {
          text-decoration: none;
      }
  </style>
</head>
<body>
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
        <div class="editor-item">
            <img src="{{asset('assets/images/home.svg')}}" alt="Home Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
            <a href="{{route('home')}}" class="editor-btn">Back to Home</a>
        </div>
    </div>

    <!-- Panel Konten -->
    <div class="content-panel" id="content">
        <div id="cv-container" style="width: 595px; height: 842px; position: relative; background: white; overflow: hidden; outline: 1px black solid; outline-offset: -1px">

          @php
            $personalCurriculumVitae = $curriculumVitaeUser->personalCurriculumVitae;
          @endphp

          {{-- Nama & Job Title --}}
          @if($personalCurriculumVitae)
            <div style="left: 40px; top: 40px; position: absolute; font-size: 36px; font-family: Roboto; font-weight: 700; text-transform: uppercase; line-height: 45px;">
              {{ $personalCurriculumVitae->first_name_curriculum_vitae }} {{ $personalCurriculumVitae->last_name_curriculum_vitae }}
            </div>
            <div style="left: 41px; top: 89px; position: absolute; font-size: 10px; font-family: Roboto; font-weight: 400; color: rgba(0,0,0,0.7); text-transform: capitalize;">
              {{ $personalCurriculumVitae->job_title_curriculum_vitae ?? '' }}
            </div>
          @endif

          {{-- Garis Horizontal --}}
          <div style="width: 515px; height: 0px; left: 41px; top: 125px; position: absolute; border-top: 1px solid rgba(0, 0, 0, 0.15);"></div>
          {{-- Garis Vertikal --}}
          <div style="width: 677px; height: 0px; left: 231px; top: 125px; position: absolute; transform: rotate(90deg); transform-origin: top left; border-top: 1px solid rgba(0, 0, 0, 0.15);"></div>

          {{-- Profile Section --}}
          @if($personalCurriculumVitae && $personalCurriculumVitae->personal_summary)
            <div style="left: 257px; top: 149px; position: absolute; font-size: 15px; font-family: Roboto; font-weight: 700; text-decoration: underline; text-transform: uppercase;">
              Profile
            </div>
            <div style="width: 299px; left: 257px; top: 182px; position: absolute; font-size: 9px; font-family: Roboto; color: rgba(0,0,0,0.70); line-height: 13px;">
              {{ $personalCurriculumVitae->personal_summary }}
            </div>
          @endif

          {{-- Experience Section --}}
          @if($curriculumVitaeUser->experiences->isNotEmpty())
            <div style="left: 257px; top: 245px; position: absolute; font-size: 15px; font-weight: 700; text-decoration: underline; text-transform: uppercase;">
              Experience
            </div>
            @php $top = 277; @endphp
            @foreach($curriculumVitaeUser->experiences as $experience)
              <div style="left: 257px; top: {{ $top }}px; position: absolute; font-size: 10px; font-weight: 700;">
                {{ $experience->company_experience }}
              </div>
              <div style="left: 257px; top: {{ $top+15 }}px; position: absolute; font-size: 9px;">
                {{ $experience->position_experience }}
              </div>
              <div style="left: 257px; top: {{ $top+31 }}px; position: absolute; font-size: 9px; color: rgba(0,0,0,0.70);">
                @if($experience->end_date)
                  {{ date('M Y', strtotime($experience->start_date)) }} - {{ date('M Y', strtotime($experience->end_date)) }}
                @else
                  {{ date('M Y', strtotime($experience->start_date)) }} - Present
                @endif
              </div>
              @if($experience->description_experience)
                <div style="width: 299px; left: 266px; top: {{ $top+50 }}px; position: absolute; font-size: 9px; color: rgba(0,0,0,0.70); line-height: 13px;">
                  {!! $experience->description_experience !!}
                </div>
              @endif
              @php $top += 100; @endphp
            @endforeach
          @endif

          {{-- Education Section --}}
          @if($curriculumVitaeUser->educations->isNotEmpty())
            <div style="left: 257px; top: 702px; position: absolute; font-size: 15px; font-weight: 700; text-decoration: underline; text-transform: uppercase;">
              Education
            </div>
            @php $edutop = 735; @endphp
            @foreach($curriculumVitaeUser->educations as $education)
              <div style="left: 257px; top: {{ $edutop }}px; position: absolute; font-size: 10px; font-weight: 700;">
                {{ $education->school_name }}
              </div>
              <div style="left: 257px; top: {{ $edutop+20 }}px; position: absolute; font-size: 9px; color: rgba(0,0,0,0.70);">
                {{ $education->field_of_study }},
                @if($education->end_date)
                  {{ date('M Y', strtotime($education->start_date)) }} - {{ date('M Y', strtotime($education->end_date)) }}
                @else
                  {{ date('M Y', strtotime($education->start_date)) }} - Present
                @endif
              </div>
              @php $edutop += 40; @endphp
            @endforeach
          @endif

          {{-- Sidebar Kiri --}}
          @if($personalCurriculumVitae)
            <div style="left: 41px; top: 149px; position: absolute; font-size: 15px; font-weight: 700; text-decoration: underline; text-transform: uppercase;">
              Details
            </div>
            <div style="left: 41px; top: 182px; position: absolute; font-size: 10px; font-weight: 700;">Address</div>
            <div style="left: 41px; top: 198px; position: absolute; font-size: 9px; color: rgba(0,0,0,0.70);">
              {{ $personalCurriculumVitae->city_curriculum_vitae }}, {{ $personalCurriculumVitae->address_curriculum_vitae }}
            </div>
            <div style="left: 41px; top: 221px; position: absolute; font-size: 10px; font-weight: 700;">Phone</div>
            <div style="left: 41px; top: 237px; position: absolute; font-size: 10px; color: rgba(0,0,0,0.70);">
              {{ $personalCurriculumVitae->phone_curriculum_vitae }}
            </div>
            <div style="left: 41px; top: 261px; position: absolute; font-size: 10px; font-weight: 700;">Email</div>
            <div style="left: 41px; top: 277px; position: absolute; font-size: 10px; color: rgba(0,0,0,0.70);">
              {{ $personalCurriculumVitae->email_curriculum_vitae }}
            </div>
          @endif

          {{-- Skills Section --}}
          @if($curriculumVitaeUser->skills->isNotEmpty())
            <div style="left: 40px; top: 444px; position: absolute; font-size: 15px; font-weight: 700; text-decoration: underline; text-transform: uppercase;">
              Skills
            </div>
            @php $skilltop = 477; @endphp
            @foreach($curriculumVitaeUser->skills as $skill)
              <div style="left: 40px; top: {{ $skilltop }}px; position: absolute; font-size: 9px; color: rgba(0,0,0,0.70);">
                {{ $skill->skill_name }} - {{ $skill->category_level }}
              </div>
              @php $skilltop += 37; @endphp
            @endforeach
          @endif

          {{-- Languages Section --}}
          @if($curriculumVitaeUser->languages->isNotEmpty())
            <div style="left: 40px; top: 712px; position: absolute; font-size: 15px; font-weight: 700; text-decoration: underline; text-transform: uppercase;">
              Languages
            </div>
            @php $langtop = 745; @endphp
            @foreach($curriculumVitaeUser->languages as $language)
              <div style="left: 40px; top: {{ $langtop }}px; position: absolute; font-size: 9px; color: rgba(0,0,0,0.70);">
                {{ $language->language_name }}
              </div>
              @php $langtop += 37; @endphp
            @endforeach
          @endif

        </div>
    </div>
</div>

<script>
    // === fungsi editor sama persis seperti cv_1 ===

    function changeFont(selectElement) {
        const content = document.getElementById("content");
        const selectedFont = selectElement.value;
        content.style.fontFamily = selectedFont;
    }

    function changeBackgroundColor(color) {
        const cvContainer = document.getElementById("cv-container");
        cvContainer.style.backgroundColor = color;
    }

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
        const canvas = await html2canvas(content, { scale: 2, useCORS: true });
        const imgData = canvas.toDataURL("image/png");
        const pdf = new jspdf.jsPDF("p", "mm", "a4");
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();
        const imgWidth = pageWidth;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        let heightLeft = imgHeight;
        let position = 0;

        while (heightLeft > 0) {
            pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
            position -= pageHeight;
            if (heightLeft > 0) pdf.addPage();
        }

        pdf.save("cv.pdf");
    }

    function printCV() {
        window.print();
    }
</script>
</body>
</html>
