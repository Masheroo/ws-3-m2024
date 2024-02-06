<?php

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

Route::post('registration', [\App\Http\Controllers\AuthController::class, 'registration']);
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::get('logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/files', [\App\Http\Controllers\FileController::class, 'addFiles']);
    Route::patch('/files/{id}', [\App\Http\Controllers\FileController::class, 'renameFile']);
    Route::delete('/files/{id}', [\App\Http\Controllers\FileController::class, 'deleteFile']);
    Route::get('/files/{id}', [\App\Http\Controllers\FileController::class, 'getFile']);

    Route::post('/files/{id}/accesses', [\App\Http\Controllers\FileController::class, 'addAccessRights']);
    Route::delete('/files/{id}/accesses', [\App\Http\Controllers\FileController::class, 'deleteAccessRights']);
});
