@php
function inlineStyle($styles) {
    return collect($styles ?? [])->map(function($v, $k) {
        return $k.':'.$v;
    })->implode(';');
}

$personal = $cv->personalDetail;
$s = $style['personal_detail'] ?? [];
@endphp

<div style="{{ inlineStyle($style['page'] ?? []) }}">
    <div style="{{ inlineStyle($s['container'] ?? []) }}">
        {{-- Name --}}
        <h1 class="inline-edit"
            contenteditable="true"
            data-section="personal_detail"
            data-field="name"
            data-placeholder="Nama Lengkap"
            style="{{ inlineStyle(array_merge($s['name'] ?? [], [
                'padding' => '4px 6px',
                'border-radius' => '4px',
                'border' => '1px dashed #ccc',
                'position' => 'relative'
            ])) }}">
            {{ $personal->first_name_curriculum_vitae ?? '' }} {{ $personal->last_name_curriculum_vitae ?? '' }}
        </h1>

        {{-- Personal Info --}}
        <div class="personal-info" style="{{ inlineStyle($s['row'] ?? []) }}" data-section="personal_detail">
            {{-- City --}}
            <span class="inline-edit"
                  contenteditable="true"
                  data-section="personal_detail"
                  data-field="city_curriculum_vitae"
                  data-placeholder="Kota"
                  style="{{ inlineStyle(array_merge($s['info'] ?? [], [
                      'padding' => '2px 4px',
                      'border-radius' => '4px',
                      'margin-right' => '2px',
                      'border' => '1px dashed #ccc'
                  ])) }}">
                {{ $personal->city_curriculum_vitae ?? '' }}
            </span>

            {{-- Address --}}
            <span class="inline-edit personal-info__item"
                  contenteditable="true"
                  data-section="personal_detail"
                  data-field="address_curriculum_vitae"
                  data-placeholder="Alamat"
                  style="{{ inlineStyle(array_merge($s['info'] ?? [], [
                      'padding' => '2px 1px',
                      'border-radius' => '4px',
                      'border' => '1px dashed #ccc'
                  ])) }}">
                {{ $personal->address_curriculum_vitae ?? '' }}
            </span>

            {{-- Phone --}}
            <span class="inline-edit personal-info__item"
                  contenteditable="true"
                  data-section="personal_detail"
                  data-field="phone_curriculum_vitae"
                  data-placeholder="No. HP"
                  style="{{ inlineStyle(array_merge($s['info'] ?? [], [
                      'padding' => '2px 1px',
                      'border-radius' => '4px',
                      'border' => '1px dashed #ccc'
                  ])) }}">
                {{ $personal->phone_curriculum_vitae ?? '' }}
            </span>

            {{-- Email --}}
            <span class="inline-edit personal-info__item"
                  contenteditable="true"
                  data-section="personal_detail"
                  data-field="email_curriculum_vitae"
                  data-placeholder="Email"
                  style="{{ inlineStyle(array_merge($s['info'] ?? [], [
                      'padding' => '2px 1px',
                      'border-radius' => '4px',
                      'border' => '1px dashed #ccc'
                  ])) }}">
                {{ $personal->email_curriculum_vitae ?? '' }}
            </span>
        </div>

        {{-- Summary --}}
        <p class="inline-edit"
           contenteditable="true"
           data-section="personal_detail"
           data-field="personal_summary"
           data-placeholder="Ringkasan singkat tentang keterampilan dan pengalaman Anda di sini"
           style="{{ inlineStyle(array_merge($s['summary'] ?? [], [
                'padding' => '4px 6px',
                'border-radius' => '4px',
                'border' => '1px dashed #ccc'
           ])) }}">
            {{ $personal->personal_summary ?? '' }}
        </p>
    </div>
</div>

<style>
/* Placeholder styling untuk contenteditable */
.inline-edit[contenteditable="true"]:empty::before {
    content: attr(data-placeholder);
    color: #888;
    pointer-events: none; /* biar teks placeholder tidak bisa dipilih */
}
</style>

<script>
document.querySelectorAll('.inline-edit').forEach(el => {
    el.addEventListener('focus', () => {
        if(el.textContent.trim() === '') {
            el.textContent = '';
        }
    });
    el.addEventListener('blur', () => {
        // kosongkan jika user hapus semua teks agar placeholder muncul kembali
        if(el.textContent.trim() === '') {
            el.textContent = '';
        }
    });
});
</script>
