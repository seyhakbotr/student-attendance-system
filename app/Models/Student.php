<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $guarded = ['name', 'gender','year_id','semester_id'];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)->withPivot('attended');
    }

    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'classroom_student');
    }
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }
    public function years(): BelongsTo
    {
        return $this->belongsTo(Year::class);
    }
    public function semesters(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

}
