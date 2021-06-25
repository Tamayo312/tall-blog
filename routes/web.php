<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});

Route::get('category', function ($category) {
    return view('welcome');
});

/*
 * Adding the middleware 'auth:sanctum' we ensure all the request to the following routes have to be authenticated.
 */
Route::group(['prefix' => 'dashboard', 'middleware' => 'auth:sanctum', 'verified'], function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('post/add', function () {
        return view('dashboard');
    });

    Route::get('category/add', function () {
        return view('dashboard');
    });
});
