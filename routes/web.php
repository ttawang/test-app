<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'user'], function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('table', [UserController::class, 'table']);
    Route::post('simpan', [UserController::class, 'simpan']);
    Route::get('hapus', [UserController::class, 'hapus']);
    Route::get('get-data/{id}', [UserController::class, 'getData']);
});
