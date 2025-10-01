<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HiringController;
use App\Http\Controllers\JobController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('/job-skills', [JobController::class, 'getJobSkills']);

// Route::get('/hirings', [HiringController::class, 'all']);        // semua
Route::get('/hirings/{id}', [HiringController::class, 'show']); // detail
// Route::get('/hirings/heatmap', [HiringController::class, 'heatmapData']); // khusus lat-lng

Route::get('/lokasi-lowongan', [HiringController::class, 'heatmapData']);
