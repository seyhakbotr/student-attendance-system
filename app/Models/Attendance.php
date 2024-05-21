<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['attended', 'date'];
    const ATTENDED_PRESENT = 'present';
    const ATTENDED_ABSENT = 'absent';

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function isPresent()
    {
        return $this->attended === self::ATTENDED_PRESENT;
    }

    public function isAbsent()
    {
        return $this->attended === self::ATTENDED_ABSENT;
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
