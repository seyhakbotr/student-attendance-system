<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $guarded = ['name', 'gender'];

    public function courses()
    {
        return $this->belongsToMany(Course::class)->withPivot('attended');
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_student');
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
}
