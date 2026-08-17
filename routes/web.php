<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProgramTrackerController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\JudgeController;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Main Filament Login Panel for All Users)
|--------------------------------------------------------------------------
*/
Route::redirect('/login', '/admin/login')->name('login');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Public Routes (Read-Only Portal & Results)
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/program');
Route::get('/program', [ProgramTrackerController::class, 'index'])->name('program.index');
Route::get('/registration', [RegistrationController::class, 'index'])->name('registration.index');
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
Route::get('/leaderboard/big-screen', [LeaderboardController::class, 'bigScreen'])->name('leaderboard.big_screen');

/*
|--------------------------------------------------------------------------
| 3-Judge Adjudication Workstation (Restricted to Authenticated Judges & Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('judge')->name('judge.')->group(function () {
    Route::get('/', [JudgeController::class, 'index'])->name('index');
    Route::get('/scoresheet/{category}/{parish}', [JudgeController::class, 'scoreSheet'])->name('scoresheet');
    Route::post('/submit', [JudgeController::class, 'submitScore'])->name('submit');
});