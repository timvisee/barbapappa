<?php

use App\Perms\Builder\Builder as Perms;

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
Route::get('/license/raw', 'PagesController@licenseRaw')->name('license.raw');
Route::get('/language/{locale?}', 'PagesController@language')->name('language');

// TODO: remove this page after testing
Route::get('/secret', 'PagesController@about')->middleware(Perms::build()->app()->admin()->middleware());

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
        Route::delete('/delete/{emailId}', 'EmailController@doDelete')->name('account.emails.doDelete');
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
        // TODO: require community administrator
        Route::get('/edit', 'CommunityController@edit')->name('community.edit');
        Route::prefix('/members/')
            ->middleware(Perms::build()->app()->admin()->or()->community()->admin()->middleware())
            ->group(function() {
                Route::get('/', 'CommunityMemberController@index')->name('community.member.index');
                Route::get('/{memberId}', 'CommunityMemberController@show')->name('community.member.show');
                Route::get('/{memberId}/edit', 'CommunityMemberController@edit')->name('community.member.edit');
                Route::put('/{memberId}/edit', 'CommunityMemberController@doEdit')->name('community.member.doEdit');
                Route::get('/{memberId}/delete', 'CommunityMemberController@delete')->name('community.member.delete');
                Route::delete('/{memberId}/delete', 'CommunityMemberController@doDelete')->name('community.member.doDelete');
            });
        Route::put('/', 'CommunityController@update')->name('community.update');
        Route::get('/join', 'CommunityController@join')->name('community.join');
        Route::post('/join', 'CommunityController@doJoin')->name('community.doJoin');
        Route::get('/leave', 'CommunityController@leave')->name('community.leave');
        Route::post('/leave', 'CommunityController@doLeave')->name('community.doLeave');
    });
});

// Bar routes
Route::prefix('/b')->group(function() {
    Route::get('/', 'BarController@overview')->name('bar.overview');
    Route::prefix('/{barId}')->middleware(['selectBar'])->group(function() {
        Route::get('/', 'BarController@show')->name('bar.show');
        // TODO: require bar administrator
        Route::get('/edit', 'BarController@edit')->name('bar.edit');
        Route::prefix('/members/')
            ->middleware(Perms::build()->app()->admin()->or()->community()->admin()->or()->bar()->admin()->middleware())
            ->group(function() {
                Route::get('/', 'BarMemberController@index')->name('bar.member.index');
                Route::get('/{memberId}', 'BarMemberController@show')->name('bar.member.show');
                Route::get('/{memberId}/edit', 'BarMemberController@edit')->name('bar.member.edit');
                Route::put('/{memberId}/edit', 'BarMemberController@doEdit')->name('bar.member.doEdit');
                Route::get('/{memberId}/delete', 'BarMemberController@delete')->name('bar.member.delete');
                Route::delete('/{memberId}/delete', 'BarMemberController@doDelete')->name('bar.member.doDelete');
            });
        Route::put('/', 'BarController@update')->name('bar.update');
        Route::get('/join', 'BarController@join')->name('bar.join');
        Route::post('/join', 'BarController@doJoin')->name('bar.doJoin');
        Route::get('/leave', 'BarController@leave')->name('bar.leave');
        Route::post('/leave', 'BarController@doLeave')->name('bar.doLeave');
    });
});

// TODO: Routes to implement
Route::get('/email/preferences', 'DashboardController@index')->name('email.preferences');
