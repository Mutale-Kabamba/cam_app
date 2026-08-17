<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Parish extends Model {
    protected $guarded = [];
    public function consolidatedResults() { return $this->hasMany(ConsolidatedResult::class); }
}