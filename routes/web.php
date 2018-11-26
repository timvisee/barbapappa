<?php

use App\Perms\AppRoles;
use App\Perms\BarRoles;
use App\Perms\CommunityRoles;
use App\Http\Controllers\BarController;
use App\Http\Controllers\BarMemberController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\CommunityMemberController;
use App\Http\Controllers\EconomyController;
use App\Http\Controllers\EconomyCurrencyController;

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
Route::middleware('auth')->get('/last', 'PagesController@last')->name('last');

// Dashboard route
Route::middleware('auth')->get('/dashboard', 'DashboardController@index')->name('dashboard');

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
Route::prefix('/account/{userId?}')->middleware(['auth', 'selectUser'])->group(function() {
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
Route::prefix('/profile')->middleware(['auth', 'selectUser'])->group(function() {
    Route::get('/{userId}/edit', 'ProfileController@edit')->name('profile.edit');
    Route::put('/{userId}', 'ProfileController@update')->name('profile.update');
});

// Community routes
Route::prefix('/c')->middleware('auth')->group(function() {
    Route::get('/', 'CommunityController@overview')->name('community.overview');

    // Require app administrator to create a community
    Route::middleware(CommunityController::permsCreate()->middleware())->group(function() {
        Route::get('/create', 'CommunityController@create')->name('community.create');
        Route::post('/', 'CommunityController@doCreate')->name('community.doCreate');
    });

    // Specific
    Route::prefix('/{communityId}')->middleware(['selectCommunity'])->group(function() {
        // Show
        Route::get('/', 'CommunityController@show')->name('community.show');

        // Join/leave
        Route::get('/join', 'CommunityController@join')->name('community.join');
        Route::post('/join', 'CommunityController@doJoin')->name('community.doJoin');
        Route::get('/leave', 'CommunityController@leave')->name('community.leave');
        Route::post('/leave', 'CommunityController@doLeave')->name('community.doLeave');

        // Edit, require manage perms
        Route::middleware(CommunityController::permsManage()->middleware())->group(function() {
            Route::get('/edit', 'CommunityController@edit')->name('community.edit');
            Route::put('/', 'CommunityController@update')->name('community.update');
        });

        // Community members, require view perms
        Route::prefix('/members')->middleware(CommunityMemberController::permsView()->middleware())->group(function() {
            // Index
            Route::get('/', 'CommunityMemberController@index')->name('community.member.index');

            // Specific
            Route::prefix('/{memberId}')->group(function() {
                // Show
                Route::get('/', 'CommunityMemberController@show')->name('community.member.show');

                // Edit/delete, require manager perms
                Route::middleware(CommunityMemberController::permsManage()->middleware())->group(function() {
                    Route::get('/edit', 'CommunityMemberController@edit')->name('community.member.edit');
                    Route::put('/edit', 'CommunityMemberController@doEdit')->name('community.member.doEdit');
                    Route::get('/delete', 'CommunityMemberController@delete')->name('community.member.delete');
                    Route::delete('/delete', 'CommunityMemberController@doDelete')->name('community.member.doDelete');
                });
            });
        });

        // Community economies, require view perms
        Route::prefix('/economies')->middleware(EconomyController::permsView()->middleware())->group(function() {
            // Index
            Route::get('/', 'EconomyController@index')->name('community.economy.index');

            // Create, require manage perms
            Route::middleware(EconomyController::permsManage()->middleware())->group(function() {
                Route::get('/create', 'EconomyController@create')->name('community.economy.create');
                Route::post('/', 'EconomyController@doCreate')->name('community.economy.doCreate');
            });

            // Specific
            Route::prefix('/{economyId}')->group(function() {
                // Show
                Route::get('/', 'EconomyController@show')->name('community.economy.show');

                // Edit/delete, require manager perms
                Route::middleware(EconomyController::permsManage()->middleware())->group(function() {
                    Route::get('/edit', 'EconomyController@edit')->name('community.economy.edit');
                    Route::put('/edit', 'EconomyController@doEdit')->name('community.economy.doEdit');
                    Route::get('/delete', 'EconomyController@delete')->name('community.economy.delete');
                    Route::delete('/delete', 'EconomyController@doDelete')->name('community.economy.doDelete');
                });

                // Supported economy currencies
                Route::prefix('/currencies')->middleware(EconomyCurrencyController::permsView()->middleware())->group(function() {
                    // Index
                    Route::get('/', 'EconomyCurrencyController@index')->name('community.economy.currency.index');

                    // Create, require manage perms
                    Route::middleware(EconomyCurrencyController::permsManage()->middleware())->group(function() {
                        Route::get('/add', 'EconomyCurrencyController@create')->name('community.economy.currency.create');
                        Route::post('/', 'EconomyCurrencyController@doCreate')->name('community.economy.currency.doCreate');
                    });

                    // Specific
                    Route::prefix('/{economyCurrencyId}')->group(function() {
                        // Show
                        Route::get('/', 'EconomyCurrencyController@show')->name('community.economy.currency.show');

                        // Edit/delete, require manager perms
                        Route::middleware(EconomyCurrencyController::permsManage()->middleware())->group(function() {
                            Route::get('/edit', 'EconomyCurrencyController@edit')->name('community.economy.currency.edit');
                            Route::put('/edit', 'EconomyCurrencyController@doEdit')->name('community.economy.currency.doEdit');
                            Route::get('/remove', 'EconomyCurrencyController@delete')->name('community.economy.currency.delete');
                            Route::delete('/remove', 'EconomyCurrencyController@doDelete')->name('community.economy.currency.doDelete');
                        });
                    });
                });
            });
        });

        // Community user wallets, require view perms
        Route::prefix('/wallets')->group(function() {
            // Index
            Route::get('/', 'WalletController@index')->name('community.wallet.index');

            // Specific economy
            Route::prefix('/{economyId}')->group(function() {
                // List wallets
                Route::get('/', 'WalletController@list')->name('community.wallet.list');

                // Create
                Route::get('/create', 'WalletController@create')->name('community.wallet.create');
                Route::post('/create', 'WalletController@doCreate')->name('community.wallet.doCreate');

                // Specific
                Route::prefix('/{walletId}')->group(function() {
                    // Show
                    Route::get('/', 'WalletController@show')->name('community.wallet.show');

                    // // Edit/delete, require manager perms
                    // Route::middleware(EconomyController::permsManage()->middleware())->group(function() {
                    //     Route::get('/edit', 'EconomyController@edit')->name('community.economy.edit');
                    //     Route::put('/edit', 'EconomyController@doEdit')->name('community.economy.doEdit');
                    //     Route::get('/delete', 'EconomyController@delete')->name('community.economy.delete');
                    //     Route::delete('/delete', 'EconomyController@doDelete')->name('community.economy.doDelete');
                    // });

                    // // Supported economy currencies
                    // Route::prefix('/currencies')->middleware(EconomyCurrencyController::permsView()->middleware())->group(function() {
                    //     // Index
                    //     Route::get('/', 'EconomyCurrencyController@index')->name('community.economy.currency.index');

                    //     // Create, require manage perms
                    //     Route::middleware(EconomyCurrencyController::permsManage()->middleware())->group(function() {
                    //         Route::get('/add', 'EconomyCurrencyController@create')->name('community.economy.currency.create');
                    //         Route::post('/', 'EconomyCurrencyController@doCreate')->name('community.economy.currency.doCreate');
                    //     });

                    //     // Specific
                    //     Route::prefix('/{economyCurrencyId}')->group(function() {
                    //         // Show
                    //         Route::get('/', 'EconomyCurrencyController@show')->name('community.economy.currency.show');

                    //         // Edit/delete, require manager perms
                    //         Route::middleware(EconomyCurrencyController::permsManage()->middleware())->group(function() {
                    //             Route::get('/edit', 'EconomyCurrencyController@edit')->name('community.economy.currency.edit');
                    //             Route::put('/edit', 'EconomyCurrencyController@doEdit')->name('community.economy.currency.doEdit');
                    //             Route::get('/remove', 'EconomyCurrencyController@delete')->name('community.economy.currency.delete');
                    //             Route::delete('/remove', 'EconomyCurrencyController@doDelete')->name('community.economy.currency.doDelete');
                    //         });
                    //     });
                    // });
                });
            });

            // // Create, require manage perms
            // Route::middleware(EconomyController::permsManage()->middleware())->group(function() {
            //     Route::get('/create', 'EconomyController@create')->name('community.economy.create');
            //     Route::post('/', 'EconomyController@doCreate')->name('community.economy.doCreate');
            // });

            // // Specific
            // Route::prefix('/{economyId}')->group(function() {
            //     // Show
            //     Route::get('/', 'EconomyController@show')->name('community.economy.show');

            //     // Edit/delete, require manager perms
            //     Route::middleware(EconomyController::permsManage()->middleware())->group(function() {
            //         Route::get('/edit', 'EconomyController@edit')->name('community.economy.edit');
            //         Route::put('/edit', 'EconomyController@doEdit')->name('community.economy.doEdit');
            //         Route::get('/delete', 'EconomyController@delete')->name('community.economy.delete');
            //         Route::delete('/delete', 'EconomyController@doDelete')->name('community.economy.doDelete');
            //     });

            //     // Supported economy currencies
            //     Route::prefix('/currencies')->middleware(EconomyCurrencyController::permsView()->middleware())->group(function() {
            //         // Index
            //         Route::get('/', 'EconomyCurrencyController@index')->name('community.economy.currency.index');

            //         // Create, require manage perms
            //         Route::middleware(EconomyCurrencyController::permsManage()->middleware())->group(function() {
            //             Route::get('/add', 'EconomyCurrencyController@create')->name('community.economy.currency.create');
            //             Route::post('/', 'EconomyCurrencyController@doCreate')->name('community.economy.currency.doCreate');
            //         });

            //         // Specific
            //         Route::prefix('/{economyCurrencyId}')->group(function() {
            //             // Show
            //             Route::get('/', 'EconomyCurrencyController@show')->name('community.economy.currency.show');

            //             // Edit/delete, require manager perms
            //             Route::middleware(EconomyCurrencyController::permsManage()->middleware())->group(function() {
            //                 Route::get('/edit', 'EconomyCurrencyController@edit')->name('community.economy.currency.edit');
            //                 Route::put('/edit', 'EconomyCurrencyController@doEdit')->name('community.economy.currency.doEdit');
            //                 Route::get('/remove', 'EconomyCurrencyController@delete')->name('community.economy.currency.delete');
            //                 Route::delete('/remove', 'EconomyCurrencyController@doDelete')->name('community.economy.currency.doDelete');
            //             });
            //         });
            //     });
            // });
        });
    });
});

// Bar routes
Route::prefix('/b')->middleware('auth')->group(function() {
    Route::get('/', 'BarController@overview')->name('bar.overview');

    // Require app administrator to create a bar
    Route::middleware(['selectCommunity', BarController::permsCreate()->middleware()])->group(function() {
        Route::get('/create/{communityId}', 'BarController@create')->name('bar.create');
        Route::post('/create/{communityId}', 'BarController@doCreate')->name('bar.doCreate');
    });

    // Specific
    Route::prefix('/{barId}')->middleware(['selectBar'])->group(function() {
        // Show
        Route::get('/', 'BarController@show')->name('bar.show');

        // Join/leave
        Route::get('/join', 'BarController@join')->name('bar.join');
        Route::post('/join', 'BarController@doJoin')->name('bar.doJoin');
        Route::get('/leave', 'BarController@leave')->name('bar.leave');
        Route::post('/leave', 'BarController@doLeave')->name('bar.doLeave');

        // Edit, require manage perms
        Route::middleware(BarController::permsManage()->middleware())->group(function() {
            Route::get('/edit', 'BarController@edit')->name('bar.edit');
            Route::put('/', 'BarController@update')->name('bar.update');
        });

        // Bar members, require view perms
        Route::prefix('/members')->middleware(BarMemberController::permsView()->middleware())->group(function() {
            // Show
            Route::get('/', 'BarMemberController@index')->name('bar.member.index');

            // Specific
            Route::prefix('/{memberId}')->group(function() {
                // Show
                Route::get('/', 'BarMemberController@show')->name('bar.member.show');

                // Edit/delete, require manage perms
                Route::middleware(BarMemberController::permsManage()->middleware())->group(function() {
                    Route::get('/edit', 'BarMemberController@edit')->name('bar.member.edit');
                    Route::put('/edit', 'BarMemberController@doEdit')->name('bar.member.doEdit');
                    Route::get('/delete', 'BarMemberController@delete')->name('bar.member.delete');
                    Route::delete('/delete', 'BarMemberController@doDelete')->name('bar.member.doDelete');
                });
            });
        });
    });
});

// TODO: Routes to implement
Route::get('/email/preferences', 'DashboardController@index')->name('email.preferences');
