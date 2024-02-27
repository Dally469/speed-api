<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

$routes->get('/api/v2/get_nearby_client_request', 'Home::getNearbyClientRequest');
$routes->get('/api/v2/get_city', 'Home::getCities');

// WORKING ENDPOINT DRIVER
$routes->post('/api/v2/driver/register', 'Home::registerDriver');
$routes->post('/api/v2/driver/register_car', 'Home::registerDriverCar');
$routes->post('/api/v2/driver/update_info', 'Home::updateDriverInformationV2');
$routes->post('/api/v2/driver/login', 'Home::loginDriver');
$routes->post('/api/v2/driver/profile', 'Home::getDriverInfo'); 
$routes->post('/api/v2/driver/enable_on_map_visibility', 'Home::isDriverAvailableOnline');
$routes->post('/api/v2/driver/accept_client_request', 'Home::acceptClientRequest');
$routes->get('/api/v2/driver/fetch_client_request/(:any)', 'Home::getRequestFromClient/$1');
$routes->post('/api/v2/driver/switch_map_availability', 'Home::switchMapAvailability');
$routes->post('/api/v2/driver/is_online', 'Home::isDriverOnline');
$routes->post('/api/v2/driver/fetch_nearby_client_request', 'Home::getNearbyClientRequest_v3');
$routes->post('/api/v2/driver/fetch_cancelled_client_request', 'Home::watchCancelledClientRequest');
$routes->post('/api/v2/driver/approve_cancellation_client_request', 'Home::approveCancellationClientRequest');





// WORKING ENDPOINT CLIENT
$routes->post('/api/v2/client/login', 'Home::clientLogin');
$routes->post('/api/v2/client/profile', 'Home::getClientInfo'); 
$routes->post('/api/v2/client/register', 'Home::registerCustomer');
$routes->post('/api/v2/client/update_info_v2', 'Home::updateClientInformationV2');
$routes->post('/api/v2/client/update_info', 'Home::updateClientInformation');
$routes->post('/api/v2/client/ride_request', 'Home::clientRequestRide');
$routes->post('/api/v2/client/fetch_my_accepted_request', 'Home::getMyAcceptedRequest');
$routes->get('/api/v2/client/fetch_my_booking_request/(:any)', 'Home::getMyBookingRequest/$1');
$routes->post('/api/v2/client/save_favorite_location', 'Home::saveClientPickupLocation');
$routes->post('/api/v2/client/fetch_favorite_location', 'Home::getClientPickUpLocation');
$routes->get('/api/v2/client/fetch_my_history/(:any)', 'Home::getMyHistory/$1');
$routes->post('/api/v2/client/fetch_nearby_driver', 'Home::getClientNearbyDriver');
$routes->get('/api/v2/client/fetch_car_categories', 'Home::getCarCategories');
$routes->get('/api/v2/client/get_online_driver_location', 'Home::getDriverLocation');
$routes->post('/api/v2/client/cancel_ride_request', 'Home::cancelRideRequest');
$routes->post('/api/v2/client/get_client_ride_request', 'Home::getClientHistorys');


$routes->get('/api/v2/get_daily_report/(:any)', 'Home::getDailyReport/$1');
$routes->post('/api/v2/processing_payment', 'Home::processingPayment');
$routes->post('/api/v2/client_send_booking_request', 'Home::clientBookingRide');



$routes->add('/(:any)', 'Home::$1');
$routes->add('/(:any)', 'Web::$1');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
