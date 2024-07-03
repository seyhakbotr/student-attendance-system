<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPUnit\Framework\returnSelf;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = ['major_id', 'room_number', 'faculty_id','teacher_id','year_id','semester_id'];
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'classroom_student');
    }
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
    public function major()
    {
        return $this->belongsTo(Major::class);
    }
    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function years()
    {
        return $this->belongsTo(Year::class);
    }
    public function semesters()
    {
        return $this->belongsTo(Semester::class);
    }
}
