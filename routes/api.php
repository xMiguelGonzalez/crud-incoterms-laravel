<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncotermController;


use App\Models\IncotermType;
use App\Models\TrackingStep;


Route::apiResource('incoterms', IncotermController::class);



// SABER TIPO INCOTERMS

Route::get('/incoterm-types', function () {
    return response()->json([
        'success' => true, 
        'data' => IncotermType::all() 
    ]);
});



// BUSCAR TRACKING STEPS

Route::get('/tracking-steps', function () {
    return response()->json([
        'success' => true, 
        'data' => TrackingStep::orderBy('ORDER_NUM', 'asc')->get() 
    ]);
});