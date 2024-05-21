<?php

use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Models\Classroom;
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

route::resource('/student',StudentController::class)->middleware('auth');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
