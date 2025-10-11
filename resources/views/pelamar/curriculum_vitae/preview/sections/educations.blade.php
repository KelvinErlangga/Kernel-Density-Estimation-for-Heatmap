@php
$educations = $cv->educations;
$s = $style['educations'] ?? [];
@endphp

@if($educations && $educations->count() > 0)
<div style="{{ inlineStyle($style['page'] ?? []) }}">
  <div style="{{ inlineStyle($s['container'] ?? []) }}">
    <h3 style="{{ inlineStyle($s['h3'] ?? []) }}">RIWAYAT PENDIDIKAN</h3>

    @foreach($educations as $index => $edu)
      @php
        $descriptions = is_array($edu->description_education)
            ? array_filter(array_map('trim', $edu->description_education))
            : array_filter(array_map('trim', explode("\n", (string)($edu->description_education ?? ''))));
        $eduId = $edu->id ?? "edu-{$index}";
      @endphp

      <div style="{{ inlineStyle($s['div'] ?? []) }}">
        {{-- Nama Institusi --}}
        <strong>
          <span contenteditable="true"
                class="inline-edit"
                data-cv="{{ $cv->id }}"
                data-section="educations"
                data-id="{{ $eduId }}"
                data-field="school_name"
                style="{{ inlineStyle(array_merge($s['school_name'] ?? [], [
                  'border' => '1px dashed #ccc',
                  'padding' => '4px 6px',
                  'border-radius' => '4px',
                ])) }}">
            {{ $edu->school_name }}
          </span>
        </strong>

        {{-- BARIS META: Jurusan (kiri) | Kota & Tanggal (kanan) --}}
        <div style="{{ inlineStyle(array_merge($s['meta'] ?? [], [
            'display' => 'flex',
            'align-items' => 'center',
            'gap' => '8px',
            'flex-wrap' => 'wrap',
            'width' => '100%',
            'margin-bottom' => '14px'    // JARAK ke item berikutnya -> tidak “nempel” ke school name di bawah
        ])) }}">
        {{-- Jurusan (kiri) --}}
        <em>
            <span contenteditable="true"
                class="inline-edit"
                data-cv="{{ $cv->id }}"
                data-section="educations"
                data-id="{{ $eduId }}"
                data-field="field_of_study"
                data-placeholder="Program studi / jurusan"
                style="{{ inlineStyle(array_merge($s['field'] ?? [], [
                    'border' => '1px dashed #ccc',
                    'padding' => '2px 4px',
                    'border-radius' => '4px',
                ])) }}">
            {{ $edu->field_of_study }}
            </span>
        </em>

        {{-- Grup kanan: Kota + Tanggal (dipaksa ke kanan) --}}
        <span style="margin-left:auto; display:inline-flex; align-items:center; gap:8px;">
            <span contenteditable="true"
                class="inline-edit"
                data-cv="{{ $cv->id }}"
                data-section="educations"
                data-id="{{ $eduId }}"
                data-field="city_education"
                data-placeholder="Kota"
                style="{{ inlineStyle(array_merge($s['date'] ?? [], [
                    'border' => '1px dashed #ccc',
                    'padding' => '2px 4px',
                    'border-radius' => '4px',
                ])) }}">
            {{ $edu->city_education }}
            </span>

            <span style="{{ inlineStyle(array_merge($s['date'] ?? [], [
                'border' => '1px dashed #ccc',
                'padding' => '2px 4px',
                'border-radius' => '4px',
            ])) }}">
            <span contenteditable="true"
                    class="inline-edit"
                    data-cv="{{ $cv->id }}"
                    data-section="educations"
                    data-id="{{ $eduId }}"
                    data-field="start_date">
                {{ $edu->start_date }}
            </span>
            -
            <span contenteditable="true"
                    class="inline-edit"
                    data-cv="{{ $cv->id }}"
                    data-section="educations"
                    data-id="{{ $eduId }}"
                    data-field="end_date">
                {{ $edu->end_date ?? 'Sekarang' }}
            </span>
        </span>
        </div>

        {{-- Deskripsi (opsional) --}}
        @if(!empty($descriptions))
          <ul style="{{ inlineStyle(array_merge($s['ul'] ?? [], [
              'border' => '1px dashed #ccc',
              'padding' => '4px 6px',
              'border-radius' => '4px',
              'margin-top' => '6px'
          ])) }}">
            @foreach($descriptions as $desc)
              <li contenteditable="true"
                  class="inline-edit"
                  data-cv="{{ $cv->id }}"
                  data-section="educations"
                  data-id="{{ $eduId }}"
                  data-field="description_education"
                  style="{{ inlineStyle(array_merge($s['li'] ?? [], [
                      'border' => '1px dashed #ccc',
                      'padding' => '2px 4px',
                      'border-radius' => '4px',
                  ])) }}">
                {{ $desc }}
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    @endforeach
  </div>
</div>
@endif

<style>
.inline-edit:empty:before { content: attr(data-placeholder); color:#aaa; pointer-events:none; }
.inline-edit { min-width:50px; display:inline-block; }
</style>
