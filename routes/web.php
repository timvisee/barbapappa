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
Route::get('/license', 'PagesController@license')->name('license');
Route::get('/language/{locale?}', 'PagesController@language')->name('language');

// Dashboard route
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

// Authentication routes
Route::get('/login', 'LoginController@login')->name('login');
Route::post('/login', 'LoginController@doLogin');
Route::get('/register', 'RegisterController@register')->name('register');
Route::post('/register', 'RegisterController@doRegister');
Route::get('/logout', 'LogoutController@logout')->name('logout');
Route::prefix('/password')->group(function() {
    Route::get('/change', 'PasswordChangeController@change')->name('password.change');
    Route::post('/change', 'PasswordChangeController@doChange')->name('password.change');
    Route::get('/request', 'PasswordForgetController@request')->name('password.request');
    Route::post('/request', 'PasswordForgetController@doRequest');
    Route::get('/reset/{token?}', 'PasswordResetController@reset')->name('password.reset');
    Route::post('/reset', 'PasswordResetController@doReset');
});

// Email routes
Route::prefix('/email/verify')->group(function() {
    Route::get('/{token?}', 'EmailVerifyController@verify')->name('email.verify');
    Route::post('/', 'EmailVerifyController@doVerify');
});

// Account routes
Route::prefix('/account/{userId?}')->middleware(['selectUser'])->group(function() {
    Route::get('/', 'AccountController@show')->name('account');
    Route::prefix("/emails")->group(function() {
        Route::get('/', 'EmailController@show')->name('account.emails');
        Route::get('/new', 'EmailController@create')->name('account.emails.create');
        Route::post('/new', 'EmailController@doCreate');
        Route::get('/reverify/{emailId}', 'EmailController@reverify')->name('account.emails.reverify');
        Route::get('/delete/{emailId}', 'EmailController@delete')->name('account.emails.delete');
    });
});

// Profile routes
Route::prefix('/profile')->middleware(['selectUser'])->group(function() {
    Route::get('/{userId}/edit', 'ProfileController@edit')->name('profile.edit');
    Route::put('/{userId}', 'ProfileController@update')->name('profile.update');
});

// Community routes
Route::prefix('/c')->group(function() {
    Route::get('/', 'CommunityController@overview')->name('community.overview');
    Route::prefix('/{communityId}')->middleware(['selectCommunity'])->group(function() {
        Route::get('/', 'CommunityController@show')->name('community.show');
        // Route::prefix("/emails")->group(function() {
        //     Route::get('/', 'EmailController@show')->name('account.emails');
        //     Route::get('/new', 'EmailController@create')->name('account.emails.create');
        //     Route::post('/new', 'EmailController@doCreate');
        //     Route::get('/reverify/{emailId}', 'EmailController@reverify')->name('account.emails.reverify');
        //     Route::get('/delete/{emailId}', 'EmailController@delete')->name('account.emails.delete');
        // });
    });
});

// Bar routes
Route::prefix('/b')->group(function() {
    Route::get('/', 'BarController@overview')->name('bar.overview');
    Route::prefix('/{barId}')->middleware(['selectBar'])->group(function() {
        Route::get('/', 'BarController@show')->name('bar.show');
        // Route::prefix("/emails")->group(function() {
        //     Route::get('/', 'EmailController@show')->name('account.emails');
        //     Route::get('/new', 'EmailController@create')->name('account.emails.create');
        //     Route::post('/new', 'EmailController@doCreate');
        //     Route::get('/reverify/{emailId}', 'EmailController@reverify')->name('account.emails.reverify');
        //     Route::get('/delete/{emailId}', 'EmailController@delete')->name('account.emails.delete');
        // });
    });
});

// TODO: Routes to implement
Route::get('/email/preferences', 'DashboardController@index')->name('email.preferences');
