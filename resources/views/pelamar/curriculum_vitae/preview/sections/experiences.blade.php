@php
$experiences = $cv->experiences;
$s = $style['experiences'] ?? [];
@endphp

<div style="{{ inlineStyle($style['page'] ?? []) }}">
    <div style="{{ inlineStyle($s['container'] ?? []) }}">
        <h3 style="{{ inlineStyle($s['h3'] ?? []) }}">PENGALAMAN KERJA</h3>

        @forelse($experiences as $index => $exp)
            @php
                $raw = $exp->description_experience ?? '';
                $hasHtml = is_string($raw) &&
                    (strpos($raw, '<ul') !== false || strpos($raw, '<li') !== false || strpos($raw, '<br') !== false);

                if (!$hasHtml) {
                    if (is_array($raw)) {
                        $descriptions = array_filter(array_map('trim', $raw));
                    } else {
                        $descriptions = array_filter(array_map('trim', explode("\n", (string)$raw)));
                    }
                } else {
                    $descriptions = [];
                }

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
                            {{ $exp->start_date }}
                        </span>
                        -
                        <span contenteditable="true"
                              class="inline-edit"
                              data-cv="{{ $cv->id }}"
                              data-section="experiences"
                              data-id="{{ $expId }}"
                              data-field="end_date"
                              data-placeholder="Tanggal Selesai">
                            {{ $exp->end_date }}
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

                {{-- Description --}}
                @if($hasHtml)
                    <div contenteditable="true"
                         class="inline-edit"
                         data-cv="{{ $cv->id }}"
                         data-section="experiences"
                         data-id="{{ $expId }}"
                         data-field="description_experience"
                         data-placeholder="Deskripsi pengalaman kerja"
                         style="{{ inlineStyle(array_merge($s['description'] ?? [], [
                             'border' => '1px dashed #ccc',
                             'padding' => '4px 6px',
                             'border-radius' => '4px'
                         ])) }}">
                        {!! $raw !!}
                    </div>
                @else
                    @if(!empty($descriptions))
                        <ul style="{{ inlineStyle(array_merge($s['ul'] ?? [], [
                            'border' => '1px dashed #ccc',
                            'padding' => '4px 6px',
                            'border-radius' => '4px'
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
                                        'padding' => '2px 4px',
                                        'border-radius' => '4px'
                                    ])) }}">
                                    {{ $desc }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p contenteditable="true"
                           class="inline-edit"
                           data-cv="{{ $cv->id }}"
                           data-section="experiences"
                           data-id="{{ $expId }}"
                           data-field="description_experience"
                           data-placeholder="Tulis deskripsi pengalaman kerja"
                           style="{{ inlineStyle(array_merge($s['li'] ?? [], [
                               'border' => '1px dashed #ccc',
                               'padding' => '2px 4px',
                               'border-radius' => '4px'
                           ])) }}">
                        </p>
                    @endif
                @endif
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
                              'padding' => '4px 6px',
                              'border-radius' => '4px',
                          ])) }}">
                    </span>
                </strong>
                {{-- Lanjutkan untuk position, date, city, description dengan cara sama --}}
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
</style>
