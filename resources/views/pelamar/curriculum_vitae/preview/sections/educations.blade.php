@php
$educations = $cv->educations;
$s = $style['educations'] ?? [];
@endphp

@if($educations && $educations->count() > 0)
<div style="{{ inlineStyle($style['page'] ?? []) }}">
    <div style="{{ inlineStyle($s['container'] ?? []) }}">
        <h3 style="{{ inlineStyle($s['h3'] ?? []) }}">RIWAYAT PENDIDIKAN</h3>

        @foreach($educations as $edu)
            @php
                $descriptions = is_array($edu->description_education)
                    ? $edu->description_education
                    : explode("\n", $edu->description_education ?? '');
            @endphp

            <div style="{{ inlineStyle($s['div'] ?? []) }}">
                {{-- School Name --}}
                <strong>
                    <span contenteditable="true"
                          class="inline-edit"
                          data-cv="{{ $cv->id }}"
                          data-section="educations"
                          data-id="{{ $edu->id }}"
                          data-field="school_name"
                          style="{{ inlineStyle(array_merge($s['school_name'] ?? [], [
                              'border' => '1px dashed #ccc',
                              'padding' => '4px 6px',
                              'border-radius' => '4px',
                          ])) }}">
                        {{ $edu->school_name }}
                    </span>
                </strong>

                {{-- City + Dates --}}
                <div class="inline-edit"
                    contenteditable="true"
                    data-cv="{{ $cv->id }}"
                    data-section="educations"
                    data-id="{{ $edu->id }}"
                    data-field="city_start_end_date"
                    style="{{ inlineStyle(array_merge($s['date'] ?? [], [
                        'border' => '1px dashed #ccc',
                        'padding' => '2px 4px',
                        'border-radius' => '4px',
                        'margin-bottom' => '4px'
                    ])) }}">
                    <span contenteditable="true"
                        class="inline-edit"
                        data-cv="{{ $cv->id }}"
                        data-section="educations"
                        data-id="{{ $edu->id }}"
                        data-field="city_education">
                        {{ $edu->city_education }}
                    </span>,
                    <span contenteditable="true"
                        class="inline-edit"
                        data-cv="{{ $cv->id }}"
                        data-section="educations"
                        data-id="{{ $edu->id }}"
                        data-field="start_date">
                        {{ $edu->start_date }}
                    </span> -
                    <span contenteditable="true"
                        class="inline-edit"
                        data-cv="{{ $cv->id }}"
                        data-section="educations"
                        data-id="{{ $edu->id }}"
                        data-field="end_date">
                        {{ $edu->end_date ?? 'Sekarang' }}
                    </span>
                </div>

                {{-- Field of Study --}}
                <p contenteditable="true"
                   class="inline-edit"
                   data-cv="{{ $cv->id }}"
                   data-section="educations"
                   data-id="{{ $edu->id }}"
                   data-field="field_of_study"
                   style="{{ inlineStyle(array_merge($s['field'] ?? [], [
                       'border' => '1px dashed #ccc',
                       'padding' => '2px 4px',
                       'border-radius' => '4px',
                   ])) }}">
                    {{ $edu->field_of_study }}
                </p>

                {{-- Description --}}
                @if(!empty(array_filter($descriptions)))
                    <ul style="{{ inlineStyle(array_merge($s['ul'] ?? [], [
                        'border' => '1px dashed #ccc',
                        'padding' => '4px 6px',
                        'border-radius' => '4px'
                    ])) }}">
                        @foreach($descriptions as $desc)
                            @if(!empty($desc))
                                <li contenteditable="true"
                                    class="inline-edit"
                                    data-cv="{{ $cv->id }}"
                                    data-section="educations"
                                    data-id="{{ $edu->id }}"
                                    data-field="description_education"
                                    style="{{ inlineStyle(array_merge($s['li'] ?? [], [
                                        'border' => '1px dashed #ccc',
                                        'padding' => '2px 4px',
                                        'border-radius' => '4px',
                                    ])) }}">
                                    {{ $desc }}
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif
