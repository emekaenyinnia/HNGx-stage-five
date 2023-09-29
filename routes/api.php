<?php

use App\Http\Controllers\RecordController;
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

Route::post('/upload', [RecordController::class, 'uploadRecord']);
Route::get('/{name}', [RecordController::class, 'getRecord' ]);
Route::get('/records/all', [RecordController::class, 'getAllRecord']);
Route::delete('/record/{name}', [RecordController::class, 'deleteRecord']);