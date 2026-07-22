<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';
    protected $guarded = ['id'];

    public function lecture()
    {
        return $this->belongsTo(User::class, 'lecture_id', 'id');
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'course_id');
    }
}
