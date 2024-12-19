<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pilih Template CV | CVRE GENERATE</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="{{asset('css/style.css')}}" />
</head>

<body>
    <!-- Background -->
    <img src="{{asset('assets/images/background.png')}}" alt="Background Shape" class="background" />

    <!-- Back Button -->
    <div class="back-button">
        <a href="#">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
    </div>

    <!-- Stepper -->
    <div class="stepper">
        <div class="step">
            <div class="circle current">1</div>

            <div class="line"></div>

            <div class="circle pending">2</div>

            <div class="line"></div>

            <div class="circle pending">3</div>
        </div>
    </div>

    <div class="wrapper">
        <h2 class="choose-template-title">Silahkan pilih template</h2>

        <div class="main-container">
            @forelse($templateCoverLetter as $templateCL)
            <!-- <form method="POST" action="{{route('pelamar.cover_letter.store')}}" enctype="multipart/form-data"> -->
            @csrf
            <div class="container">
                <img src="{{Storage::url($templateCL->thumbnail_cover_letter)}}" alt="Template CL 1" />
                <input type="hidden" name="template_cover_letter_id" id="template_cover_letter_id" value="{{$templateCL->id}}">
                <button class="choose-template-btn">Pilih Template</button>
                <div class="template-name">{{$templateCL->template_cover_letter_name}}</div>
            </div>
            <!-- </form> -->
            @empty
            <div class="container">
                <div class="template-name">Tidak ada template</div>
            </div>
            @endforelse
        </div>
    </div>
</body>

</html>