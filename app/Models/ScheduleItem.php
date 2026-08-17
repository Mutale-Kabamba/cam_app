<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ScheduleItem extends Model {
    protected $guarded = [];
    public function parish() { return $this->belongsTo(Parish::class); }
    public function category() { return $this->belongsTo(Category::class); }
}