<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', 'DefaultController@hi')->name('default');

Route::group(['prefix' => 'v1', 'namespace' => 'v1'], function () {

  Route::get('test', 'TestController@index')->name('test.index');

  Route::get('auth/logout', 'AuthController@logout')->name('auth.logout');
  Route::get('auth/refresh', 'AuthController@refresh')->name('auth.refresh');
  Route::get('auth/me', 'AuthController@me')->name('auth.me');

  Route::resource('board', 'BoardController')->except(['create', 'show', 'update', 'edit', 'store', 'destroy']);
  Route::resource('sprints', 'SprintController')->except(['create', 'update', 'edit', 'store', 'destroy']);
  Route::resource('issues', 'IssueController')->except(['create', 'update', 'edit', 'store', 'destroy']);

  Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('users', 'UserController')->except(['create', 'edit', 'store', 'destroy']);
    Route::resource('roles', 'RoleController')->except(['create', 'edit']);
    Route::resource('roles.users', 'RoleMemberController')->except(['create', 'edit', 'show']);
  });
});
