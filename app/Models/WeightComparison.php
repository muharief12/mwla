<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightComparison extends Model
{
    use HasFactory;

    protected $table = 'weight_comparisons';
    protected $guarded = ['id'];

    public function assessment()
    {
        return $this->hasMany(Assessment::class, 'assessment_id', 'id');
    }
}
