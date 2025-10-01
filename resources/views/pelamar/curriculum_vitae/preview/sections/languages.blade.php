@php
$languages = $cv->languages;
$s = $style['languages'] ?? [];
@endphp

@if($languages->count())
<div style="{{ inlineStyle($style['page'] ?? []) }}">
    <div style="{{ inlineStyle($s['container'] ?? []) }}">
        <h3 style="{{ inlineStyle($s['h3'] ?? []) }}">BAHASA</h3>
        <ul style="{{ inlineStyle($s['ul'] ?? []) }}">
            @foreach($languages as $lang)
                <li style="margin-bottom: 6px;">
                    <span contenteditable="true"
                          class="inline-edit"
                          data-cv="{{ $cv->id }}"
                          data-section="languages"
                          data-id="{{ $lang->id }}"
                          data-field="language_name"
                          style="{{ inlineStyle(array_merge($s['li'] ?? [], [
                              'border' => '1px dashed #ccc',
                              'padding' => '2px 4px',
                              'border-radius' => '4px',
                              'display' => 'inline-block',
                              'margin-right' => '6px'
                          ])) }}">
                        {{ $lang->language_name }}
                    </span>

                    @if(!empty($lang->proficiency))
                        <span contenteditable="true"
                              class="inline-edit"
                              data-cv="{{ $cv->id }}"
                              data-section="languages"
                              data-id="{{ $lang->id }}"
                              data-field="proficiency"
                              style="{{ inlineStyle(array_merge($s['li'] ?? [], [
                                  'border' => '1px dashed #ccc',
                                  'padding' => '2px 4px',
                                  'border-radius' => '4px',
                                  'display' => 'inline-block'
                              ])) }}">
                            {{ $lang->proficiency }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
