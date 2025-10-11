<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomSection extends Model
{
    protected $fillable = [
        'curriculum_vitae_user_id',
        'section_key',      // mis. 'custom_text'
        'section_title',    // judul yang tampil
        'subtitle',
        'payload',          // bebas (array), contoh: ['body' => '<p>..</p>']
        'sort_order',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function cv()
    {
        return $this->belongsTo(CurriculumVitaeUser::class, 'curriculum_vitae_user_id');
    }
}
