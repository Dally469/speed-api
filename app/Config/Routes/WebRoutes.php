<?php namespace Config;

$routes->group('api/v1/web', ['namespace' => 'App\Controllers\Api\Web'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});
