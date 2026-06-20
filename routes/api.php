<?php

use App\Http\Controllers\Api\PrinterBridgeController;
use Illuminate\Support\Facades\Route;

Route::prefix('bridge')
    ->middleware('terminal.bridge')
    ->group(function () {
        Route::get('me', [PrinterBridgeController::class, 'me']);
        Route::post('heartbeat', [PrinterBridgeController::class, 'heartbeat']);

        Route::get('print-jobs/next', [PrinterBridgeController::class, 'nextPrintJob']);
        Route::post('print-jobs/{print_job}/printed', [PrinterBridgeController::class, 'markPrinted']);
        Route::post('print-jobs/{print_job}/failed', [PrinterBridgeController::class, 'markFailed']);
    });