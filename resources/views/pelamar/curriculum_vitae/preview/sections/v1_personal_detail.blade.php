@php
function inlineStyle($styles) {
    return collect($styles ?? [])->map(function($v, $k) {
        return $k.':'.$v;
    })->implode(';');
}
@endphp

@php
$personal = $cv->personalDetail;
$s = $style['personal_detail'] ?? [];

$infos = [
    trim($personal->city_curriculum_vitae . ( $personal->address_curriculum_vitae ? ', ' . $personal->address_curriculum_vitae : '' )),
    $personal->phone_curriculum_vitae,
    $personal->email_curriculum_vitae
];
$infos = array_filter($infos);
@endphp

@if($personal)
<div style="{{ inlineStyle($style['page'] ?? []) }}">
    <div style="{{ inlineStyle($s['container'] ?? []) }}">
        <h1 style="{{ inlineStyle($s['name'] ?? []) }}">
            {{ $personal->first_name_curriculum_vitae }} {{ $personal->last_name_curriculum_vitae }}
        </h1>

        <!-- beri class & data-section supaya CSS bisa target -->
        <div class="personal-info" style="{{ inlineStyle($s['row'] ?? []) }}" data-section="personal_detail">
            @foreach($infos as $info)
                <span class="personal-info__item" style="{{ inlineStyle($s['info'] ?? []) }}">{{ $info }}</span>
            @endforeach
        </div>

        <p style="{{ inlineStyle($s['summary'] ?? []) }}">{{ $personal->personal_summary }}</p>
    </div>
</div>
@endif
