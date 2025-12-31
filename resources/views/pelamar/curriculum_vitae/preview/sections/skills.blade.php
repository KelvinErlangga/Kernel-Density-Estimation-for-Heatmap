@php
$skills = $cv->skills;
$s = $style['skills'] ?? [];

$categoryRules = [
    'programming' => ['php','javascript','js','typescript','ts','python','java','dart','c#','c++','golang','go','kotlin','swift','ruby','rust'],
    'frameworks'  => ['laravel','next','nextjs','react','reactjs','vue','nuxt','express','django','flask','spring','yii','flutter','tailwind','bootstrap','material ui','mui'],
    'database'    => ['mysql','mariadb','postgres','postgresql','mongodb','firebase','redis','sql server','sqlite'],
    'tools'       => ['git','github','gitlab','bitbucket','vscode','visual studio code','docker','postman','figma','linux'],
    'testing'     => ['phpunit','jest','mocha','selenium','cypress','testing','debugging','unit test','integration test'],
    'others_web'  => ['html','html5','css','css3','rest','restful','api','jwt','oauth','responsive','web development'],
    'soft'        => ['komunikasi','communication','kerjasama','teamwork','leadership','problem solving','adaptability','time management','critical thinking','mengajar','teaching'],
];

$grouped = collect();
foreach (array_keys($categoryRules) as $catKey) $grouped[$catKey] = collect();

$normalize = function($txt) {
    $t = trim((string)$txt);
    $t = preg_replace('/\s+/', ' ', $t);
    return $t;
};

// kata placeholder yang sering “ke-save” tidak sengaja
$placeholderWords = ['skill','keahlian'];

foreach ($skills as $skill) {
    $nameRaw = $normalize($skill->skill_name ?? '');
    if ($nameRaw === '') continue;

    $nameLower = strtolower($nameRaw);
    if (in_array($nameLower, $placeholderWords, true)) continue; // <-- ini biar yang kosong/placeholder gak dianggap isi

    $picked = null;
    foreach ($categoryRules as $catKey => $keywords) {
        foreach ($keywords as $kw) {
            if ($kw !== '' && str_contains($nameLower, strtolower($kw))) {
                $picked = $catKey;
                break 2;
            }
        }
    }
    if (!$picked) $picked = 'others_web';

    $exists = $grouped[$picked]->first(function($row) use ($nameLower) {
        return strtolower($row['name']) === $nameLower;
    });

    if (!$exists) {
        $grouped[$picked]->push([
            'id'   => $skill->id,
            'name' => $nameRaw,
        ]);
    }
}

$grouped = $grouped->filter(fn($items) => $items->isNotEmpty());

$liStyle = inlineStyle(array_merge($s['li'] ?? [], [
    'margin-bottom' => '8px',
    'line-height' => '1.5',
]));
$labelStyle = 'font-weight:700;';

$skillItemStyle = inlineStyle([
    'border' => '1px dashed #ccc',
    'padding' => '2px 4px',
    'border-radius' => '4px',
    'display' => 'inline',
    'white-space' => 'nowrap',
]);
@endphp

@if($skills && $skills->isNotEmpty() && $grouped->isNotEmpty())
<div style="{{ inlineStyle(array_merge($style['page'] ?? [], ['margin-bottom' => '12px'])) }}">
    <div style="{{ inlineStyle($s['container'] ?? []) }}">
        <h3 data-i18n="section.skills" style="{{ inlineStyle($s['h3'] ?? []) }}">KEAHLIAN</h3>

        <ul style="{{ inlineStyle(array_merge($s['ul'] ?? [], [
            'margin' => '0',
            'padding-left' => '20px',
        ])) }}">
            @foreach($grouped as $catKey => $items)
                <li class="skill-li" data-cat="{{ $catKey }}" style="{{ $liStyle }}">
                    <span class="skill-cat-label"
                          data-i18n="skill.cat.{{ $catKey }}"
                          data-cat-key="{{ $catKey }}"
                          style="{{ $labelStyle }}">{{ $catKey }}</span><span style="{{ $labelStyle }}">:</span>

                    {{-- dibungkus supaya JS gampang rapihin & bisa hapus kategori jika kosong --}}
                    <span class="skill-items">
                        @foreach($items as $row)
                            <span contenteditable="true"
                                  class="inline-edit skill-item"
                                  data-cv="{{ $cv->id }}"
                                  data-section="skills"
                                  data-id="{{ $row['id'] }}"
                                  data-field="skill_name"
                                  data-placeholder="Skill"
                                  style="{{ $skillItemStyle }}">{{ $row['name'] }}</span>@if(!$loop->last), @endif
                        @endforeach
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
