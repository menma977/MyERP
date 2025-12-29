<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [
        'app' => config('app.name'),
        'version' => '1.0.0',
        'status' => 'OK',
    ];
});
