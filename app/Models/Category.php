<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Category extends Model {
    protected $guarded = [];
    protected $casts = [
        'judging_criteria' => 'array',
        'rules' => 'array',
    ];
    public function consolidatedResults() { return $this->hasMany(ConsolidatedResult::class); }
}