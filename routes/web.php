<?php

use App\Http\Controllers\Index;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\LandingController;

Route::get('/', [Index::class, 'index'])->name('landing');
