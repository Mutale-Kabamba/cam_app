<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsolidatedResult extends Model
{
    protected $guarded = [];

    protected $casts = [
        'adjudicators_average' => 'float',
        'time_penalty' => 'float',
        'final_score' => 'float',
        'rank' => 'integer',
        'championship_points' => 'integer',
        'is_finalized' => 'boolean',
    ];

    public function parish()
    {
        return $this->belongsTo(Parish::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}