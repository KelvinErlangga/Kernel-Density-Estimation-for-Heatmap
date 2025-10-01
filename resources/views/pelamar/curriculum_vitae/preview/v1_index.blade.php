@extends('layouts.cv')

@section('content')
<div class="main-container">
    <!-- Panel Kiri (Editor Panel) -->
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
            <input type="color" onchange="changeBackgroundColor(this.value)" class="color-picker" />
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
            <a href="{{route('pelamar.curriculum_vitae.profile.index', $cv->id)}}" class="editor-btn">Edit Data</a>
        </div>

        <div class="editor-item">
            <img src="{{asset('assets/images/download.svg')}}" alt="Save Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
            <button id="saveToDashboardBtn" data-template-id="{{ $cv->template_curriculum_vitae_id ?? 1 }}">Save to Dashboard</button>
        </div>

        <div class="editor-item">
            <img src="{{asset('assets/images/home.svg')}}" alt="Home Icon" style="width: 24px; height: 24px; margin-right: 8px;" />
            <a href="{{route('home')}}" class="editor-btn">Back to Home</a>
        </div>
    </div>

    <!-- Panel Kanan (Preview CV) -->
    <div class="container">
        <div id="content">
            @foreach($layout as $section)
                <div class="section {{ $section['key'] }}">
                    @includeIf('pelamar.curriculum_vitae.preview.sections.' . $section['key'], [
                        'cv' => $cv
                    ])
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
