<?php

use App\Http\Controllers\SpinWheelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('spin-wheel')->name('spin-wheel.')->group(function () {
    Route::get('/', [SpinWheelController::class, 'index'])->name('index');
    Route::post('/start', [SpinWheelController::class, 'start'])->name('start');
    Route::post('/save-result', [SpinWheelController::class, 'saveResult'])->name('save-result');
});
