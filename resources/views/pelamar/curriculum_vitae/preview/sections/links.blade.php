@php
$links = $cv->links;
$s = $style['links'] ?? [];
@endphp

@if($links->isNotEmpty())
<div style="{{ inlineStyle($style['page'] ?? []) }}">
    <div style="{{ inlineStyle($s['container'] ?? []) }}">
        <h3 style="{{ inlineStyle($s['h3'] ?? []) }}">LINKS</h3>
        <ul style="{{ inlineStyle($s['ul'] ?? []) }}">
            @foreach($links as $link)
                <li style="margin-bottom: 6px;">
                    <a href="{{ $link->url }}"
                       contenteditable="true"
                       class="inline-edit"
                       data-cv="{{ $cv->id }}"
                       data-section="links"
                       data-id="{{ $link->id }}"
                       data-field="link_name"
                       style="{{ inlineStyle(array_merge($s['li'] ?? [], [
                           'border' => '1px dashed #ccc',
                           'padding' => '2px 4px',
                           'border-radius' => '4px',
                           'display' => 'inline-block'
                       ])) }}">
                        {{ $link->link_name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
