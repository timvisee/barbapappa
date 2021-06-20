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

Breadcrumbs::for('bar.show', function(BreadcrumbTrail $trail, $bar) {
    $trail->parent('community.show', $bar->community);
    $trail->push($bar->name, route('bar.show', ['barId' => $bar->human_id]));
});

Breadcrumbs::for('bar.product.index', function(BreadcrumbTrail $trail, $bar) {
    $trail->parent('bar.show', $bar);
    $trail->push(__('pages.products.title'), route('bar.product.index', ['barId' => $bar->human_id]));
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

Breadcrumbs::for('transaction.mutation.index', function(BreadcrumbTrail $trail) {
    $trail->parent('transaction.index');
    $trail->push(__('pages.mutations.title'));
});

Breadcrumbs::for('payment.index', function(BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('pages.payments.title'), route('payment.index'));
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
