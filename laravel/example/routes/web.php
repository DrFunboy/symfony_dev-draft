<?php
use Illuminate\Support\Facades\Route;

Route::get('/{route?}', function ($route = null) {
    return view('homepage', ['route' => $route]);
})->where('route', '.*');

Route::get('/user/{id}/profile', function ($id) {
    //
})->name('profile');
