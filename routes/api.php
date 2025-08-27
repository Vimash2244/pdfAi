<?php

use App\Http\Controllers\Api\PdfParseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function () {
    Route::post('/pdf-parse', [PdfParseApiController::class, 'parse']);
    Route::get('/pdf-parse/{id}/status', [PdfParseApiController::class, 'status']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
