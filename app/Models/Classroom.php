<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'room_number'];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'classroom_student');
    }
}
