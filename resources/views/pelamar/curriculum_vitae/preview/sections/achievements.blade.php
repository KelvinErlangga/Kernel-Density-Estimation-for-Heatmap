@php
$achievements = $cv->achievements;
$s = $style['achievements'] ?? [];
// AMBIL STYLE DARI EXPERIENCES SEBAGAI PATOKAN
$s_exp = $style['experiences'] ?? [];
@endphp

@if($achievements && $achievements->count() > 0)
<div style="{{ inlineStyle($style['page'] ?? []) }}">
    <div style="{{ inlineStyle($s['container'] ?? []) }}">
        <h3 style="{{ inlineStyle($s['h3'] ?? []) }}">PRESTASI</h3>

        @foreach($achievements as $ach)
            @php $achId = $ach->id; @endphp

            {{-- Div item utama (dari style experiences) --}}
            <div style="{{ inlineStyle(array_merge($s_exp['div'] ?? [], ['color' => $s['div']['color'] ?? '#000'])) }}">

                {{-- [PERUBAHAN]: "Meta row" sekarang berisi NAMA dan TANGGAL --}}
                <div style="{{ inlineStyle(array_merge($s_exp['meta'] ?? [], [
                    'overflow' => 'hidden',
                    'margin-bottom' => '4px',
                    'width' => '100%'
                ])) }}">

                    {{-- 1. Nama Prestasi (float: left) --}}
                    {{-- Kita bungkus <strong> dengan style float:left --}}
                    <strong style="float: left;">
                        <span contenteditable="true"
                              class="inline-edit"
                              data-cv="{{ $cv->id }}"
                              data-section="achievements"
                              data-id="{{ $achId }}"
                              data-field="achievement_name"
                              style="{{ inlineStyle(array_merge($s_exp['company'] ?? [], [ // Pakai style 'company'
                                  'border' => '1px dashed #ccc',
                                  'padding' => '4px 6px',
                                  'border-radius' => '4px',
                              ])) }}">
                            {{ $ach->achievement_name }}
                        </span>
                    </strong>

                    {{-- 2. Tanggal (float: right) --}}
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
                              data-section="achievements"
                              data-id="{{ $achId }}"
                              data-field="date_achievement">
                            {{ $ach->date_achievement ? \Carbon\Carbon::parse($ach->date_achievement)->format('d-m-Y') : '...' }}
                        </span>
                    </span>
                </div>

                {{-- 3. Deskripsi (opsional) --}}
                @php
                    $desc = (string)($ach->description_achievement ?? '');
                    $plain = trim(preg_replace(['/&nbsp;/i','/<br\s*\/?>/i'], [' ',' '], strip_tags($desc)));
                @endphp

                @if($plain !== '')
                    <div contenteditable="true"
                         class="inline-edit"
                         data-cv="{{ $cv->id }}"
                         data-section="achievements"
                         data-id="{{ $achId }}"
                         data-field="description_achievement"
                         style="{{ inlineStyle(array_merge($s_exp['li'] ?? [], [ // Pakai style 'li'
                             'border' => '1px dashed #ccc',
                             'padding' => '4px 6px',
                             'border-radius' => '4px',
                         ])) }}">
                        {!! $desc !!}
                    </div>
                @endif

            </div> {{-- Akhir <div> item --}}
        @endforeach
    </div>
</div>
@endif
