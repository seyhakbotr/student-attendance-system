<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    use HasFactory;
    protected $fillable = ['semester'];
    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }


}
