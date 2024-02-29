<?php namespace Config;

$routes->group('api/v1/web', ['namespace' => 'App\Controllers\Api\Web'], function ($routes) {
    $routes->post('auth', 'AuthController::login');
    $routes->get('dashboard', 'DashboardController::index');
});
