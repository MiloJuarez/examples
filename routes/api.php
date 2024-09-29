<?php

use App\Http\Controllers\Examples\DropzoneController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('examples')->group(function () {
    Route::post('dropzone-upload', [DropzoneController::class, 'upload'])->name('dropzone.upload');
    Route::post('dropzone', [DropzoneController::class, 'saveForm'])->name('dropzone.store');
});
