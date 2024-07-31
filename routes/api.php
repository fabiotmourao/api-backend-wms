<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstrategiaWmsController;


Route::post('/estrategiaWMS', [EstrategiaWmsController::class, 'store']);
Route::get('/estrategiaWMS/{cdEstrategia}/{dsHora}/{dsMinuto}/prioridade', [EstrategiaWmsController::class, 'show']);

