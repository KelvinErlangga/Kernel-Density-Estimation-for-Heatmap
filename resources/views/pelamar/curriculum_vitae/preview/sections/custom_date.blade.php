@php
    $rawItem = $section->payload['items'][0] ?? [];

    $item = [
        'section_name' => $rawItem['section_name'] ?? ($rawItem['org_name'] ?? 'Nama kegiatan / organisasi'),
        'position'     => $rawItem['position']     ?? 'Jabatan atau peran Anda',
        'start_date'   => $rawItem['start_date']   ?? null,
        'end_date'     => $rawItem['end_date']     ?? null,
        'description'  => $rawItem['description']  ?? 'Tuliskan secara singkat tanggung jawab, aktivitas utama, dan pencapaian Anda di kegiatan ini.'
    ];

    $title = $section->section_title ?: 'Custom dated section';

    $startIso = $item['start_date'];
    $endIso   = $item['end_date'];

    $todayIso = now()->format('Y-m-d'); // tanggal hari ini (YYYY-MM-DD)

    // start date: selalu tanggal
    $startDisplay = $startIso
        ? \Carbon\Carbon::parse($startIso)->format('d-m-Y')
        : 'dd-mm-yyyy';

    // end date: kalau sama dengan hari ini → "Sekarang"
    if ($endIso === $todayIso) {
        $endDisplay = 'Sekarang';
    } elseif ($endIso) {
        $endDisplay = \Carbon\Carbon::parse($endIso)->format('d-m-Y');
    } else {
        $endDisplay = 'dd-mm-yyyy';
    }
@endphp

<div class="section custom-text"
     data-key="custom_date"
     data-custom-id="{{ $section->id }}"
     style="position:relative; padding:8px 0;">

    {{-- drag & delete --}}
    <span class="drag-indicator"
          style="display:none;position:absolute;left:8px;top:12px;font-size:20px;color:#3538cd;cursor:grab;z-index:10;">&#x2630;</span>

    <span class="delete-indicator custom-delete"
          data-custom-id="{{ $section->id }}"
          style="display:none;position:absolute;right:8px;top:12px;font-size:20px;color:#e74c3c;cursor:pointer;z-index:10;"
          title="Hapus section">&#128465;</span>

    {{-- judul section --}}
    <h3 contenteditable="true"
        class="editable custom-title"
        data-custom-id="{{ $section->id }}"
        data-field="section_title">
        {{ $title }}
    </h3>

    <div class="custom-body custom-date-body">

        {{-- BARIS 1: nama kegiatan / organisasi (full width) --}}
        <div style="margin-bottom:6px;">
            <span class="editable"
                  contenteditable="true"
                  spellcheck="false"
                  data-custom-id="{{ $section->id }}"
                  data-field="payload.items.0.section_name"
                  style="display:inline-block;font-weight:700;border:1px dashed #bbb;border-radius:4px;padding:4px 8px;">
                {{ $item['section_name'] }}
            </span>
        </div>

        {{-- BARIS 2: role (kiri) + tanggal (kanan) SEJAJAR --}}
        <div style="display:flex;justify-content:space-between;gap:16px;align-items:flex-start;margin-bottom:8px;">
            <div>
                <span class="editable"
                      contenteditable="true"
                      spellcheck="false"
                      data-custom-id="{{ $section->id }}"
                      data-field="payload.items.0.position"
                      style="display:inline-block;font-style:italic;border:1px dashed #bbb;border-radius:4px;padding:4px 8px;">
                    {{ $item['position'] }}
                </span>
            </div>

            <div style="text-align:right;">
                {{-- chip tampilan tanggal --}}
                <span class="custom-date-pill"
                      data-custom-id="{{ $section->id }}"
                      data-start-input-id="start-date-{{ $section->id }}"
                      data-end-input-id="end-date-{{ $section->id }}"
                      style="display:inline-block;border:1px dashed #bbb;border-radius:4px;padding:4px 8px;font-size:12px;cursor:pointer;white-space:nowrap;">
                    {{ $startDisplay }} – {{ $endDisplay }}
                </span>

                {{-- input date hidden --}}
                <input type="date"
                       id="start-date-{{ $section->id }}"
                       class="custom-date-input"
                       data-custom-id="{{ $section->id }}"
                       data-field="payload.items.0.start_date"
                       value="{{ $startIso ? \Carbon\Carbon::parse($startIso)->format('Y-m-d') : '' }}"
                       style="position:absolute;opacity:0;pointer-events:none;width:0;height:0;">

                <input type="date"
                       id="end-date-{{ $section->id }}"
                       class="custom-date-input"
                       data-custom-id="{{ $section->id }}"
                       data-field="payload.items.0.end_date"
                       value="{{ $endIso ? \Carbon\Carbon::parse($endIso)->format('Y-m-d') : '' }}"
                       style="position:absolute;opacity:0;pointer-events:none;width:0;height:0;">
            </div>
        </div>

        {{-- BARIS 3: deskripsi --}}
        <div style="border:1px dashed #bbb;border-radius:4px;padding:8px 10px;margin-top:4px;">
            <div class="editable"
                 contenteditable="true"
                 spellcheck="false"
                 data-custom-id="{{ $section->id }}"
                 data-field="payload.items.0.description"
                 style="min-height:40px;">
                {{ $item['description'] }}
            </div>
        </div>
    </div>

    <style>
        .section.custom-text .custom-date-body {
            text-align: left;
        }
        .section.custom-text .custom-date-pill:hover {
            background:#f3f4ff;
        }
    </style>
</div>
