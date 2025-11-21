<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTemplateCurriculumVitaeRequest;
use App\Http\Requests\UpdateTemplateCurriculumVitaeRequest;
use App\Models\TemplateCurriculumVitae;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemplateCurriculumVitaeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua template termasuk yang dihapus (trashed) jika ingin menampilkan tombol restore
        $templateCurriculumVitaes = TemplateCurriculumVitae::withTrashed()->get();

        return view('admin.template_curriculum_vitae.index', compact('templateCurriculumVitaes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.template_curriculum_vitae.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreTemplateCurriculumVitaeRequest $request)
    // {
    //     DB::transaction(function () use ($request) {
    //         $validated = $request->validated();

    //         if ($request->hasFile('thumbnail_curriculum_vitae')) {
    //             $thumbnailPath = $request->file('thumbnail_curriculum_vitae')->store('thumbnail_curriculum_vitae', 'public');
    //             $validated['thumbnail_curriculum_vitae'] = $thumbnailPath;
    //         }

    //         TemplateCurriculumVitae::create($validated);
    //     });

    //     return redirect()->route('admin.template_curriculum_vitae.index')
    //                      ->with('success', 'Template CV berhasil ditambahkan.');
    // }

    public function store(StoreTemplateCurriculumVitaeRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            // Upload thumbnail jika ada
            if ($request->hasFile('thumbnail_curriculum_vitae')) {
                $thumbnailPath = $request->file('thumbnail_curriculum_vitae')
                    ->store('thumbnails', 'public');
                $validated['thumbnail_curriculum_vitae'] = $thumbnailPath;
            }

            // layout_json wajib diisi, default array kosong jika null
            $validated['layout_json'] = $request->layout_json ?? json_encode([]);

            // style_json wajib diisi, default array kosong jika null
            $validated['style_json'] = $request->style_json ?? json_encode([]);

            // template_type wajib diisi (ambil dari form), default 'ats' kalau null
            $validated['template_type'] = $request->template_type ?? 'ats';

            // Simpan ID admin yang membuat
            $validated['created_by'] = auth()->id();

            TemplateCurriculumVitae::create($validated);
        });

        return redirect()->route('admin.template_curriculum_vitae.index')
            ->with('success', 'Template CV berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TemplateCurriculumVitae $templateCurriculumVitae)
    {
        return view('admin.template_curriculum_vitae.edit', compact('templateCurriculumVitae'));
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdateTemplateCurriculumVitaeRequest $request, TemplateCurriculumVitae $templateCurriculumVitae)
    // {
    //     DB::transaction(function () use ($request, $templateCurriculumVitae) {
    //         $validated = $request->validated();

    //         if ($request->hasFile('thumbnail_curriculum_vitae')) {
    //             $thumbnailPath = $request->file('thumbnail_curriculum_vitae')->store('thumbnail_curriculum_vitae', 'public');
    //             $validated['thumbnail_curriculum_vitae'] = $thumbnailPath;
    //         }

    //         $templateCurriculumVitae->update($validated);
    //     });

    //     return redirect()->route('admin.template_curriculum_vitae.index')
    //         ->with('success', 'Template CV berhasil diperbarui.');
    // }

    public function update(UpdateTemplateCurriculumVitaeRequest $request, TemplateCurriculumVitae $templateCurriculumVitae)
    {
        DB::transaction(function () use ($request, $templateCurriculumVitae) {
            $validated = $request->validated();

            if ($request->hasFile('thumbnail_curriculum_vitae')) {
                $thumbnailPath = $request->file('thumbnail_curriculum_vitae')
                    ->store('thumbnail_curriculum_vitae', 'public');
                $validated['thumbnail_curriculum_vitae'] = $thumbnailPath;
            }

            // update layout JSON jika ada
            if ($request->has('layout_json')) {
                $validated['layout_json'] = $request->layout_json;
            }

            $templateCurriculumVitae->update($validated);
        });

        return redirect()->route('admin.template_curriculum_vitae.index')
            ->with('success', 'Template CV berhasil diperbarui.');
    }

    /**
     * Soft delete / nonaktifkan template CV.
     */
    public function destroy(TemplateCurriculumVitae $templateCurriculumVitae)
    {
        DB::transaction(function () use ($templateCurriculumVitae) {
            $templateCurriculumVitae->delete(); // soft delete
        });

        return redirect()->route('admin.template_curriculum_vitae.index')
            ->with('success', 'Template CV berhasil dinonaktifkan.');
    }

    /**
     * Restore template CV yang di-soft delete.
     */
    public function restore($id)
    {
        $templateCurriculumVitae = TemplateCurriculumVitae::withTrashed()->findOrFail($id);
        $templateCurriculumVitae->restore();

        return redirect()->route('admin.template_curriculum_vitae.index')
            ->with('success', 'Template CV berhasil diaktifkan kembali.');
    }
    /**
     * Halaman editor visual (GrapesJS) khusus template kreatif.
     */
    public function designer(TemplateCurriculumVitae $templateCurriculumVitae)
    {
        // jaga-jaga: paksa type kreatif
        if ($templateCurriculumVitae->template_type !== 'kreatif') {
            abort(404, 'Editor visual hanya untuk template kreatif.');
        }

        // Ambil layout & style dari JSON yang sudah ada
        $layout = is_array($templateCurriculumVitae->layout_json)
            ? $templateCurriculumVitae->layout_json
            : [];

        $style = is_array($templateCurriculumVitae->style_json)
            ? $templateCurriculumVitae->style_json
            : [];

        $isGrapes = isset($layout['engine']) && $layout['engine'] === 'grapesjs';

        $initialHtml = $isGrapes ? ($layout['html'] ?? '') : '';
        $initialCss  = $isGrapes ? ($style['css'] ?? '') : '';

        return view('admin.template_curriculum_vitae.grapes_editor', [
            'template'    => $templateCurriculumVitae,
            'initialHtml' => $initialHtml,
            'initialCss'  => $initialCss,
        ]);
    }

    /**
     * Simpan hasil dari editor GrapesJS (HTML+CSS) ke layout_json & style_json.
     */
    public function updateDesigner(Request $request, TemplateCurriculumVitae $templateCurriculumVitae)
    {
        $data = $request->validate([
            'template_curriculum_vitae_name' => 'required|string|max:255',
            'html' => 'required|string',
            'css'  => 'nullable|string',
        ]);

        $templateCurriculumVitae->update([
            'template_curriculum_vitae_name' => $data['template_curriculum_vitae_name'],
            'template_type'                  => 'kreatif', // pastikan flag kreatif
            'layout_json' => json_encode([
                'engine' => 'grapesjs',
                'html'   => $data['html'],
            ]),
            'style_json'  => json_encode([
                'engine' => 'grapesjs',
                'css'    => $data['css'] ?? '',
            ]),
        ]);

        return redirect()
            ->route('admin.template_curriculum_vitae.index')
            ->with('success', 'Template kreatif berhasil diperbarui dengan editor visual.');
    }

    public function editVisual(TemplateCurriculumVitae $template)
    {
        // optional: pastikan hanya untuk kreatif
        if (strtolower($template->template_type) !== 'kreatif') {
            abort(404);
        }

        // view builder (bisa pakai layout yang sama dengan create, tapi untuk update)
        return view('admin.template_curriculum_vitae.edit_visual', [
            'template' => $template,
        ]);
    }
}
