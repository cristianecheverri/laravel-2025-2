<?php

use App\Http\Controllers\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('test', function (Request $request) {
    return response()->json(['status' => true, 'message' => 'Hola mundo']);
});

Route::get('events', [EventController::class, 'index']);
Route::post('events', [EventController::class, 'store']);
