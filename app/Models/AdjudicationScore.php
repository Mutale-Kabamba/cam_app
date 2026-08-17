<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdjudicationScore extends Model
{
    protected $guarded = [];

    protected $casts = [
        'criteria_scores' => 'array',
        'song_titles_breakdown' => 'array',
        'raw_total_score' => 'float',
        'normalized_score' => 'float',
        'is_disqualified' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function parish()
    {
        return $this->belongsTo(Parish::class);
    }
}