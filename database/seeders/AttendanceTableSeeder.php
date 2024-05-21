<?php
namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AttendanceTableSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $classrooms = Classroom::with('courses')->get();
        $courseNames = ['Math', 'English', 'Science', 'Khmer', 'Japanese'];
        $schedule = [
            'Monday' => 'Math',
            'Tuesday' => 'English',
            'Wednesday' => 'Science',
            'Thursday' => 'Khmer',
            'Friday' => 'Japanese'
        ];

        foreach ($classrooms as $classroom) {
            $studentsInClassroom = $classroom->students;
            $courses = $classroom->courses->whereIn('name', $courseNames);

            foreach ($studentsInClassroom as $student) {
                foreach ($schedule as $day => $courseName) {
                    $course = $courses->where('name', $courseName)->first();
                    if ($course) {
                        $date = Carbon::now()->next(Carbon::parse($day)->dayOfWeek);

                        // Create or update attendance record
                        Attendance::updateOrCreate([
                            'student_id' => $student->id,
                            'course_id' => $course->id,
                            'classroom_id' => $classroom->id,
                            'date' => $date,
                        ], [
                            'attended' => rand(0, 1) ? 'present' : 'absent',
                        ]);
                    } else {
                        echo "Course '$courseName' not found in classroom {$classroom->id}.\n";
                    }
                }
            }
        }
    }
}

