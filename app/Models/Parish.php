<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parish extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saving(function (Parish $parish) {
            $parish->male_count = intval($parish->male_count ?? 0);
            $parish->female_count = intval($parish->female_count ?? 0);
            $parish->camp_contingent_count = $parish->male_count + $parish->female_count;
        });
    }

    public function consolidatedResults()
    {
        return $this->hasMany(ConsolidatedResult::class);
    }
}