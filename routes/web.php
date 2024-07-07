<?php

use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\ProfileController;




use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;




use App\Http\Controllers\TeacherController;
use App\Models\Classroom;
use App\Models\Major;
use App\Models\Student;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::resource('/classroom', ClassroomController::class)->middleware('auth');
Route::get('/classroom/{classroom}/attendance', [ClassroomController::class, 'showAttendancePage'])
    ->name('classroom.attendance')
    ->middleware('auth');

Route::post('/classroom/{classroom}/attendance', [ClassroomController::class, 'markAttendance'])
    ->middleware('auth');

Route::resource('/student', StudentController::class)->middleware('auth');
Route::get('/student/{student}/edit/{classroom}', [StudentController::class, 'edited'])->name('student.edit')->middleware('auth');
Route::delete('classrooms/{classroom}/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
Route::delete('/students', [StudentController::class, 'bulkDestroy'])->name('students.bulkDestroy');

Route::middleware('auth')->group(function () {
    Route::resource('/student', StudentController::class);
    Route::get('/student/{student}/edit/{classroom}', [StudentController::class, 'edited'])->name('student.edited');
    Route::put('/students/{student}/', [StudentController::class,'updateGlobally'])->name('students.updateGlobally');
    Route::delete('classrooms/{classroom}/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::delete('/students', [StudentController::class, 'bulkDestroy'])->name('students.bulkDestroy');

    Route::delete('/students/{student}', [StudentController::class,'destroyGlobally'])->name('students.destroyGlobally');

    Route::get('/classroom/{classroom}/importStudent', [StudentController::class,'importStudent'])->name('students.importStudent');
    Route::post('/students/bulkInsert', [StudentController::class,'handleBulkInsert'])->name('students.bulkInsert');


});

Route::middleware('auth')->group(function () {
    Route::post('/courses', [CourseController::class, 'storeGlobally'])->name('course.storeGlobally');
    Route::get('/course', [CourseController::class, 'index'])->name('course.index');
    Route::post('/course', [CourseController::class, 'store']);
    Route::get('/classroom/{classroomId}/course', [CourseController::class, 'getCoursesByClassroom'])->name('classroom.courses');
    Route::delete('/course/{course}', [CourseController::class, 'destroy']);
    Route::patch('/course/{course}', [CourseController::class, 'update'])->name('course.update');

    Route::get('/course/{course}', [CourseController::class,'edit'])->name('course.edit');

    Route::delete('/courses/{course}', [CourseController::class,'bulkDestroy'])->name('course.bulkDestroy');

    Route::delete('/course/{course}/classroom/{classroom}', [CourseController::class, 'detachFromClassroom'])->name('course.detachFromClassroom');
});

Route::middleware('auth')->group(function () {
    Route::get('/faculty', [FacultyController::class,'index'])->name('faculty.index');
    Route::delete('/faculty/{faculty}', [FacultyController::class,'destroy'])->name('faculty.destroy');
    Route::get('/faculty/{facultyId}', [FacultyController::class,'edit'])->name('faculty.edit');
    Route::patch('/faculty/{faculty}', [FacultyController::class,'update'])->name('faculty.update');
    Route::post('/faculty', [FacultyController::class,'store'])->name('faculty.store');
    Route::delete('/facultys', [FacultyController::class,'bulkDestroy'])->name('faculty.bulkDestroy');

});

Route::middleware('auth')->group(function () {
    Route::get('/major', [MajorController::class,'index'])->name('major.index');
    Route::delete('/major/{major}', [MajorController::class,'destroy'])->name('major.destroy');
    Route::get('/major/{majorId}', [MajorController::class,'edit'])->name('major.edit');
    Route::patch('/major/{major}', [MajorController::class,'update'])->name('major.update');
    Route::post('/major', [MajorController::class,'store'])->name('major.store');
    Route::delete('/majors', [MajorController::class, 'bulkDestroy'])->name('majors.bulkDestroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/teacher', [TeacherController::class,'index'])->name('teacher.index');

    Route::get('/teacher/{teacher}', [TeacherController::class,'edit'])->name('teacher.edit');
    Route::post('/teacher', [TeacherController::class,'store'])->name('teacher.store');
    Route::post('/teachers', [TeacherController::class,'storeGlobally'])->name('teacher.storeGlobally');
    Route::get('/classroom/{classroomId}/teacher', [TeacherController::class,'show'])->name('classroom.teachers');
    Route::delete('/teacher/{teacher}', [TeacherController::class,'destroy'])->name('teacher.destroy');
    Route::delete('/teachers/{teacher}', [TeacherController::class,'bulkDestroy'])->name('teacher.bulkDestroy');

    Route::delete('/teacher/{teacher}/classroom/{classroom}', [TeacherController::class, 'detachFromClassroom'])->name('teacher.detachFromClassroom');
    Route::patch('/teacher/{teacher}', [TeacherController::class,'update'])->name('teacher.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/schedule', [ScheduleController::class,'store'])->name('schedule.store');
    Route::patch('/schedule/{schedule}', [ScheduleController::class,'update'])->name('schedule.update');
    Route::delete('/schedule/{schedule}', [ScheduleController::class,'destroy'])->name('schedule.destroy');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
