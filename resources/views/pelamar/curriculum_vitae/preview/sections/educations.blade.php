@php
$educations = $cv->educations;
$s = $style['educations'] ?? [];
// AMBIL STYLE DARI EXPERIENCES SEBAGAI PATOKAN
$s_exp = $style['experiences'] ?? [];
@endphp

@if($educations && $educations->count() > 0)
<div style="{{ inlineStyle($style['page'] ?? []) }}">
  <div style="{{ inlineStyle($s['container'] ?? []) }}">
    <h3 style="{{ inlineStyle($s['h3'] ?? []) }}">RIWAYAT PENDIDIKAN</h3>

    @foreach($educations as $index => $edu)
      @php
        $eduId = $edu->id ?? "edu-{$index}";
      @endphp

      {{-- Div item utama. Menggunakan style 'div' dari experiences --}}
      <div style="{{ inlineStyle(array_merge($s_exp['div'] ?? [], ['color' => $s['div']['color'] ?? '#000'])) }}">

        {{-- Nama Institusi (Padding disamakan) --}}
        <strong>
          <span contenteditable="true"
                class="inline-edit"
                data-cv="{{ $cv->id }}"
                data-section="educations"
                data-id="{{ $eduId }}"
                data-field="school_name"
                style="{{ inlineStyle(array_merge($s['school_name'] ?? [], [
                    'border' => '1px dashed #ccc',
                    'padding' => '4px 6px', // Samakan dengan experiences
                    'border-radius' => '4px',
                ])) }}">
            {{ $edu->school_name }}
          </span>
        </strong>

        {{-- BARIS META. Menggunakan style 'meta' dari experiences (overflow: hidden) --}}
        <div style="{{ inlineStyle(array_merge($s_exp['meta'] ?? [], [
            'overflow' => 'hidden', // (dari JSON experiences.meta)
            'margin-bottom' => '4px', // (dari JSON experiences.meta)
            'width' => '100%'
        ])) }}">

          {{-- Jurusan (kiri) + TAMBAHAN GPA --}}
          <em style="float: left;"> {{-- [MODIFIKASI]: float:left dipindah ke <em> --}}
            <span contenteditable="true"
                  class="inline-edit"
                  data-cv="{{ $cv->id }}"
                  data-section="educations"
                  data-id="{{ $eduId }}"
                  data-field="field_of_study"
                  data-placeholder="Program studi / jurusan"
                  style="{{ inlineStyle(array_merge($s_exp['position'] ?? [], [
                      'border' => '1px dashed #ccc',
                      'padding' => '2px 4px',
                      'border-radius' => '4px',
                      'float' => 'none', // [MODIFIKASI]: Hapus float dari sini
                      'font-style' => 'italic',
                      'min-width' => 'auto'  {{--  <-- PERBAIKAN DI SINI --}}
                  ])) }}">
                {{ $edu->field_of_study }}
            </span>

            {{-- === BLOK TAMBAHAN UNTUK GPA === --}}
            @if($edu->gpa)
                <span style="font-style: italic; margin-left: 0px;"> | </span>
                <span contenteditable="true"
                      class="inline-edit"
                      data-cv="{{ $cv->id }}"
                      data-section="educations"
                      data-id="{{ $eduId }}"
                      data-field="gpa"
                      data-placeholder="GPA"
                      style="{{ inlineStyle(array_merge($s_exp['position'] ?? [], [
                          'border' => '1px dashed #ccc',
                          'padding' => '2px 4px',
                          'border-radius' => '4px',
                          'margin-right' => '0px',
                          'float' => 'none', // Tidak perlu float
                          'font-style' => 'italic',
                          'min-width' => 'auto'  {{--  <-- PERBAIKAN DI SINI --}}
                      ])) }}">
                    {{ $edu->gpa }}
                </span>
                <span style="font-style: italic;"></span>
            @endif
            {{-- === AKHIR BLOK GPA === --}}

          </em>

          {{-- Grup kanan (Tanggal + Kota) --}}

          {{-- Tanggal (kanan) --}}
          <span style="{{ inlineStyle(array_merge($s_exp['date'] ?? [], [
                'border' => '1px dashed #ccc',
                'padding' => '2px 4px',
                'border-radius' => '4px',
                'margin-right' => '4px',
                'float' => 'right',
                'font-size' => '12px',
                'color' => $s_exp['date']['color'] ?? '#555'
            ])) }}">
            <span contenteditable="true"
                  class="inline-edit"
                  data-cv="{{ $cv->id }}"
                  data-section="educations"
                  data-id="{{ $eduId }}"
                  data-field="start_date">
                {{ $edu->start_date ? \Carbon\Carbon::parse($edu->start_date)->format('d-m-Y') : '...' }}
            </span>
            -
            <span contenteditable="true"
                  class="inline-edit"
                  data-cv="{{ $cv->id }}"
                  data-section="educations"
                  data-id="{{ $eduId }}"
                  data-field="end_date">
                  {{-- [MODIFIKASI]: Gunakan 'is_current' --}}
                  @if($edu->is_current)
                      Sekarang
                  @else
                      {{ $edu->end_date ? \Carbon\Carbon::parse($edu->end_date)->format('d-m-Y') : '...' }}
                  @endif
            </span>
          </span>

          {{-- Kota (kanan) --}}
          <span contenteditable="true"
                class="inline-edit"
                data-cv="{{ $cv->id }}"
                data-section="educations"
                data-id="{{ $eduId }}"
                data-field="city_education"
                data-placeholder="Kota"
                style="{{ inlineStyle(array_merge($s_exp['date'] ?? [], [
                    'border' => '1px dashed #ccc',
                    'padding' => '2px 4px',
                    'border-radius' => '4px',
                    'float' => 'right',
                    'font-size' => '12px',
                    'color' => $s_exp['date']['color'] ?? '#555'
                ])) }}">
            {{ $edu->city_education }}
          </span>

        </div>

        {{-- [MODIFIKASI]: Deskripsi (opsional) --}}
        @php
            // Ganti nama kolom ke 'description'
            $rawDesc = (string)($edu->description ?? '');
            $plain = trim(strip_tags($rawDesc));
        @endphp

        @if($plain !== '')
            <div contenteditable="true"
                class="inline-edit edu-desc"
                data-cv="{{ $cv->id }}"
                data-section="educations"
                data-id="{{ $eduId }}"
                data-field="description"
                data-placeholder="Deskripsi pendidikan"
                style="{{ inlineStyle(array_merge($s_exp['li'] ?? [], [
                    'border'        => '1px dashed #ccc',
                    'padding'       => '3px 6px',
                    'border-radius' => '4px',
                    'margin-top'    => '2px',   // ✅ sebelumnya 6px
                    'line-height'   => '1.35',  // ✅ rapikan tinggi baris
                ])) }}">
                {!! $rawDesc !!}
            </div>
        @endif

      </div>
    @endforeach
  </div>
</div>

<style>
  /* ✅ Hilangkan margin bawaan dari HTML editor supaya jarak tidak “jauh” */
  .edu-desc p { margin: 0 !important; }
  .edu-desc ul { margin: 0 !important; padding-left: 18px; }
  .edu-desc li { margin: 0 !important; }
</style>

@endif
