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

Route::get('/', 'PagesController@index')->name('index');
Route::get('/about', 'PagesController@about')->name('about');

// Posts
Route::resource('posts', 'PostsController');

//Auth::routes();

// Authentication
Route::get('/login', 'LoginController@login')->name('login');
Route::post('/login', 'LoginController@doLogin');
Route::get('/register', 'RegisterController@register')->name('register');
Route::post('/register', 'RegisterController@doRegister');
Route::get('/password/request', 'PasswordController@request')->name('password.request');
Route::post('/password/request', 'PasswordController@doRequest');
Route::get('/password/reset/{token?}', 'PasswordController@reset')->name('password.reset');
Route::post('/password/reset', 'PasswordController@doReset');

// Dashboard
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
