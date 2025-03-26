<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../bootstrap.php';

define('APPNAME', 'MINT FRESH FRUIT');

session_start();

$router = new \Bramus\Router\Router();

// Auth routes
$router->post('/logout', '\App\Controllers\Auth\LoginController@destroy');
$router->get('/register', '\App\Controllers\Auth\RegisterController@create');
$router->post('/register', '\\App\Controllers\Auth\RegisterController@store');
$router->get('/login', '\App\Controllers\Auth\LoginController@create');
$router->post('/login', '\App\Controllers\Auth\LoginController@store');

// Home routes
$router->get('/', '\App\Controllers\HomeController@index');
$router->get('/home', '\App\Controllers\HomeController@index');

// Contact routes
$router->get('/contacts', '\App\Controllers\ContactsController@index');
// $router->get('/home', '\App\Controllers\ContactsController@index');

$router->get(
    '/contacts/create',
    '\App\Controllers\ContactsController@create'
);
$router->post('/contacts', '\App\Controllers\ContactsController@store');

$router->get('/contacts/edit/(\d+)', '\App\Controllers\ContactsController@edit');

$router->post('/contacts/(\d+)', '\App\Controllers\ContactsController@update');
$router->post('/contacts/delete/(\d+)','\App\Controllers\ContactsController@destroy');


// product routes
$router->get('/products', '\App\Controllers\productsController@index');
$router->POST('/products/load_prod_cata', '\App\Controllers\productsController@getprodcatabyid');

$router->POST('/products/addprod/([\w-]+)', '\App\Controllers\OrdersController@addprod');
$router->get('/products/proddetail/([\w-]+)', '\App\Controllers\productsController@getproductbyid');

// load trang gio hang
$router->get('/products/shoppingcard', '\App\Controllers\OrdersController@shoppingcart');

// order routes
$router->get('/orders/index', '\App\Controllers\OrdersController@index');

//xem chi tiết đơn hàng
$router->get('/orders/order_detail/([\w-]+)', '\App\Controllers\OrdersController@orderdetail');

$router->POST('/orders/update/([\w-]+)', '\App\Controllers\OrdersController@updateprod');
$router->get('/orders/delete/([\w-]+)', '\App\Controllers\OrdersController@deletebyIDProd');

$router->POST('/orders/start_order', '\App\Controllers\DeliveryController@index');

//đặt hàng
$router->POST('/orders/save', '\App\Controllers\OrdersController@store');

$router->POST('/orders/search', '\App\Controllers\OrdersController@search');

//order cancel
$router->POST('/orders/cancel', '\App\Controllers\OrdersController@cancel');


$router->POST('/delivery/update', '\App\Controllers\DeliveryController@update');
$router->POST('/delivery/add', '\App\Controllers\DeliveryController@store');

$router->set404('\App\Controllers\Controller@sendNotFound');
$router->run();