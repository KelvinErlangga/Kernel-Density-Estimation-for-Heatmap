@php
$organizations = $cv->organizations;
$s = $style['organizations'] ?? [];
@endphp

@if($organizations->count())
<div style="{{ inlineStyle($style['page'] ?? []) }}">
    <div style="{{ inlineStyle($s['container'] ?? []) }}">
        <h3 style="{{ inlineStyle($s['h3'] ?? []) }}">PENGALAMAN ORGANISASI</h3>
        <ul style="{{ inlineStyle($s['ul'] ?? []) }}">
            @foreach($organizations as $org)
                <li style="margin-bottom: 6px;">
                    <span contenteditable="true"
                          class="inline-edit"
                          data-cv="{{ $cv->id }}"
                          data-section="organizations"
                          data-id="{{ $org->id }}"
                          data-field="organization_name"
                          style="{{ inlineStyle(array_merge($s['div'] ?? [], [
                              'border' => '1px dashed #ccc',
                              'padding' => '2px 4px',
                              'border-radius' => '4px',
                              'display' => 'inline-block'
                          ])) }}">
                        {{ $org->organization_name }}
                    </span>
                    -
                    <span contenteditable="true"
                          class="inline-edit"
                          data-cv="{{ $cv->id }}"
                          data-section="organizations"
                          data-id="{{ $org->id }}"
                          data-field="position_organization"
                          style="{{ inlineStyle(array_merge($s['div'] ?? [], [
                              'border' => '1px dashed #ccc',
                              'padding' => '2px 4px',
                              'border-radius' => '4px',
                              'display' => 'inline-block',
                              'margin-left' => '4px'
                          ])) }}">
                        {{ $org->position_organization }}
                    </span>
                    <br>
                    <small>
                        <span contenteditable="true"
                              class="inline-edit"
                              data-cv="{{ $cv->id }}"
                              data-section="organizations"
                              data-id="{{ $org->id }}"
                              data-field="start_date">
                            {{ $org->start_date }}
                        </span>
                        -
                        <span contenteditable="true"
                              class="inline-edit"
                              data-cv="{{ $cv->id }}"
                              data-section="organizations"
                              data-id="{{ $org->id }}"
                              data-field="end_date">
                            {{ $org->end_date ?? 'Sekarang' }}
                        </span>
                    </small>
                    <div contenteditable="true"
                         class="inline-edit"
                         data-cv="{{ $cv->id }}"
                         data-section="organizations"
                         data-id="{{ $org->id }}"
                         data-field="description"
                         style="{{ inlineStyle(array_merge($s['div'] ?? [], [
                             'border' => '1px dashed #ccc',
                             'padding' => '2px 4px',
                             'border-radius' => '4px',
                             'margin-top' => '4px'
                         ])) }}">
                        {{ $org->description }}
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
