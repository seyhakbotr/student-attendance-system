<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPUnit\Framework\returnSelf;

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
    }
    public function classSchedule()
    {
        return $this->hasMany(ClassSchedule::class);
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
    public function major()
    {
        return $this->belongsTo(Major::class);
    }
}
