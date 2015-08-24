<?php

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

// Return the config array
return Array(
    'general' => Array(
        'site_url'          => '', // http://localhost/app/barapp/
        'site_path'         => '/app/barapp'
    ),

    'database' => Array(
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'barapp',
        'user' => 'root',
        'password' => 'root',
        'table_prefix' => 'bar_'
    ),

    'database' => Array(
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'ovrally',
        'user' => 'root',
        'password' => 'hoihoi',
        'table_prefix' => 'ov_'
    ),

    'cookie' => Array(
        'domain' => '',
        'path' => '/'
    ),

    'hash' => Array(
        'hash_algorithm' => 'sha256',
        'hash_key'  => '7cc8b7833dba1a03dd6d1401aa25262fab029862b494aff2168f1a6b35f1f406'
    ),

    'app' => Array(
        // TODO: Disable debug mode on release!
        'debug' => true
    )
);
