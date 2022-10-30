<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::get('/', [\App\Http\Controllers\Controller::class, 'list'])->name('list');
Route::prefix('{car}')->group(function () {
    Route::get('take', [\App\Http\Controllers\Controller::class, 'take'])
        ->name('take')
        ->missing(function (Request $request) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        });
    Route::get('give', [\App\Http\Controllers\Controller::class, 'give'])
        ->name('give')
        ->missing(function (Request $request) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        });
});
