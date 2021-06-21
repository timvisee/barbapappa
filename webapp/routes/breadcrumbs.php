<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard, common base page
Breadcrumbs::for('dashboard', function(BreadcrumbTrail $trail) {
    $trail->push(__('pages.dashboard.title'), route('dashboard'));
});

Breadcrumbs::for('explore', function(BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('pages.explore.title'), route('explore.community'));
});

Breadcrumbs::for('community.show', function(BreadcrumbTrail $trail, $community) {
    $trail->parent('dashboard');
    $trail->push($community->name, route('community.show', ['communityId' => $community->human_id]));
});

Breadcrumbs::for('community.wallet.index', function(BreadcrumbTrail $trail, $community) {
    $trail->parent('community.show', $community);
    $trail->push(__('pages.wallets.title'), route('community.wallet.index', ['communityId' => $community->human_id]));
});

Breadcrumbs::for('community.wallet.show', function(BreadcrumbTrail $trail, $community, $wallet) {
    $trail->parent('community.wallet.index', $community);
    $trail->push($wallet->name, route('community.wallet.show', [
        'communityId' => $community->human_id,
        'economyId' => $wallet->currency->economy_id,
        'walletId' => $wallet->id,
    ]));
});

Breadcrumbs::for('bar.show', function(BreadcrumbTrail $trail, $bar) {
    // $trail->parent('community.show', $bar->community);
    $trail->parent('dashboard');
    $trail->push($bar->name, route('bar.show', ['barId' => $bar->human_id]));
});

Breadcrumbs::for('bar.product.index', function(BreadcrumbTrail $trail, $bar) {
    $trail->parent('bar.show', $bar);
    $trail->push(__('pages.products.title'), route('bar.product.index', ['barId' => $bar->human_id]));
});

Breadcrumbs::for('bar.product.show', function(BreadcrumbTrail $trail, $bar, $product) {
    $trail->parent('bar.product.index', $bar);
    $trail->push($product->displayName(), route('bar.product.show', [
        'barId' => $bar->human_id,
        'productId' => $product->id,
    ]));
});

Breadcrumbs::for('bar.manage', function(BreadcrumbTrail $trail, $bar) {
    $trail->parent('bar.show', $bar);
    $trail->push(__('misc.manage'), route('bar.manage', ['barId' => $bar->human_id]));
});

Breadcrumbs::for('bar.history', function(BreadcrumbTrail $trail, $bar) {
    $trail->parent('bar.manage', $bar);
    $trail->push(__('pages.bar.purchaseHistory'), route('bar.history', ['barId' => $bar->human_id]));
});

Breadcrumbs::for('bar.links', function(BreadcrumbTrail $trail, $bar) {
    $trail->parent('bar.manage', $bar);
    $trail->push(__('pages.bar.links.title'), route('bar.links', ['barId' => $bar->human_id]));
});

Breadcrumbs::for('bar.kiosk.sessions', function(BreadcrumbTrail $trail, $bar) {
    $trail->parent('bar.manage', $bar);
    $trail->push(__('pages.bar.kioskSessions'), route('bar.kiosk.sessions', ['barId' => $bar->human_id]));
});

Breadcrumbs::for('account', function(BreadcrumbTrail $trail, $user) {
    $trail->parent('dashboard');
    $trail->push(__('pages.account'), route('account', ['userId' => $user->id]));
});

Breadcrumbs::for('account.emails', function(BreadcrumbTrail $trail, $user) {
    $trail->parent('account', $user);
    $trail->push(__('pages.accountPage.email.emails'), route('account.emails', ['userId' => $user->id]));
});

Breadcrumbs::for('account.sessions', function(BreadcrumbTrail $trail, $user) {
    $trail->parent('account', $user);
    $trail->push(__('account.sessions'), route('account.sessions', ['userId' => $user->id]));
});

Breadcrumbs::for('notification.index', function(BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('pages.notifications.title'));
});

Breadcrumbs::for('transaction.index', function(BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('pages.transactions.title'));
});

Breadcrumbs::for('transaction.show', function(BreadcrumbTrail $trail, $transaction) {
    $trail->parent('transaction.index');
    $trail->push('#' . $transaction->id, route('transaction.show', ['transactionId' => $transaction->id]));
});

Breadcrumbs::for('transaction.mutation.index', function(BreadcrumbTrail $trail, $transaction) {
    $trail->parent('transaction.show', $transaction);
    $trail->push(__('pages.mutations.title'));
});

Breadcrumbs::for('transaction.mutation.show', function(BreadcrumbTrail $trail, $mutation) {
    $trail->parent('transaction.mutation.index', $mutation->transaction);
    $trail->push('#' . $mutation->id);
});

Breadcrumbs::for('payment.index', function(BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('pages.payments.title'), route('payment.index'));
});

Breadcrumbs::for('payment.show', function(BreadcrumbTrail $trail, $payment) {
    $trail->parent('payment.index');
    $trail->push($payment->getReference(false, true), route('payment.show', ['paymentId' => $payment->id]));
});

// Generic info pages
Breadcrumbs::for('pages', function(BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('misc.information'));
});

Breadcrumbs::for('about', function(BreadcrumbTrail $trail) {
    $trail->parent('pages');
    $trail->push(__('pages.about.title'), route('about'));
});

Breadcrumbs::for('terms', function(BreadcrumbTrail $trail) {
    $trail->parent('pages');
    $trail->push(__('pages.terms.title'), route('terms'));
});

Breadcrumbs::for('privacy', function(BreadcrumbTrail $trail) {
    $trail->parent('pages');
    $trail->push(__('pages.privacy.title'), route('privacy'));
});

Breadcrumbs::for('license', function(BreadcrumbTrail $trail) {
    $trail->parent('pages');
    $trail->push(__('pages.license.title'), route('license'));
});

Breadcrumbs::for('contact', function(BreadcrumbTrail $trail) {
    $trail->parent('pages');
    $trail->push(__('pages.contact.title'), route('contact'));
});

Breadcrumbs::for('error', function(BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('general.error'));
});
