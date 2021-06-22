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

Breadcrumbs::for('app.manage', function(BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(__('misc.app'));
    $trail->push(__('misc.manage'), route('app.manage'));
});

Breadcrumbs::for('app.bunqaccount.index', function(BreadcrumbTrail $trail) {
    $trail->parent('app.manage');
    $trail->push(__('pages.bunqAccounts.title'), route('app.bunqAccount.index'));
});

Breadcrumbs::for('app.bunqaccount.show', function(BreadcrumbTrail $trail, $account) {
    $trail->parent('app.bunqaccount.index');
    $trail->push($account->name, route('app.bunqAccount.show', ['accountId' => $account->id]));
});

Breadcrumbs::for('community.show', function(BreadcrumbTrail $trail, $community) {
    $trail->parent('dashboard');
    $trail->push($community->name, route('community.show', ['communityId' => $community->human_id]));
});

Breadcrumbs::for('community.info', function(BreadcrumbTrail $trail, $community) {
    $trail->parent('community.show', $community);
    $trail->push(__('misc.information'), route('community.info', ['communityId' => $community->human_id]));
});

Breadcrumbs::for('community.stats', function(BreadcrumbTrail $trail, $community) {
    $trail->parent('community.show', $community);
    $trail->push(__('pages.stats.title'), route('community.stats', ['communityId' => $community->human_id]));
});

Breadcrumbs::for('community.manage', function(BreadcrumbTrail $trail, $community) {
    $trail->parent('community.show', $community);
    $trail->push(__('misc.manage'), route('community.manage', ['communityId' => $community->human_id]));
});

Breadcrumbs::for('community.links', function(BreadcrumbTrail $trail, $community) {
    $trail->parent('community.manage', $community);
    $trail->push(__('misc.links'), route('community.links', ['communityId' => $community->human_id]));
});

Breadcrumbs::for('community.poster', function(BreadcrumbTrail $trail, $community) {
    $trail->parent('community.manage', $community);
    $trail->push(__('misc.poster'), route('community.poster.generate', ['communityId' => $community->human_id]));
});

Breadcrumbs::for('community.member.index', function(BreadcrumbTrail $trail, $community) {
    $trail->parent('community.manage', $community);
    $trail->push(__('misc.members'), route('community.member.index', ['communityId' => $community->human_id]));
});

Breadcrumbs::for('community.member.show', function(BreadcrumbTrail $trail, $member) {
    $trail->parent('community.member.index', $member->community);
    $trail->push($member->name, route('community.member.show', ['communityId' => $member->community_id, 'memberId' => $member->id]));
});

Breadcrumbs::for('community.economy.index', function(BreadcrumbTrail $trail, $community) {
    $trail->parent('community.manage', $community);
    $trail->push(__('pages.economies.title'), route('community.economy.index', ['communityId' => $community->human_id]));
});

Breadcrumbs::for('community.economy.show', function(BreadcrumbTrail $trail, $economy) {
    $trail->parent('community.economy.index', $economy->community);
    $trail->push($economy->name, route('community.economy.show', ['communityId' => $economy->community_id, 'economyId' => $economy->id]));
});

Breadcrumbs::for('community.economy.product.index', function(BreadcrumbTrail $trail, $economy) {
    $trail->parent('community.economy.show', $economy);
    $trail->push(__('pages.products.title'), route('community.economy.product.index', ['communityId' => $economy->community_id, 'economyId' => $economy->id]));
});

Breadcrumbs::for('community.economy.product.show', function(BreadcrumbTrail $trail, $product) {
    $economy = $product->economy;
    $trail->parent('community.economy.product.index', $economy);
    $trail->push($product->name, route('community.economy.product.show', [
        'communityId' => $economy->community_id,
        'economyId' => $economy->id,
        'productId' => $product->id,
    ]));
});

Breadcrumbs::for('community.economy.balanceimport.index', function(BreadcrumbTrail $trail, $economy) {
    $trail->parent('community.economy.show', $economy);
    $trail->push(__('pages.balanceImport.title'), route('community.economy.balanceimport.index', ['communityId' => $economy->community_id, 'economyId' => $economy->id]));
});

Breadcrumbs::for('community.economy.balanceimport.show', function(BreadcrumbTrail $trail, $system) {
    $economy = $system->economy;
    $trail->parent('community.economy.balanceimport.index', $economy);
    $trail->push($system->name, route('community.economy.balanceimport.show', [
        'communityId' => $economy->community_id,
        'economyId' => $economy->id,
        'systemId' => $system->id,
    ]));
});

Breadcrumbs::for('community.economy.balanceimport.event.index', function(BreadcrumbTrail $trail, $system) {
    $economy = $system->economy;
    $trail->parent('community.economy.balanceimport.show', $system);
    $trail->push(__('pages.balanceImportEvent.events'), route('community.economy.balanceimport.event.index', [
        'communityId' => $economy->community_id,
        'economyId' => $economy->id,
        'systemId' => $system->id,
    ]));
});

Breadcrumbs::for('community.economy.balanceimport.event.show', function(BreadcrumbTrail $trail, $event) {
    $system = $event->system;
    $economy = $system->economy;
    $trail->parent('community.economy.balanceimport.event.index', $system);
    $trail->push($event->name, route('community.economy.balanceimport.event.show', [
        'communityId' => $economy->community_id,
        'economyId' => $economy->id,
        'systemId' => $system->id,
        'eventId' => $event->id,
    ]));
});

Breadcrumbs::for('community.economy.balanceimport.change.index', function(BreadcrumbTrail $trail, $event) {
    $system = $event->system;
    $economy = $system->economy;
    $trail->parent('community.economy.balanceimport.event.show', $event);
    $trail->push(__('pages.balanceImportChange.changes'), route('community.economy.balanceimport.change.index', [
        'communityId' => $economy->community_id,
        'economyId' => $economy->id,
        'systemId' => $system->id,
        'eventId' => $event->id,
    ]));
});

Breadcrumbs::for('community.economy.balanceimport.change.show', function(BreadcrumbTrail $trail, $change) {
    $event = $change->event;
    $system = $event->system;
    $economy = $system->economy;
    $trail->parent('community.economy.balanceimport.change.index', $event);
    $trail->push('#' . $change->id, route('community.economy.balanceimport.change.show', [
        'communityId' => $economy->community_id,
        'economyId' => $economy->id,
        'systemId' => $system->id,
        'eventId' => $event->id,
        'changeId' => $change->id,
    ]));
});

Breadcrumbs::for('community.economy.currency.index', function(BreadcrumbTrail $trail, $economy) {
    $trail->parent('community.economy.show', $economy);
    $trail->push(__('pages.currencies.title'), route('community.economy.currency.index', ['communityId' => $economy->community_id, 'economyId' => $economy->id]));
});

Breadcrumbs::for('community.economy.currency.show', function(BreadcrumbTrail $trail, $currency) {
    $economy = $currency->economy;
    $trail->parent('community.economy.currency.index', $economy);
    $trail->push($currency->name, route('community.economy.currency.show', [
        'communityId' => $economy->community_id,
        'economyId' => $economy->id,
        'currencyId' => $currency->id,
    ]));
});

Breadcrumbs::for('community.economy.finance.overview', function(BreadcrumbTrail $trail, $economy) {
    $trail->parent('community.economy.show', $economy);
    $trail->push(__('pages.finance.title'), route('community.economy.finance.overview', ['communityId' => $economy->community_id, 'economyId' => $economy->id]));
});

Breadcrumbs::for('community.economy.paymentservice.index', function(BreadcrumbTrail $trail, $economy) {
    $trail->parent('community.economy.show', $economy);
    $trail->push(__('pages.paymentService.title'), route('community.economy.payservice.index', ['communityId' => $economy->community_id, 'economyId' => $economy->id]));
});

Breadcrumbs::for('community.economy.paymentservice.show', function(BreadcrumbTrail $trail, $service) {
    $economy = $service->economy;
    $trail->parent('community.economy.paymentservice.index', $economy);
    $trail->push('#' . $service->id, route('community.economy.payservice.show', [
        'communityId' => $economy->community_id,
        'economyId' => $economy->id,
        'serviceId' => $service->id,
    ]));
});

Breadcrumbs::for('community.wallet.index', function(BreadcrumbTrail $trail, $community) {
    $trail->parent('community.show', $community);
    $trail->push(__('pages.wallets.title'), route('community.wallet.index', ['communityId' => $community->human_id]));
});

Breadcrumbs::for('community.wallet.list', function(BreadcrumbTrail $trail, $economy) {
    $community = $economy->community;
    $trail->parent('community.wallet.index', $community);
    $trail->push($economy->name, route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]));
});

Breadcrumbs::for('community.wallet.show', function(BreadcrumbTrail $trail, $community, $wallet) {
    $economy = $wallet->currency->economy;
    $trail->parent('community.wallet.list', $economy);
    $trail->push($wallet->name, route('community.wallet.show', [
        'communityId' => $community->human_id,
        'economyId' => $economy->id,
        'walletId' => $wallet->id,
    ]));
});

Breadcrumbs::for('community.wallet.stats', function(BreadcrumbTrail $trail, $community, $wallet) {
    $trail->parent('community.wallet.show', $community, $wallet);
    $trail->push(__('misc.stats'), route('community.wallet.stats', [
        'communityId' => $community->human_id,
        'economyId' => $wallet->currency->economy_id,
        'walletId' => $wallet->id,
    ]));
});

Breadcrumbs::for('community.bunqaccount.index', function(BreadcrumbTrail $trail, $community) {
    $trail->parent('community.manage', $community);
    $trail->push(__('pages.bunqAccounts.title'), route('community.bunqAccount.index', ['communityId' => $community->human_id]));
});

Breadcrumbs::for('community.bunqaccount.show', function(BreadcrumbTrail $trail, $account) {
    $trail->parent('community.bunqaccount.index', $account->community);
    $trail->push($account->name, route('community.bunqAccount.show', ['communityId' => $account->community_id, 'accountId' => $account->id]));
});

Breadcrumbs::for('bar.show', function(BreadcrumbTrail $trail, $bar) {
    // $trail->parent('community.show', $bar->community);
    $trail->parent('dashboard');
    $trail->push($bar->name, route('bar.show', ['barId' => $bar->human_id]));
});

Breadcrumbs::for('bar.info', function(BreadcrumbTrail $trail, $bar) {
    $trail->parent('bar.show', $bar);
    $trail->push(__('misc.information'), route('bar.info', ['barId' => $bar->human_id]));
});

Breadcrumbs::for('bar.stats', function(BreadcrumbTrail $trail, $bar) {
    $trail->parent('bar.show', $bar);
    $trail->push(__('pages.stats.title'), route('bar.stats', ['barId' => $bar->human_id]));
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
    $trail->push(__('misc.links'), route('bar.links', ['barId' => $bar->human_id]));
});

Breadcrumbs::for('bar.poster', function(BreadcrumbTrail $trail, $bar) {
    $trail->parent('bar.manage', $bar);
    $trail->push(__('misc.poster'), route('bar.poster.generate', ['barId' => $bar->human_id]));
});

Breadcrumbs::for('bar.member.index', function(BreadcrumbTrail $trail, $bar) {
    $trail->parent('bar.manage', $bar);
    $trail->push(__('misc.members'), route('bar.member.index', ['barId' => $bar->human_id]));
});

Breadcrumbs::for('bar.member.show', function(BreadcrumbTrail $trail, $member) {
    $trail->parent('bar.member.index', $member->bar);
    $trail->push($member->name, route('bar.member.show', ['barId' => $member->bar_id, 'memberId' => $member->id]));
});

Breadcrumbs::for('bar.kiosk.sessions.index', function(BreadcrumbTrail $trail, $bar) {
    $trail->parent('bar.manage', $bar);
    $trail->push(__('pages.bar.kioskSessions'), route('bar.kiosk.sessions.index', ['barId' => $bar->human_id]));
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

Breadcrumbs::for('error', function(BreadcrumbTrail $trail, $error) {
    $trail->parent('dashboard');
    $trail->push(__('general.error'));
    $trail->push($error);
});
