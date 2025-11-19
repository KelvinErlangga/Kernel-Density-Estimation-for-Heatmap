@php
$organizations = $cv->organizations;
$s = $style['organizations'] ?? [];
// AMBIL STYLE DARI EXPERIENCES SEBAGAI PATOKAN
$s_exp = $style['experiences'] ?? [];
@endphp

@if($organizations && $organizations->count())
<div style="{{ inlineStyle($style['page'] ?? []) }}">
  <div style="{{ inlineStyle($s['container'] ?? []) }}">
    <h3 style="{{ inlineStyle($s['h3'] ?? []) }}">PENGALAMAN ORGANISASI</h3>

    @foreach($organizations as $idx => $org)
      @php $orgId = $org->id ?? "org-{$idx}"; @endphp

      {{-- [PERUBAHAN 1]: Div item utama. Menggunakan style 'div' dari experiences --}}
      <div style="{{ inlineStyle(array_merge($s_exp['div'] ?? [], ['color' => $s['div']['color'] ?? '#000'])) }}">

        {{-- NAMA ORGANISASI (Padding sudah OK) --}}
        <strong>
            <span contenteditable="true"
                  class="inline-edit"
                  data-cv="{{ $cv->id }}"
                  data-section="organizations"
                  data-id="{{ $orgId }}"
                  data-field="organization_name"
                  style="{{ inlineStyle(array_merge($s['org'] ?? [], [
                      'border'=>'1px dashed #ccc',
                      'padding'=>'4px 6px',
                      'border-radius'=>'4px',
                  ])) }}">
                {{ $org->organization_name }}
            </span>
        </strong>

        {{-- [PERUBAHAN 2]: BARIS META. Menggunakan style 'meta' dari experiences --}}
        <div style="{{ inlineStyle(array_merge($s_exp['meta'] ?? [], [
            'overflow' => 'hidden',
            'margin-bottom' => '4px',
            'width' => '100%'
        ])) }}">

          {{-- Posisi (kiri). Menggunakan style 'position' from experiences --}}
          <em>
            <span contenteditable="true" class="inline-edit"
                  data-cv="{{ $cv->id }}" data-section="organizations"
                  data-id="{{ $orgId }}" data-field="position_organization"
                  data-placeholder="Jabatan"
                  style="{{ inlineStyle(array_merge($s_exp['position'] ?? [], [
                    'border'=>'1px dashed #ccc',
                    'padding'=>'2px 4px',
                    'border-radius'=>'4px',
                    'margin-right' => '4px',
                    'float' => 'left', // (dari JSON experiences.position)
                    'font-style' => 'italic' // (dari JSON experiences.position)
                  ])) }}">
              {{ $org->position_organization }}
            </span>
          </em>

          {{-- [PERUBAHAN 3]: Tanggal (kanan). Menggunakan style 'date' from experiences --}}
          <span style="{{ inlineStyle(array_merge($s_exp['date'] ?? [], [
                    'border' => '1px dashed #ccc',
                    'padding' => '2px 4px',
                    'border-radius' => '4px',
                    'float' => 'right',
                    'font-size' => '12px',
                    'color' => $s_exp['date']['color'] ?? '#555'
                ])) }}">
            <span contenteditable="true"
                  class="inline-edit"
                  data-cv="{{ $cv->id }}"
                  data-section="organizations"
                  data-id="{{ $orgId }}"
                  data-field="start_date">
                {{ $org->start_date ? \Carbon\Carbon::parse($org->start_date)->format('d-m-Y') : '...' }}
            </span>
            -
            <span contenteditable="true"
                  class="inline-edit"
                  data-cv="{{ $cv->id }}"
                  data-section="organizations"
                  data-id="{{ $orgId }}"
                  data-field="end_date">
                {{ $org->end_date ? \Carbon\Carbon::parse($org->end_date)->format('d-m-Y') : 'Sekarang' }}
            </span>
          </span>
        </div>

        {{-- DESKRIPSI (opsional) --}}
        @php
        $rawDesc = (string)($org->description_organization ?? '');

        $plain = trim(strip_tags($rawDesc));
        @endphp

        @if($plain !== '')
        <div contenteditable="true"
            class="inline-edit"
            data-cv="{{ $cv->id }}"
            data-section="organizations"
            data-id="{{ $orgId }}"
            data-field="description_organization"
            data-placeholder="Uraian singkat kegiatan & pencapaian"
            style="{{ inlineStyle(array_merge($s_exp['li'] ?? [], [
                'border'=>'1px dashed #ccc','padding'=>'4px 6px','border-radius'=>'4px', 'margin-top' => '4px'
            ])) }}">
            {!! $rawDesc !!}
        </div>
        @endif
      </div> {{-- Akhir <div> item --}}
    @endforeach
  </div>
</div>
@endif
