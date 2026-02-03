<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;

Route::get('/ping', function () {
    return response()->json([
        'ok' => true,
        'message' => 'pong'
    ]);
});

/*
|--------------------------------------------------------------------------
| Tasks API
|--------------------------------------------------------------------------
*/

Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::patch('/tasks/{task}', [TaskController::class, 'update']);
Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
