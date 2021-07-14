<?php

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

Route::group(['prefix' => 'v1', 'namespace' => 'v1'], function () {
  Route::get('auth/{provider}/login', 'AuthController@login')->name('auth.login');
  Route::get('auth/{provider}/callback', 'AuthController@callback')->name('auth.callback');
});
