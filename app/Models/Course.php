<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $guarded = ['course_name'];
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class)->withPivot('attended');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }}
