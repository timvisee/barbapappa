<?php

use App\Http\Controllers\BarController;
use App\Http\Controllers\BarMemberController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\CommunityMemberController;
use App\Http\Controllers\EconomyController;
use App\Http\Controllers\EconomyCurrencyController;
use App\Http\Controllers\ProductController;
use App\Perms\AppRoles;
use App\Perms\BarRoles;
use App\Perms\CommunityRoles;

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
        // Show, info
        Route::get('/', 'CommunityController@show')->name('community.show');
        Route::get('/info', 'CommunityController@info')->name('community.info');

        // Stats
        Route::get('/stats', 'CommunityController@stats')->middleware(CommunityController::permsUser()->middleware())->name('community.stats');

        // Join/leave
        Route::get('/join', 'CommunityController@join')->name('community.join');
        Route::post('/join', 'CommunityController@doJoin')->name('community.doJoin');
        Route::get('/leave', 'CommunityController@leave')->name('community.leave');
        Route::post('/leave', 'CommunityController@doLeave')->name('community.doLeave');

        // Edit, require manage perms
        Route::prefix('/edit')->middleware(CommunityController::permsAdminister()->middleware())->group(function() {
            Route::get('/', 'CommunityController@edit')->name('community.edit');
            Route::put('/', 'CommunityController@doEdit')->name('community.doEdit');
        });

        // Management page
        // TODO: assing proper permission here, allow management role
        Route::get('/manage', 'CommunityController@manage')->middleware(CommunityController::permsManage()->middleware())->name('community.manage');

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

                // Economy products, require view perms
                Route::prefix('/products')->middleware(ProductController::permsView()->middleware())->group(function() {
                    // Index
                    Route::get('/', 'ProductController@index')->name('community.economy.product.index');

                    // Create, require manage perms
                    Route::middleware(ProductController::permsManage()->middleware())->group(function() {
                        Route::get('/create', 'ProductController@create')->name('community.economy.product.create');
                        Route::post('/create', 'ProductController@doCreate')->name('community.economy.product.doCreate');
                    });

                    // Specific
                    Route::prefix('/{productId}')->group(function() {
                        // Show
                        Route::get('/', 'ProductController@show')->name('community.economy.product.show');

                        // Edit/delete, require manager perms
                        Route::middleware(ProductController::permsManage()->middleware())->group(function() {
                            Route::get('/edit', 'ProductController@edit')->name('community.economy.product.edit');
                            Route::put('/edit', 'ProductController@doEdit')->name('community.economy.product.doEdit');
                            Route::get('/delete', 'ProductController@delete')->name('community.economy.product.delete');
                            Route::delete('/delete', 'ProductController@doDelete')->name('community.economy.product.doDelete');
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

                    // Edit/delete
                    Route::get('/edit', 'WalletController@edit')->name('community.wallet.edit');
                    Route::put('/edit', 'WalletController@doEdit')->name('community.wallet.doEdit');
                    Route::get('/delete', 'WalletController@delete')->name('community.wallet.delete');
                    Route::delete('/delete', 'WalletController@doDelete')->name('community.wallet.doDelete');

                    // Transactions
                    Route::get('/transactions', 'WalletController@transactions')->name('community.wallet.transactions');

                    // Transfer pages
                    Route::get('/transfer', 'WalletController@transfer')->name('community.wallet.transfer');
                    Route::post('/transfer', 'WalletController@doTransfer')->name('community.wallet.doTransfer');
                    Route::get('/transfer/user', 'WalletController@transferUser')->name('community.wallet.transfer.user');

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
        // Show, info
        Route::get('/', 'BarController@show')->name('bar.show');
        Route::get('/info', 'BarController@info')->name('bar.info');

        // Stats
        Route::get('/stats', 'BarController@stats')->middleware(BarController::permsUser()->middleware())->name('bar.stats');

        // Join/leave
        Route::get('/join', 'BarController@join')->name('bar.join');
        Route::post('/join', 'BarController@doJoin')->name('bar.doJoin');
        Route::get('/leave', 'BarController@leave')->name('bar.leave');
        Route::post('/leave', 'BarController@doLeave')->name('bar.doLeave');

        // Management page
        // TODO: link to proper action
        // TODO: assing proper permission here, allow management role
        Route::get('/manage', 'BarController@show')->middleware(BarController::permsManage()->middleware())->name('bar.manage');

        // Edit, require manage perms
        Route::middleware(BarController::permsManage()->middleware())->group(function() {
            Route::get('/edit', 'BarController@edit')->name('bar.edit');
            Route::put('/', 'BarController@update')->name('bar.update');
        });

        // Bar products
        Route::prefix('/products')->group(function() {
            Route::get('/', 'BarProductController@index')->name('bar.product.index');
            Route::get('/{productId}', 'BarProductController@show')->name('bar.product.show');
        });

        // Quick buy products
        Route::post('/quick-buy', 'BarController@quickBuy')->name('bar.quickBuy');

        // Bar members, require view perms
        Route::prefix('/members')->middleware(BarMemberController::permsView()->middleware())->group(function() {
            // Index
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

// Transactions
Route::prefix('/transactions')->group(function() {
    // Index
    // Route::get('/', 'EconomyCurrencyController@index')->name('community.economy.currency.index');

    // Specific
    // TODO: find transaction, check permission
    Route::prefix('/{transactionId}')->group(function() {
        // Show
        Route::get('/', 'TransactionController@show')->name('transaction.show');

        // // Edit/delete, require manager perms
        // Route::middleware(EconomyCurrencyController::permsManage()->middleware())->group(function() {
        //     Route::get('/edit', 'EconomyCurrencyController@edit')->name('community.economy.currency.edit');
        //     Route::put('/edit', 'EconomyCurrencyController@doEdit')->name('community.economy.currency.doEdit');
        //     Route::get('/remove', 'EconomyCurrencyController@delete')->name('community.economy.currency.delete');
        //     Route::delete('/remove', 'EconomyCurrencyController@doDelete')->name('community.economy.currency.doDelete');
        // });

        // Undo
        Route::get('/undo', 'TransactionController@undo')->name('transaction.undo');
        Route::delete('/undo', 'TransactionController@doUndo')->name('transaction.doUndo');

        // Transaction mutations
        // TODO: add a permission check
        // Route::prefix('/mutations')->middleware(MutationController::permsView()->middleware())->group(function() {
        Route::prefix('/mutations')->group(function() {
            // Index
            // Route::get('/', 'MutationController@index')->name('transaction.mutation.index');

            // Specific
            Route::prefix('/{mutationId}')->group(function() {
                // Show
                Route::get('/', 'MutationController@show')->name('transaction.mutation.show');

                // // Edit/delete, require manage perms
                // Route::middleware(MutationController::permsManage()->middleware())->group(function() {
                //     Route::get('/edit', 'MutationController@edit')->name('bar.member.edit');
                //     Route::put('/edit', 'MutationController@doEdit')->name('bar.member.doEdit');
                //     Route::get('/delete', 'MutationController@delete')->name('bar.member.delete');
                //     Route::delete('/delete', 'MutationController@doDelete')->name('bar.member.doDelete');
                // });
            });
        });
    });
});

// Magic routes
Route::get('/__heartbeat__', function() { return 'OK'; });
Route::get('/__version__', function() { return [
    'version' => config('app.version_name'),
    'version_code' => config('app.version_code'),
    'source' => config('app.source'),
    'env' => config('app.env'),
]; });

// TODO: Routes to implement
Route::get('/email/preferences', 'DashboardController@index')->name('email.preferences');
