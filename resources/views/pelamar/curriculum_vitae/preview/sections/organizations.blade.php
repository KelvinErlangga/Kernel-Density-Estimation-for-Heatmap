@php
$organizations = $cv->organizations;
$s = $style['organizations'] ?? [];
$s_exp = $style['experiences'] ?? [];
@endphp

@if($organizations && $organizations->count())
<div style="{{ inlineStyle($style['page'] ?? []) }}">
  <div style="{{ inlineStyle($s['container'] ?? []) }}">
    <h3 style="{{ inlineStyle($s['h3'] ?? []) }}">PENGALAMAN ORGANISASI</h3>

    @foreach($organizations as $idx => $org)
      @php $orgId = $org->id ?? "org-{$idx}"; @endphp

      <div style="{{ inlineStyle(array_merge($s_exp['div'] ?? [], ['color' => $s['div']['color'] ?? '#000'])) }}">

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

        <div style="{{ inlineStyle(array_merge($s_exp['meta'] ?? [], [
            'overflow' => 'hidden',
            'margin-bottom' => '2px',  // ✅ rapatkan (sebelumnya 4px)
            'width' => '100%'
        ])) }}">

          <em style="float:left;">
            <span contenteditable="true"
                  class="inline-edit"
                  data-cv="{{ $cv->id }}"
                  data-section="organizations"
                  data-id="{{ $orgId }}"
                  data-field="position_organization"
                  data-placeholder="Jabatan"
                  style="{{ inlineStyle(array_merge($s_exp['position'] ?? [], [
                    'border'=>'1px dashed #ccc',
                    'padding'=>'2px 4px',
                    'border-radius'=>'4px',
                    'float' => 'none',
                    'font-style' => 'italic',
                    'min-width' => 'auto'
                  ])) }}">
              {{ $org->position_organization }}
            </span>
          </em>

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

        @php
          $rawDesc = (string)($org->description_organization ?? '');
          $rawDesc = preg_replace('/<p>\s*<br\s*\/?>\s*<\/p>/i', '', $rawDesc); // ✅ buang paragraf kosong
          $plain   = trim(strip_tags($rawDesc));
        @endphp

        @if($plain !== '')
          <div contenteditable="true"
               class="inline-edit org-desc"
               data-cv="{{ $cv->id }}"
               data-section="organizations"
               data-id="{{ $orgId }}"
               data-field="description_organization"
               data-placeholder="Uraian singkat kegiatan & pencapaian"
               style="{{ inlineStyle(array_merge($s_exp['li'] ?? [], [
                  'border'=>'1px dashed #ccc',
                  'padding'=>'3px 6px',
                  'border-radius'=>'4px',
                  'margin-top' => '2px',   // ✅ rapatkan (sebelumnya 4px)
                  'line-height' => '1.35',
               ])) }}">
            {!! $rawDesc !!}
          </div>
        @endif

      </div>
    @endforeach
  </div>
</div>

<style>
  /* ✅ hilangkan margin default dari HTML editor agar tidak “jauh” */
  .org-desc p { margin: 0 !important; }
  .org-desc ul { margin: 0 !important; padding-left: 18px; }
  .org-desc li { margin: 0 !important; }
</style>
@endif
