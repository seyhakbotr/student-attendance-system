<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $majorCount = Major::count();
        $facultyCount = Faculty::count();
        $courseCount = Course::count();
        $studentCount = Student::count();
        $classroomCount = Classroom::count();
        $teacherCount = Teacher::count();

        // SQLite specific query to extract month from created_at
        $studentCreationData = Student::selectRaw('COUNT(*) as count, strftime("%m", created_at) as month')
            ->groupBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Ensure all months are represented
        $studentCreationData = array_replace(array_fill(1, 12, 0), $studentCreationData);

        return Inertia::render('Dashboard', [
            'majorCount' => $majorCount,
            'facultyCount' => $facultyCount,
            'courseCount' => $courseCount,
            'studentCount' => $studentCount,
            'classroomCount' => $classroomCount,
            'teacherCount' => $teacherCount,
            'studentCreationData' => $studentCreationData
        ]);
    }
}
