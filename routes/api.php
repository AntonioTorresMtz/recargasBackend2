<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecargasController;
use App\Http\Controllers\ServiciosController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/recargas', [RecargasController::class, 'index']);
Route::get('/servicios', [ServiciosController::class, 'index']);
Route::get('/recargas/ultimaRecarga', [RecargasController::class, 'ultimaRecarga']);
Route::get('/servicios/ultimoServicio', [ServiciosController::class, 'ultimoServicio']);
Route::post('/recargas/insertarDatos', [RecargasController::class, 'insertarDatos']);
Route::post('/servicios/insertarDatos', [ServiciosController::class, 'insertarDatos']);
