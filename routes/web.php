<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

if (app()->environment('local', 'staging')) {
    Route::get('/chat/test', fn () => view('chat.test'));
}
