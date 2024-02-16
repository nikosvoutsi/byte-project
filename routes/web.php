<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\DegreeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//candidates
Route::get('/', [CandidateController::class, 'index'])->name('index');
Route::post('/candidates', [CandidateController::class, 'store'])->name('candidates.store');
Route::post('/candidates/{id}/update', [CandidateController::class, 'update'])->name('candidates.edit');

Route::delete('/candidates/{id}', [CandidateController::class, 'delete'])->name('candidates.delete');

Route::get('/download-resume/{id}', [CandidateController::class, 'downloadResume'])->name('download.resume');

//degrees
Route::get('/degrees', [DegreeController::class, 'index'])->name('degrees.index');

Route::post('/store-degree', [DegreeController::class, 'store'])->name('degree.store');

Route::delete('/degrees/{id}', [DegreeController::class, 'delete'])->name('degree.delete');

Route::post('/degrees/{id}/update', [DegreeController::class, 'update'])->name('degrees.edit');