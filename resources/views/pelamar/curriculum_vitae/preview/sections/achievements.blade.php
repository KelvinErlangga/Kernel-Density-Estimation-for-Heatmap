@php
$achievements = $cv->achievements;
$s = $style['achievements'] ?? [];
@endphp

@if($achievements && $achievements->count() > 0)
<div style="{{ inlineStyle($style['page'] ?? []) }}">
    <div style="{{ inlineStyle($s['container'] ?? []) }}">
        <h3 style="{{ inlineStyle($s['h3'] ?? []) }}">PRESTASI</h3>
        <ul style="{{ inlineStyle($s['ul'] ?? []) }}">
            @foreach($achievements as $ach)
                <li style="margin-bottom: 6px;">
                    <span contenteditable="true"
                          class="inline-edit"
                          data-cv="{{ $cv->id }}"
                          data-section="achievements"
                          data-id="{{ $ach->id }}"
                          data-field="achievement_name"
                          style="{{ inlineStyle(array_merge($s['li'] ?? [], [
                              'border' => '1px dashed #ccc',
                              'padding' => '2px 4px',
                              'border-radius' => '4px',
                              'display' => 'inline-block',
                              'margin-bottom' => '2px'
                          ])) }}">
                        {{ $ach->achievement_name }}
                    </span>
                    <br>
                    <span contenteditable="true"
                          class="inline-edit"
                          data-cv="{{ $cv->id }}"
                          data-section="achievements"
                          data-id="{{ $ach->id }}"
                          data-field="date_achievement"
                          style="{{ inlineStyle(array_merge($s['li'] ?? [], [
                              'border' => '1px dashed #ccc',
                              'padding' => '2px 4px',
                              'border-radius' => '4px',
                              'display' => 'inline-block'
                          ])) }}">
                        {{ $ach->date_achievement }}
                    </span>
                    <div contenteditable="true"
                         class="inline-edit"
                         data-cv="{{ $cv->id }}"
                         data-section="achievements"
                         data-id="{{ $ach->id }}"
                         data-field="description_achievement"
                         style="{{ inlineStyle(array_merge($s['li'] ?? [], [
                             'border' => '1px dashed #ccc',
                             'padding' => '2px 4px',
                             'border-radius' => '4px',
                             'margin-top' => '4px'
                         ])) }}">
                        {!! $ach->description_achievement !!}
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
