<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $table = 'assessments';
    protected $guarded = ['id'];

    public function rating_scores()
    {
        return $this->hasMany(RatingScore::class, 'assessment_id');
    }

    public function weight_comparisons()
    {
        return $this->hasMany(WeightComparison::class, 'assessment_id');
    }

    public function result()
    {
        return $this->hasOne(RatingScore::class, 'assessment_id');
    }
}
