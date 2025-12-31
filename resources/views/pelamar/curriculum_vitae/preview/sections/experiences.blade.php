@php
$experiences = $cv->experiences;
$s = $style['experiences'] ?? [];
@endphp

<div style="{{ inlineStyle($style['page'] ?? []) }}">
    <div style="{{ inlineStyle($s['container'] ?? []) }}">
        <h3 style="{{ inlineStyle($s['h3'] ?? []) }}">PENGALAMAN KERJA</h3>

        @forelse($experiences as $index => $exp)
            @php
                $expId = $exp->id ?? "new-{$index}";
            @endphp

            <div style="{{ inlineStyle($s['div'] ?? []) }}">
                {{-- Company --}}
                <strong>
                    <span contenteditable="true"
                          class="inline-edit"
                          data-cv="{{ $cv->id }}"
                          data-section="experiences"
                          data-id="{{ $expId }}"
                          data-field="company_experience"
                          data-placeholder="Nama Perusahaan"
                          style="{{ inlineStyle(array_merge($s['company'] ?? [], [
                              'border' => '1px dashed #ccc',
                              'padding' => '4px 6px',
                              'border-radius' => '4px',
                          ])) }}">
                        {{ $exp->company_experience }}
                    </span>
                </strong>

                {{-- Position + Dates + City --}}
                <div style="{{ inlineStyle($s['meta'] ?? []) }}">
                    <em>
                        <span contenteditable="true"
                              class="inline-edit"
                              data-cv="{{ $cv->id }}"
                              data-section="experiences"
                              data-id="{{ $expId }}"
                              data-field="position_experience"
                              data-placeholder="Jabatan"
                              style="{{ inlineStyle(array_merge($s['position'] ?? [], [
                                  'border' => '1px dashed #ccc',
                                  'padding' => '2px 4px',
                                  'border-radius' => '4px',
                                  'margin-right' => '4px'
                              ])) }}">
                            {{ $exp->position_experience }}
                        </span>
                    </em>

                    <span style="{{ inlineStyle(array_merge($s['date'] ?? [], [
                        'border' => '1px dashed #ccc',
                        'padding' => '2px 4px',
                        'border-radius' => '4px',
                        'margin-right' => '4px'
                    ])) }}">
                        <span contenteditable="true"
                              class="inline-edit"
                              data-cv="{{ $cv->id }}"
                              data-section="experiences"
                              data-id="{{ $expId }}"
                              data-field="start_date"
                              data-placeholder="Tanggal Mulai">
                            {{-- Format tanggal agar konsisten --}}
                            {{ $exp->start_date ? \Carbon\Carbon::parse($exp->start_date)->format('d-m-Y') : '...' }}
                        </span>
                        -
                        <span contenteditable="true"
                              class="inline-edit"
                              data-cv="{{ $cv->id }}"
                              data-section="experiences"
                              data-id="{{ $expId }}"
                              data-field="end_date"
                              data-placeholder="Tanggal Selesai">
                            {{-- Cek 'is_current' jika ada, jika tidak, pakai 'end_date' --}}
                            @if($exp->is_current ?? false)
                                Sekarang
                            @else
                                {{ $exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('d-m-Y') : 'Sekarang' }}
                            @endif
                        </span>
                    </span>

                    <span contenteditable="true"
                          class="inline-edit"
                          data-cv="{{ $cv->id }}"
                          data-section="experiences"
                          data-id="{{ $expId }}"
                          data-field="city_experience"
                          data-placeholder="Kota"
                          style="{{ inlineStyle(array_merge($s['date'] ?? [], [
                              'border' => '1px dashed #ccc',
                              'padding' => '2px 4px',
                              'border-radius' => '4px'
                          ])) }}">
                        {{ $exp->city_experience }}
                    </span>
                </div>

                {{-- [PERBAIKAN] Description --}}
                @php
                    $rawDesc = (string)($exp->description_experience ?? '');
                    // Cek apakah benar-benar kosong setelah strip tag dan trim
                    $plainDesc = trim(strip_tags($rawDesc));

                    // Cek apakah mengandung HTML list
                    $hasHtmlList = (strpos($rawDesc, '<ul') !== false || strpos($rawDesc, '<li') !== false);
                @endphp

                {{-- HANYA render jika $plainDesc TIDAK KOSONG --}}
                @if($plainDesc !== '')

                    {{-- Jika datanya adalah list HTML (seperti di job pertama) --}}
                    @if($hasHtmlList)
                        <div contenteditable="true"
                            class="inline-edit exp-desc"
                            data-cv="{{ $cv->id }}"
                            data-section="experiences"
                            data-id="{{ $expId }}"
                            data-field="description_experience"
                            data-placeholder="Deskripsi pengalaman kerja"
                            style="{{ inlineStyle(array_merge($s['ul'] ?? [], [
                                'border' => '1px dashed #ccc',
                                'padding' => '0px 1px',
                                'border-radius' => '4px',
                            ])) }}">
                        {!! $rawDesc !!}
                        </div>
                    @else
                        {{-- Jika datanya plain text, kita buatkan list --}}
                        @php
                            $descriptions = array_filter(array_map('trim', explode("\n", $rawDesc)));
                        @endphp
                        <ul style="{{ inlineStyle(array_merge($s['ul'] ?? [], [
                            'margin-top' => '6px',
                            'margin-bottom' => '0px',
                            'padding-left' => '18px',
                            'list-style-position' => 'outside',
                            'border' => '1px dashed #ccc',
                            'border-radius' => '4px',
                        ])) }}">
                            @foreach($descriptions as $desc)
                                <li contenteditable="true"
                                    class="inline-edit"
                                    data-cv="{{ $cv->id }}"
                                    data-section="experiences"
                                    data-id="{{ $expId }}"
                                    data-field="description_experience"
                                    data-placeholder="Tulis deskripsi..."
                                    style="{{ inlineStyle(array_merge($s['li'] ?? [], [
                                        'border' => '1px dashed #ccc',
                                        'padding' => '0px 1px',
                                        'border-radius' => '4px'
                                    ])) }}">
                                    {{ $desc }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                @endif {{-- Akhir cek $plainDesc --}}

            </div>
        @empty
            {{-- Jika belum ada pengalaman --}}
            <div style="{{ inlineStyle($s['div'] ?? []) }}">
                <strong>
                    <span contenteditable="true"
                          class="inline-edit"
                          data-cv="{{ $cv->id }}"
                          data-section="experiences"
                          data-id="new-0"
                          data-field="company_experience"
                          data-placeholder="Nama Perusahaan"
                          style="{{ inlineStyle(array_merge($s['company'] ?? [], [
                              'border' => '1px dashed #ccc',
                              'padding' => '0px 1px',
                              'border-radius' => '4px',
                          ])) }}">
                    </span>
                </strong>
                {{-- Lanjutkan untuk position, date, city, description dengan cara sama --}}
                 <div style="{{ inlineStyle($s['meta'] ?? []) }}">
                    <em>
                        <span contenteditable="true"
                              class="inline-edit"
                              data-cv="{{ $cv->id }}"
                              data-section="experiences"
                              data-id="new-0"
                              data-field="position_experience"
                              data-placeholder="Jabatan"
                              style="{{ inlineStyle(array_merge($s['position'] ?? [], [
                                  'border' => '1px dashed #ccc',
                                  'padding' => '2px 4px',
                                  'border-radius' => '4px',
                                  'margin-right' => '4px'
                              ])) }}">
                        </span>
                    </em>
                    <span style="{{ inlineStyle(array_merge($s['date'] ?? [], [
                        'border' => '1px dashed #ccc',
                        'padding' => '2px 4px',
                        'border-radius' => '4px',
                        'margin-right' => '4px'
                    ])) }}">
                        <span contenteditable="true"
                              class="inline-edit"
                              data-cv="{{ $cv->id }}"
                              data-section="experiences"
                              data-id="new-0"
                              data-field="start_date"
                              data-placeholder="Tanggal Mulai">
                        </span>
                        -
                        <span contenteditable="true"
                              class="inline-edit"
                              data-cv="{{ $cv->id }}"
                              data-section="experiences"
                              data-id="new-0"
                              data-field="end_date"
                              data-placeholder="Tanggal Selesai">
                        </span>
                    </span>
                    <span contenteditable="true"
                          class="inline-edit"
                          data-cv="{{ $cv->id }}"
                          data-section="experiences"
                          data-id="new-0"
                          data-field="city_experience"
                          data-placeholder="Kota"
                          style="{{ inlineStyle(array_merge($s['date'] ?? [], [
                              'border' => '1px dashed #ccc',
                              'padding' => '2px 4px',
                              'border-radius' => '4px'
                          ])) }}">
                    </span>
                </div>
            </div>
        @endforelse
    </div>
</div>

{{-- CSS untuk placeholder ngawang --}}
<style>
.inline-edit:empty:before {
    content: attr(data-placeholder);
    color: #aaa;
    pointer-events: none;
}
.inline-edit {
    min-width: 50px;
    display: inline-block;
}
/* FIX: margin default UL bikin jarak role jadi jauh */
.exp-desc ul{
  margin: 6px 0 0 0 !important;     /* atas dikit, bawah 0 */
  padding-left: 14px !important;    /* indent bullet */
}
.exp-desc li{
  margin-bottom: 2px !important;
}
/* Khusus pengalaman kerja: bullet harus masuk ke kanan */
.section.experiences ul,
.exp-desc ul {
  margin: 6px 0 0 0 !important;
  padding-left: 24px !important;       /* posisi teks bullet masuk */
  list-style-position: outside !important; /* bullet di luar teks, rapi */
}

.section.experiences li,
.exp-desc li {
  margin-bottom: 2px !important;
}

</style>
