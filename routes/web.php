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

// Regular page routes
Route::get('/', 'PagesController@index')->name('index');
Route::get('/about', 'PagesController@about')->name('about');
Route::get('/contact', 'PagesController@contact')->name('contact');
Route::get('/terms', 'PagesController@terms')->name('terms');
Route::get('/privacy', 'PagesController@privacy')->name('privacy');

// Dashboard route
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

// Authentication routes
Route::get('/login', 'LoginController@login')->name('login');
Route::post('/login', 'LoginController@doLogin');
Route::get('/register', 'RegisterController@register')->name('register');
Route::post('/register', 'RegisterController@doRegister');
Route::get('/logout', 'LogoutController@logout')->name('logout');
Route::get('/password/change', 'PasswordChangeController@change')->name('password.change');
Route::post('/password/change', 'PasswordChangeController@doChange')->name('password.change');
Route::get('/password/request', 'PasswordForgetController@request')->name('password.request');
Route::post('/password/request', 'PasswordForgetController@doRequest');
Route::get('/password/reset/{token?}', 'PasswordResetController@reset')->name('password.reset');
Route::post('/password/reset', 'PasswordResetController@doReset');

// Email routes
Route::get('/email/verify/{token?}', 'EmailVerifyController@verify')->name('email.verify');
Route::post('/email/verify', 'EmailVerifyController@doVerify');

// TODO: Routes to implement
Route::get('/account', 'AccountController@overview')->name('account');
Route::get('/email/preferences', 'DashboardController@index')->name('email.preferences');

// Posts
Route::resource('posts', 'PostsController');
