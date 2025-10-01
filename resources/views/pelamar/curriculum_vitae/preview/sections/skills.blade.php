@php
$skills = $cv->skills;
$s = $style['skills'] ?? [];
@endphp

@if($skills->isNotEmpty())
<div style="{{ inlineStyle($style['page'] ?? []) }}">
    <div style="{{ inlineStyle($s['container'] ?? []) }}">
        <h3 style="{{ inlineStyle($s['h3'] ?? []) }}">SKILLS</h3>
        <ul style="{{ inlineStyle($s['ul'] ?? []) }}">
            @foreach($skills as $skill)
                <li style="margin-bottom: 6px;">
                    <span contenteditable="true"
                          class="inline-edit"
                          data-cv="{{ $cv->id }}"
                          data-section="skills"
                          data-id="{{ $skill->id }}"
                          data-field="skill_name"
                          style="{{ inlineStyle(array_merge($s['li'] ?? [], [
                              'border' => '1px dashed #ccc',
                              'padding' => '2px 4px',
                              'border-radius' => '4px',
                              'display' => 'inline-block'
                          ])) }}">
                        {{ $skill->skill_name }} – {{ $skill->category_level }}
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
