<?php

use App\Http\Controllers\AppBunqAccountController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\BarController;
use App\Http\Controllers\BarMemberController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\CommunityMemberController;
use App\Http\Controllers\EconomyController;
use App\Http\Controllers\EconomyCurrencyController;
use App\Http\Controllers\PaymentServiceController;
use App\Http\Controllers\ProductController;

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

// Explore routes
Route::prefix('/explore')->middleware('auth')->group(function() {
    Route::get('/', 'ExploreController@communities')->name('explore.community');
    Route::get('/bars', 'ExploreController@bars')->name('explore.bar');
});

// Community routes
Route::prefix('/c')->middleware('auth')->group(function() {
    // Redirect to explore
    Route::redirect('/', '/explore');

    // Require app administrator to create a community
    Route::middleware(CommunityController::permsCreate()->middleware())->group(function() {
        Route::get('/create', 'CommunityController@create')->name('community.create');
        Route::post('/', 'CommunityController@doCreate')->name('community.doCreate');
    });

    // Community specific, public
    Route::prefix('/{communityId}')->middleware(['selectCommunity'])->group(function() {
        // Show, info
        Route::get('/', 'CommunityController@show')->name('community.show');
        Route::get('/info', 'CommunityController@info')->name('community.info');

        // Join/leave
        Route::get('/join', 'CommunityController@join')->name('community.join');
        Route::post('/join', 'CommunityController@doJoin')->name('community.doJoin');
        Route::get('/leave', 'CommunityController@leave')->name('community.leave');
        Route::post('/leave', 'CommunityController@doLeave')->name('community.doLeave');
    });

    // Community specific, members
    Route::prefix('/{communityId}')->middleware(['selectCommunity', CommunityController::permsUser()->middleware()])->group(function() {
        // Stats
        Route::get('/stats', 'CommunityController@stats')->name('community.stats');

        // Edit, require manage perms
        Route::prefix('/edit')->middleware(CommunityController::permsAdminister()->middleware())->group(function() {
            Route::get('/', 'CommunityController@edit')->name('community.edit');
            Route::put('/', 'CommunityController@doEdit')->name('community.doEdit');
        });

        // Delete, require administration perms
        Route::prefix('/delete')->middleware(CommunityController::permsAdminister()->middleware())->group(function() {
            Route::get('/', 'CommunityController@delete')->name('community.delete');
            Route::delete('/', 'CommunityController@doDelete')->name('community.doDelete');
        });

        // Management pages
        Route::prefix('/manage')->middleware(CommunityController::permsManage()->middleware())->group(function() {
            // Index
            Route::get('/', 'CommunityController@manage')->middleware(CommunityController::permsManage()->middleware())->name('community.manage');

            // Generate poster
            Route::get('/generate-poster', 'CommunityController@generatePoster')->name('community.poster.generate');
            Route::post('/generate-poster', 'CommunityController@doGeneratePoster')->name('community.poster.doGenerate');

            // bunq accounts, require view perms
            // TODO: set proper permissions!
            Route::prefix('/bunq-accounts')->group(function() {
                // Index
                Route::get('/', 'BunqAccountController@index')->name('community.bunqAccount.index');

                // Create
                Route::get('/add', 'BunqAccountController@create')->name('community.bunqAccount.create');
                Route::post('/add', 'BunqAccountController@doCreate')->name('community.bunqAccount.doCreate');

                // Specific
                Route::prefix('/{accountId}')->group(function() {
                    // Show
                    Route::get('/', 'BunqAccountController@show')->name('community.bunqAccount.show');

                    // Housekeeping
                    Route::post('/housekeep', 'BunqAccountController@doHousekeep')->name('community.bunqAccount.doHousekeep');

                    // Edit/delete
                    Route::get('/edit', 'BunqAccountController@edit')->name('community.bunqAccount.edit');
                    Route::put('/edit', 'BunqAccountController@doEdit')->name('community.bunqAccount.doEdit');
                    Route::get('/delete', 'BunqAccountController@delete')->name('community.bunqAccount.delete');
                    Route::delete('/delete', 'BunqAccountController@doDelete')->name('community.bunqAccount.doDelete');
                });
            });
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
                            Route::get('/restore', 'ProductController@restore')->name('community.economy.product.restore');
                            Route::put('/restore', 'ProductController@doRestore')->name('community.economy.product.doRestore');
                            Route::get('/delete', 'ProductController@delete')->name('community.economy.product.delete');
                            Route::delete('/delete', 'ProductController@doDelete')->name('community.economy.product.doDelete');
                        });
                    });
                });

                // Economy payment services, require view perms
                Route::prefix('/pay-services')->middleware(PaymentServiceController::permsView()->middleware())->group(function() {
                    // Index
                    Route::get('/', 'PaymentServiceController@index')->name('community.economy.payservice.index');

                    // Create, require manage perms
                    Route::middleware(PaymentServiceController::permsManage()->middleware())->group(function() {
                        Route::get('/add', 'PaymentServiceController@create')->name('community.economy.payservice.create');
                        Route::post('/add', 'PaymentServiceController@doCreate')->name('community.economy.payservice.doCreate');
                    });

                    // Specific
                    Route::prefix('/{serviceId}')->group(function() {
                        // Show
                        Route::get('/', 'PaymentServiceController@show')->name('community.economy.payservice.show');

                        // Edit/delete, require manager perms
                        Route::middleware(PaymentServiceController::permsManage()->middleware())->group(function() {
                            Route::get('/edit', 'PaymentServiceController@edit')->name('community.economy.payservice.edit');
                            Route::put('/edit', 'PaymentServiceController@doEdit')->name('community.economy.payservice.doEdit');
                            // Route::get('/restore', 'PaymentServiceController@restore')->name('community.economy.payservice.restore');
                            // Route::put('/restore', 'PaymentServiceController@doRestore')->name('community.economy.payservice.doRestore');
                            Route::get('/delete', 'PaymentServiceController@delete')->name('community.economy.payservice.delete');
                            Route::delete('/delete', 'PaymentServiceController@doDelete')->name('community.economy.payservice.doDelete');
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

                    // Top-up pages
                    Route::get('/top-up', 'WalletController@topUp')->name('community.wallet.topUp');
                    Route::post('/top-up', 'WalletController@doTopUp')->name('community.wallet.topUp');

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
    // Redirect to explore
    Route::redirect('/', '/explore/bars');

    // Require app administrator to create a bar
    Route::middleware(['selectCommunity', BarController::permsCreate()->middleware()])->group(function() {
        Route::get('/create/{communityId}', 'BarController@create')->name('bar.create');
        Route::post('/create/{communityId}', 'BarController@doCreate')->name('bar.doCreate');
    });

    // Bar specific, public
    Route::prefix('/{barId}')->middleware(['selectBar'])->group(function() {
        // Show, info
        Route::get('/', 'BarController@show')->name('bar.show');
        Route::get('/info', 'BarController@info')->name('bar.info');

        // Join/leave
        Route::get('/join', 'BarController@join')->name('bar.join');
        Route::post('/join', 'BarController@doJoin')->name('bar.doJoin');
        Route::get('/leave', 'BarController@leave')->name('bar.leave');
        Route::post('/leave', 'BarController@doLeave')->name('bar.doLeave');
    });

    // Bar specific, members
    Route::prefix('/{barId}')->middleware(['selectBar', BarController::permsUser()->middleware()])->group(function() {
        // Stats
        Route::get('/stats', 'BarController@stats')->name('bar.stats');

        // Management pages
        Route::prefix('/manage')->middleware(BarController::permsManage()->middleware())->group(function() {
            // Index
            Route::get('/', 'BarController@manage')->middleware(BarController::permsManage()->middleware())->name('bar.manage');

            // Generate poster
            Route::get('/generate-poster', 'BarController@generatePoster')->name('bar.poster.generate');
            Route::post('/generate-poster', 'BarController@doGeneratePoster')->name('bar.poster.doGenerate');
        });

        // Edit, require manage perms
        // TODO: require manager or admin?
        Route::prefix('/edit')->middleware(BarController::permsAdminister()->middleware())->group(function() {
            Route::get('/', 'BarController@edit')->name('bar.edit');
            Route::put('/', 'BarController@doEdit')->name('bar.doEdit');
        });

        // Delete, require administration perms
        Route::prefix('/delete')->middleware(BarController::permsAdminister()->middleware())->group(function() {
            Route::get('/', 'BarController@delete')->name('bar.delete');
            Route::delete('/', 'BarController@doDelete')->name('bar.doDelete');
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
Route::prefix('/transactions')->middleware('auth')->group(function() {
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

// Payments
Route::prefix('/payments')->middleware('auth')->group(function() {
    // Index
    Route::get('/', 'PaymentController@index')->name('payment.index');

    // Approve
    // TODO: check permission
    Route::prefix('/approve')->group(function() {
        // Show
        Route::get('/', 'PaymentController@approveList')->name('payment.approveList');

        // Specific
        // TODO: find payment, check permission
        Route::get('/{paymentId}', 'PaymentController@approve')->name('payment.approve');
        Route::post('/{paymentId}', 'PaymentController@doApprove')->name('payment.doApprove');
    });

    // Specific
    // TODO: find payment, check permission
    Route::prefix('/{paymentId}')->group(function() {
        // Show
        Route::get('/', 'PaymentController@show')->name('payment.show');

        // Pay
        Route::get('/pay', 'PaymentController@pay')->name('payment.pay');
        Route::post('/pay', 'PaymentController@doPay')->name('payment.doPay');

        // Cancel
        Route::get('/cancel', 'PaymentController@cancel')->name('payment.cancel');
        Route::delete('/cancel', 'PaymentController@doCancel')->name('payment.doCancel');
    });
});

// Management pages
Route::prefix('/manage')->middleware(AppController::permsAdminister()->middleware())->group(function() {
    // Index
    Route::get('/', 'AppController@manage')->name('app.manage');

    // bunq accounts, require view perms
    // TODO: set proper perms here
    Route::prefix('/bunq-accounts')->middleware(AppBunqAccountController::permsView()->middleware())->group(function() {
        // Index
        Route::get('/', 'AppBunqAccountController@index')->name('app.bunqAccount.index');

        // Create
        Route::get('/add', 'AppBunqAccountController@create')->name('app.bunqAccount.create');
        Route::post('/add', 'AppBunqAccountController@doCreate')->name('app.bunqAccount.doCreate');

        // Specific
        Route::prefix('/{accountId}')->group(function() {
            // Show
            Route::get('/', 'AppBunqAccountController@show')->name('app.bunqAccount.show');

            // Housekeeping
            Route::post('/housekeep', 'AppBunqAccountController@doHousekeep')->name('app.bunqAccount.doHousekeep');

            // Edit/delete
            Route::get('/edit', 'AppBunqAccountController@edit')->name('app.bunqAccount.edit');
            Route::put('/edit', 'AppBunqAccountController@doEdit')->name('app.bunqAccount.doEdit');
            Route::get('/delete', 'AppBunqAccountController@delete')->name('app.bunqAccount.delete');
            Route::delete('/delete', 'AppBunqAccountController@doDelete')->name('app.bunqAccount.doDelete');
        });
    });
});

// Ajax routes
// TODO: skip language select middleware here
Route::prefix('/ajax')->name('ajax.')->group(function() {
    Route::get('/messages-sidebar', 'AjaxController@messagesSidebar')->name('messagesSidebar');
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
