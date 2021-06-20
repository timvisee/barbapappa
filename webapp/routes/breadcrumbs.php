<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// // Home
// Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
//     $trail->push('Home', route('home'));
// });

// // Home > Blog
// Breadcrumbs::for('blog', function (BreadcrumbTrail $trail) {
//     $trail->parent('home');
//     $trail->push('Blog', route('blog'));
// });

// // Home > Blog > [Category]
// Breadcrumbs::for('category', function (BreadcrumbTrail $trail, $category) {
//     $trail->parent('blog');
//     $trail->push($category->title, route('category', $category));
// });

// Dashboard
Breadcrumbs::for('dashboard', function(BreadcrumbTrail $trail) {
    $trail->push(__('pages.dashboard.title'), route('dashboard'));
});

// Community
Breadcrumbs::for('community.show', function(BreadcrumbTrail $trail, $community) {
    $trail->parent('dashboard');
    $trail->push($community->name, route('community.show', ['communityId' => $community->human_id]));
});

// Bar
Breadcrumbs::for('bar.show', function(BreadcrumbTrail $trail, $bar) {
    $trail->parent('community.show', $bar->community);
    $trail->push($bar->name, route('bar.show', ['barId' => $bar->human_id]));
});
