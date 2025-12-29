<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [
        'app' => config('app.name'),
        'version' => config('app.version'),
        'status' => 'OK',
    ];
});
