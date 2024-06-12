<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Student;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $major = Major::all();
        $faculty = Faculty::all();
        $course = Course::all();
        $student = Student::all();
        $classroom = Classroom::all();
    }
}
