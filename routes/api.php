<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecargasController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/recargas', [RecargasController::class, 'index']);
Route::post('/recargas/insertarDatos', [RecargasController::class, 'insertarDatos']);
