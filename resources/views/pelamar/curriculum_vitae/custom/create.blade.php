<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Isi Custom Section | CVRE GENERATE</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="{{asset('assets/icons/logo.svg')}}" type="image/x-icon">
</head>
<body class="bg-gray-50" style="font-family:'Poppins',sans-serif">
<div class="max-w-3xl mx-auto py-10">
  <div class="mb-6">
    <a href="{{ route('pelamar.curriculum_vitae.custom.index', $curriculumVitaeUser->id) }}" class="text-blue-700">&larr; Kembali</a>
  </div>

  <h1 class="text-2xl font-semibold text-blue-800 mb-6">Isi Custom Section</h1>

  <form method="POST" action="{{ route('pelamar.curriculum_vitae.custom.store', [$curriculumVitaeUser->id, $sectionKey]) }}">
    @csrf

    <div class="bg-white p-5 rounded border shadow-sm space-y-4">
      <div>
        <label class="block text-sm text-gray-600 mb-1">Judul Section (opsional, override)</label>
        <input type="text" name="section_title" value="{{ old('section_title', $defaultTitle) }}"
               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring" />
      </div>

      <div>
        <label class="block text-sm text-gray-600 mb-1">Subjudul (opsional)</label>
        <input type="text" name="subtitle" value="{{ old('subtitle', $defaultSubtitle) }}"
               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring" />
      </div>

      <hr>

      <div class="flex items-center justify-between">
        <div class="text-lg font-semibold text-gray-800">Item</div>
        <button type="button" id="btnAdd" class="px-3 py-2 bg-blue-600 text-white rounded text-sm">+ Tambah Item</button>
      </div>

      <div id="items" class="space-y-4">
        <!-- container item -->
      </div>

      <button type="submit" class="w-full py-3 bg-blue-700 text-white rounded hover:bg-blue-800">
        Simpan
      </button>
    </div>
  </form>
</div>

<script>
(function(){
  const itemsEl = document.getElementById('items');
  const addBtn = document.getElementById('btnAdd');

  function row(idx, data = {}) {
    const t = document.createElement('div');
    t.className = "p-4 border rounded bg-gray-50 space-y-3";
    t.innerHTML = `
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Judul</label>
          <input name="items[${idx}][title]" value="${data.title ?? ''}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Meta (kanan, ops: kota/tanggal)</label>
          <input name="items[${idx}][meta]" value="${data.meta ?? ''}" class="w-full border rounded px-3 py-2">
        </div>
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Deskripsi (boleh HTML)</label>
        <textarea name="items[${idx}][desc]" rows="3" class="w-full border rounded px-3 py-2">${data.desc ?? ''}</textarea>
      </div>
      <div class="text-right">
        <button type="button" class="px-3 py-1 bg-red-600 text-white rounded text-sm btn-remove">Hapus</button>
      </div>
    `;
    t.querySelector('.btn-remove').addEventListener('click', ()=> t.remove());
    return t;
  }

  let i = 0;
  addBtn.addEventListener('click', ()=> {
    itemsEl.appendChild(row(i++));
  });

  // minimal satu baris default
  addBtn.click();
})();
</script>
</body>
</html>
