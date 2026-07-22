<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $table = 'results';
    protected $guarded = ['id'];

    public function assessment()
    {
        return $this->hasMany(Assessment::class, 'assessment_id', 'id');
    }
}
