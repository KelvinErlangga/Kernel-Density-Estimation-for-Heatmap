<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Custom Section | CVRE GENERATE</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="icon" href="{{asset('assets/icons/logo.svg')}}" type="image/x-icon">
</head>
<body class="bg-gray-50" style="font-family:'Poppins',sans-serif">
  <div class="max-w-4xl mx-auto py-10">
    <div class="mb-6">
      <a href="{{ route('pelamar.curriculum_vitae.preview.index', $curriculumVitaeUser->id) }}" class="text-blue-700">&larr; Kembali ke Preview</a>
    </div>

    <h1 class="text-2xl font-semibold text-blue-800 mb-4">Custom Section</h1>
    <p class="text-gray-600 mb-6">Daftar section “custom” yang ditentukan oleh admin pada template ini.</p>

    <div class="space-y-4">
      @forelse($customConfigs as $cfg)
        @php
          $key = $cfg['key'] ?? '';
          $title = $cfg['title'] ?? ($cfg['section_title'] ?? 'Custom Section');
          $subtitle = $cfg['subtitle'] ?? null;
          $row = $existing->get($key); // App\Models\CustomSection atau null
        @endphp

        <div class="bg-white p-4 rounded border shadow-sm flex items-center justify-between">
          <div>
            <div class="text-lg font-semibold text-gray-900">{{ $title }}</div>
            @if($subtitle)
              <div class="text-sm text-gray-500">{{ $subtitle }}</div>
            @endif
            <div class="mt-1 text-xs {{ $row ? 'text-green-600':'text-yellow-600' }}">
              {{ $row ? 'Sudah diisi' : 'Belum diisi' }}
            </div>
          </div>

          <div class="flex items-center gap-2">
            @if(!$row)
              <a href="{{ route('pelamar.curriculum_vitae.custom.create', [$curriculumVitaeUser->id, $key]) }}"
                 class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Isi</a>
            @else
              <a href="{{ route('pelamar.curriculum_vitae.custom.edit', [$curriculumVitaeUser->id, $row->id]) }}"
                 class="px-3 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">Edit</a>
              <form method="POST" action="{{ route('pelamar.curriculum_vitae.custom.destroy', [$curriculumVitaeUser->id, $row->id]) }}"
                    onsubmit="return confirm('Hapus data section ini?')" class="inline">
                @csrf @method('DELETE')
                <button class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">Hapus</button>
              </form>
            @endif
          </div>
        </div>
      @empty
        <div class="p-6 bg-white rounded border text-gray-600">
          Tidak ada section “custom” pada template ini.
        </div>
      @endforelse
    </div>

    <div class="mt-8">
      <a href="{{ route('pelamar.curriculum_vitae.preview.index', $curriculumVitaeUser->id) }}"
         class="px-4 py-3 bg-blue-700 text-white rounded hover:bg-blue-800">Lanjut ke Preview</a>
    </div>
  </div>
</body>
</html>
