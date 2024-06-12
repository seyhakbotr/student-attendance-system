<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    use HasFactory;
    protected $fillable = ['course_id', 'classroom_id', 'day','teacher_id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
