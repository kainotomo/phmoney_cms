<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('cms::welcome');
})->name('phmoney.cms.welcome')->middleware('web');
