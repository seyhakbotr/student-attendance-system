<?php

use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\ProfileController;




use App\Http\Controllers\StudentController;




use App\Models\Classroom;
use App\Models\Major;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::resource('/classroom', ClassroomController::class)->middleware('auth');
Route::get('/classroom/{classroom}/attendance', [ClassroomController::class, 'showAttendancePage'])
    ->name('classroom.attendance')
    ->middleware('auth');

Route::post('/classroom/{classroom}/attendance', [ClassroomController::class, 'markAttendance'])
    ->middleware('auth');

Route::resource('/student', StudentController::class)->middleware('auth');
Route::get('/student/{student}/edit/{classroom}', [StudentController::class, 'edited'])->name('student.edit')->middleware('auth');


Route::middleware('auth')->group(function () {
    Route::post('/course', [CourseController::class, 'store']);
    Route::get('/classroom/{classroomId}/course', [CourseController::class, 'getCoursesByClassroom'])->name('classroom.courses');
    Route::delete('/course/{course}', [CourseController::class, 'destroy']);
    Route::patch('/course/{course}', [CourseController::class, 'update'])->name('course.update');
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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
