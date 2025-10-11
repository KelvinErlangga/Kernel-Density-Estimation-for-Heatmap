@php
$organizations = $cv->organizations;
$s = $style['organizations'] ?? [];
@endphp

@if($organizations && $organizations->count())
<div style="{{ inlineStyle($style['page'] ?? []) }}">
  <div style="{{ inlineStyle($s['container'] ?? []) }}">
    <h3 style="{{ inlineStyle($s['h3'] ?? []) }}">PENGALAMAN ORGANISASI</h3>

    @foreach($organizations as $idx => $org)
      @php $orgId = $org->id ?? "org-{$idx}"; @endphp

      <div style="{{ inlineStyle(array_merge($s['item'] ?? [], ['margin-bottom'=>'12px'])) }}">
        {{-- NAMA ORGANISASI --}}
        <strong>
          <span contenteditable="true" class="inline-edit"
                data-cv="{{ $cv->id }}" data-section="organizations"
                data-id="{{ $orgId }}" data-field="organization_name"
                style="{{ inlineStyle(array_merge($s['org'] ?? [], [
                  'border'=>'1px dashed #ccc','padding'=>'2px 6px','border-radius'=>'4px','line-height'=>'1.1'
                ])) }}">
            {{ $org->organization_name }}
          </span>
        </strong>

        {{-- BARIS META: Posisi (kiri) | Tanggal (kanan) --}}
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; width:100%; margin-top:2px;">
          <em>
            <span contenteditable="true" class="inline-edit"
                  data-cv="{{ $cv->id }}" data-section="organizations"
                  data-id="{{ $orgId }}" data-field="position_organization"
                  data-placeholder="Jabatan"
                  style="{{ inlineStyle(array_merge($s['position'] ?? [], [
                    'border'=>'1px dashed #ccc','padding'=>'2px 6px','border-radius'=>'4px','line-height'=>'1.1'
                  ])) }}">
              {{ $org->position_organization }}
            </span>
          </em>

          <span class="pill" style="margin-left:auto; display:inline-flex; align-items:center; gap:6px;">
            <span contenteditable="true" class="inline-edit"
                  data-cv="{{ $cv->id }}" data-section="organizations"
                  data-id="{{ $orgId }}" data-field="start_date"
                  style="{{ inlineStyle(array_merge($s['date'] ?? [], [
                    'border'=>'1px dashed #ccc','padding'=>'2px 6px','border-radius'=>'4px'
                  ])) }}">
              {{ $org->start_date }}
            </span>
            <span> - </span>
            <span contenteditable="true" class="inline-edit"
                  data-cv="{{ $cv->id }}" data-section="organizations"
                  data-id="{{ $orgId }}" data-field="end_date"
                  style="{{ inlineStyle(array_merge($s['date'] ?? [], [
                    'border'=>'1px dashed #ccc','padding'=>'2px 6px','border-radius'=>'4px'
                  ])) }}">
              {{ $org->end_date ?? 'Sekarang' }}
            </span>
          </span>
        </div>

        {{-- DESKRIPSI (opsional) --}}
        @php
          $rawDesc = (string)($org->description ?? '');
          // anggap kosong jika setelah strip_tags & trim masih kosong,
          // juga buang &nbsp; dan <br> “kosong”
          $plain = trim(preg_replace(['/&nbsp;/i','/<br\s*\/?>/i'], [' ',' '], strip_tags($rawDesc)));
        @endphp
        @if($plain !== '')
          <div contenteditable="true" class="inline-edit"
               data-cv="{{ $cv->id }}" data-section="organizations"
               data-id="{{ $orgId }}" data-field="description"
               data-placeholder="Uraian singkat kegiatan & pencapaian"
               style="{{ inlineStyle(array_merge($s['description'] ?? [], [
                 'border'=>'1px dashed #ccc','padding'=>'4px 6px','border-radius'=>'4px','margin-top'=>'6px'
               ])) }}">
            {!! nl2br(e($rawDesc)) !!}
          </div>
        @endif
      </div>
    @endforeach
  </div>
</div>
@endif
