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

        {{-- Personal Info + Links (separator via CSS) --}}
        <div class="personal-info"
            style="{{ inlineStyle(array_merge($s['row'] ?? [], [
                'display' => 'flex',
                'flex-wrap' => 'wrap',
                'align-items' => 'center',
                'gap' => '0px',     // <-- penting: jangan dobel spacing
            ])) }}"
            data-section="personal_detail">

            @php
                $baseInfoArr = array_merge($s['info'] ?? [], [
                    'padding' => '2px 4px',
                    'border-radius' => '4px',
                    'border' => '1px dashed #ccc',
                    'display' => 'inline-block',
                ]);
                $infoStyle = inlineStyle($baseInfoArr);

                $links = $cv->links;

                $linkStyle = inlineStyle(array_merge($baseInfoArr, [
                    'text-decoration' => 'underline',
                    'cursor' => 'pointer',
                ]));
            @endphp

            {{-- City --}}
            @if(!empty($personal->city_curriculum_vitae))
                <span class="pi-item">
                    <span class="inline-edit"
                        contenteditable="true"
                        data-section="personal_detail"
                        data-field="city_curriculum_vitae"
                        data-placeholder="Kota"
                        style="{{ $infoStyle }}">
                        {{ $personal->city_curriculum_vitae }}
                    </span>
                </span>
            @endif

            {{-- Address --}}
            @if(!empty($personal->address_curriculum_vitae))
                <span class="pi-item">
                    <span class="inline-edit"
                        contenteditable="true"
                        data-section="personal_detail"
                        data-field="address_curriculum_vitae"
                        data-placeholder="Alamat"
                        style="{{ $infoStyle }}">
                        {{ $personal->address_curriculum_vitae }}
                    </span>
                </span>
            @endif

            {{-- Phone --}}
            @if(!empty($personal->phone_curriculum_vitae))
                <span class="pi-item">
                    <span class="inline-edit"
                        contenteditable="true"
                        data-section="personal_detail"
                        data-field="phone_curriculum_vitae"
                        data-placeholder="No. HP"
                        style="{{ $infoStyle }}">
                        {{ $personal->phone_curriculum_vitae }}
                    </span>
                </span>
            @endif

            {{-- Email --}}
            @if(!empty($personal->email_curriculum_vitae))
                <span class="pi-item">
                    <span class="inline-edit"
                        contenteditable="true"
                        data-section="personal_detail"
                        data-field="email_curriculum_vitae"
                        data-placeholder="Email"
                        style="{{ $infoStyle }}">
                        {{ $personal->email_curriculum_vitae }}
                    </span>
                </span>
            @endif

            {{-- Links --}}
            @if($links && $links->isNotEmpty())
                @foreach($links as $link)
                    <span class="pi-item">
                        <a href="{{ $link->url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="cv-link"
                        style="{{ $linkStyle }}">
                            <span class="cv-link-text"
                                contenteditable="false"
                                data-cv="{{ $cv->id }}"
                                data-section="links"
                                data-id="{{ $link->id }}"
                                data-field="url"
                                data-placeholder="Link">
                                {{ $link->url }}
                            </span>
                        </a>
                    </span>
                @endforeach
            @endif
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
    .inline-edit[contenteditable="true"]:empty::before,
    .cv-link-text[contenteditable="true"]:empty::before {
        content: attr(data-placeholder);
        color: #888;
        pointer-events: none;
    }

    /* LINK: underline lurus pakai pseudo-element */
    .cv-link,
    .cv-link:visited,
    .cv-link:hover,
    .cv-link:active {
    color: #2563eb !important;
    text-decoration: none !important;

    position: relative;
    display: inline-block;      /* penting */
    padding-bottom: 2px;        /* jarak garis dari teks */
    }

    .cv-link::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;                  /* posisi garis */
    height: 1px;
    background: #2563eb;
    border-radius: 0;           /* pastikan lurus */
    }

    /* kalau ada child di dalam link */
    .cv-link * {
    color: inherit !important;
    text-decoration: none !important;
    }

    /* Personal info: separator rapih & sejajar */
    .personal-info {
    line-height: 1.2; /* biar baseline konsisten */
    }

    .personal-info .pi-item{
    display: inline-flex;
    align-items: center;
    }

    /* separator otomatis, tidak dobel dengan gap */
    .personal-info .pi-item + .pi-item::before{
    content: "|";
    opacity: .7;
    margin: 0 10px;          /* atur jarak di sini */
    line-height: 1;          /* biar nggak “turun” */
    transform: translateY(-1px); /* fine-tune biar sejajar */
    }
</style>

<script>
    /**
     * Personal inline placeholder helper (punya kamu)
     */
    document.querySelectorAll('.inline-edit').forEach(el => {
        el.addEventListener('focus', () => {
            if (el.textContent.trim() === '') el.textContent = '';
        });
        el.addEventListener('blur', () => {
            if (el.textContent.trim() === '') el.textContent = '';
        });
    });

    /**
     * ✅ LINKS:
     * - Klik = buka link (new tab)
     * - Double-click = edit inline
     * - Blur / Enter = simpan & balik jadi non-editable
     */
    document.querySelectorAll('.cv-link').forEach(a => {
        const span = a.querySelector('.cv-link-text');
        if (!span) return;

        // kalau lagi edit, jangan navigate
        a.addEventListener('click', (e) => {
            if (span.getAttribute('contenteditable') === 'true') {
                e.preventDefault();
            }
        });

        // double-click untuk edit
        span.addEventListener('dblclick', (e) => {
            e.preventDefault();
            e.stopPropagation();

            span.setAttribute('contenteditable', 'true');
            span.focus();

            // select all text
            try { document.execCommand('selectAll', false, null); } catch (err) {}
        });

        // Enter = selesai edit & save
        span.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                span.blur();
            }
            if (e.key === 'Escape') {
                e.preventDefault();
                span.setAttribute('contenteditable', 'false');
                span.blur();
            }
        });

        span.addEventListener('blur', async () => {
            if (span.getAttribute('contenteditable') !== 'true') return;

            span.setAttribute('contenteditable', 'false');
            const value = (span.innerText || '').trim();

            // Simpan pakai endpoint inline edit kamu
            try {
                await fetch("{{ route('pelamar.curriculum_vitae.updateInline') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        cv_id: "{{ $cv->id }}",
                        section: "links",
                        field: "url",
                        value: value,
                        id: span.dataset.id || null
                    })
                });
            } catch (err) {
                console.warn('Save link failed:', err);
            }

            // update href biar klik berikutnya sesuai value baru
            if (value) {
                a.setAttribute('href', value);
            }
        });
    });
</script>
