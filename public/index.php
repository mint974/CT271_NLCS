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
$router->post('/search', '\App\Controllers\HomeController@search');

$router->get('/adminhome', '\App\Controllers\HomeController@adminindex');


$router->get('/home/report', '\App\Controllers\HomeController@report');


// Contact routes
$router->get('/contacts', '\App\Controllers\ContactsController@indexUser');
$router->post('/contacts/save', '\App\Controllers\ContactsController@store');


//contact admin 
$router->get('/contacts/admin', '\App\Controllers\ContactsController@index');

// product routes
$router->get('/products', '\App\Controllers\productsController@index');
$router->POST('/products/load_prod_cata', '\App\Controllers\productsController@getprodcatabyid');

$router->POST('/products/addprod/([\w-]+)', '\App\Controllers\OrdersController@addprod');
$router->get('/products/proddetail/([\w-]+)', '\App\Controllers\productsController@getproductbyid');

// pruduct admin
$router->get('/products/admin', '\App\Controllers\productsController@indexadmin');

$router->post('/products/search', '\App\Controllers\productsController@search');
$router->get('/products/updateInfor/([\w-]+)', '\App\Controllers\productsController@UpdateInforPage');


// load trang gio hang
$router->get('/products/shoppingcard', '\App\Controllers\OrdersController@shoppingcart');

// order routes
$router->get('/orders/index', '\App\Controllers\OrdersController@index');

//order_detail
$router->get('/orders/order_detail/([\w-]+)', '\App\Controllers\OrdersController@orderdetail');

$router->POST('/orders/update/([\w-]+)', '\App\Controllers\OrdersController@updateprod');
$router->get('/orders/delete/([\w-]+)', '\App\Controllers\OrdersController@deletebyIDProd');

$router->POST('/orders/start_order', '\App\Controllers\DeliveryController@index');

//order
$router->POST('/orders/save', '\App\Controllers\OrdersController@store');

$router->POST('/orders/search', '\App\Controllers\OrdersController@search');

//order cancel
$router->POST('/orders/cancel', '\App\Controllers\OrdersController@cancel');
$router->POST('/orders/searchadmin', '\App\Controllers\OrdersController@searchadmin');


//order admin 
$router->get('/orders/admin', '\App\Controllers\OrdersController@indexadmin');


$router->POST('/delivery/update', '\App\Controllers\DeliveryController@update');
$router->POST('/delivery/add', '\App\Controllers\DeliveryController@store');
$router->POST('/delivery/edit', '\App\Controllers\DeliveryController@edit');
$router->POST('/delivery/delete', '\App\Controllers\DeliveryController@delete');

//payment
$router->get('/payments/store', '\App\Controllers\PaymentsController@store');
$router->get('/payments/storeCOD', '\App\Controllers\PaymentsController@storeCOD');


//account
$router->get('/account/detail/(\d+)', '\App\Controllers\UserController@index');

$router->get('/account/update/(\d+)', '\App\Controllers\UserController@updatepage');
$router->POST('/account/update', '\App\Controllers\UserController@update');

$router->get('/account/suspend/(\d+)', '\App\Controllers\UserController@suspendpage');
$router->POST('/account/suspend', '\App\Controllers\UserController@suspend');

$router->get('/account/reactivate', '\App\Controllers\UserController@reactivatepage');
$router->POST('/account/reactivate', '\App\Controllers\UserController@reactivate');

//account admin
$router->get('/account/admin', '\App\Controllers\UserController@adminindex');
$router->get('/account/search', '\App\Controllers\UserController@search');
$router->get('/account/activate/(\d+)', '\App\Controllers\UserController@activatepage');
$router->POST('/account/activate', '\App\Controllers\UserController@activate');

$router->get('/introduction', '\App\Controllers\HomeController@introduction');


//promotion admin 
$router->get('/promotion/admin', '\App\Controllers\PromotionController@index');

$router->POST('/promotions/search', '\App\Controllers\PromotionController@search');

$router->get('/promotion/add', '\App\Controllers\PromotionController@storepage');
$router->POST('/promotion/store', '\App\Controllers\PromotionController@store');

$router->get('/promotions/update/([\w-]+)', '\App\Controllers\PromotionController@updatepage');
$router->POST('/promotion/update', '\App\Controllers\PromotionController@update');

// supplier admin
$router->get('/suppliers/admin', '\App\Controllers\SupplierController@index');

$router->POST('/suppliers/search', '\App\Controllers\SupplierController@search');

$router->get('/suppliers/add', '\App\Controllers\SupplierController@storepage');
$router->POST('/suppliers/store', '\App\Controllers\SupplierController@store');

$router->get('/suppliers/update/([\w-]+)', '\App\Controllers\SupplierController@updatepage');
$router->POST('/suppliers/update', '\App\Controllers\SupplierController@update');

// catalog admin
$router->get('/catalogs/admin', '\App\Controllers\CatalogController@index');

$router->POST('/catalogs/search', '\App\Controllers\CatalogController@search');

$router->get('/catalogs/add', '\App\Controllers\CatalogController@storepage');
$router->POST('/catalogs/store', '\App\Controllers\CatalogController@store');

$router->get('/catalogs/update/([\w-]+)', '\App\Controllers\catalogController@updatepage');
$router->POST('/catalogs/update', '\App\Controllers\catalogController@update');

$router->set404('\App\Controllers\Controller@sendNotFound');
$router->run();