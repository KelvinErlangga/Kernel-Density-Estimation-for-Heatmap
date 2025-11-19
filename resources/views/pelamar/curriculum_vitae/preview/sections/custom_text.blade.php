<div class="section custom-text"
     data-key="custom"
     data-custom-id="{{ $section->id }}"
     style="position:relative; padding:8px 0;">

  <span class="drag-indicator"
        style="display:none;position:absolute;left:8px;top:12px;font-size:20px;color:#3538cd;cursor:grab;z-index:10;">&#x2630;</span>

  <span class="delete-indicator custom-delete"
        data-custom-id="{{ $section->id }}"
        style="display:none;position:absolute;right:8px;top:12px;font-size:20px;color:#e74c3c;cursor:pointer;z-index:10;"
        title="Hapus section">&#128465;</span>

  <h3 contenteditable="true"
      class="editable custom-title"
      data-custom-id="{{ $section->id }}"
      data-field="section_title">
    {{ $section->section_title ?? 'PROJECT' }}
  </h3>

  <div contenteditable="true"
       class="editable custom-body"
       data-custom-id="{{ $section->id }}"
       data-field="payload.body">
    {!! data_get($section->payload, 'body', 'Tulis deskripsi di sini…') !!}
  </div>
</div>

<style>
  .section.custom-text .custom-title{
    color:#111;
    margin:0 0 6px 0;
    font-size:18px;
    text-align:center;
    border-bottom:1px solid #111;
    padding-bottom:6px;
    width:88%;
    max-width:760px;
    margin-left:auto;
    margin-right:auto;
  }

  .section.custom-text .custom-body{
    color:#000;
    font-size:14px;
    line-height:1.5;
    text-align:justify;
    width:88%;
    max-width:760px;
    margin:6px auto 0;
  }

  .section.custom-text hr{ display:none !important; }
</style>
