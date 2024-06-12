<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone'];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
    public function classSchedule()
    {
        return $this->hasMany(ClassSchedule::class);
    }
}
