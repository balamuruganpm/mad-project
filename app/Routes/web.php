<?php
// app/Routes/web.php

use App\Helpers\Router;

$router = new Router();

// Auth routes
$router->add('GET', '/login', 'AuthController@showLogin');
$router->add('POST', '/login', 'AuthController@login');
$router->add('GET', '/logout', 'AuthController@logout');

// Dashboard & Profile routes
$router->add('GET', '/', 'DashboardController@index');
$router->add('GET', '/dashboard', 'DashboardController@index');
$router->add('GET', '/guide', 'DashboardController@guide');
$router->add('GET', '/profile', 'ProfileController@index');
$router->add('POST', '/profile/save-signature', 'ProfileController@saveSignature');
$router->add('POST', '/dashboard/toggle-qr-login', 'DashboardController@toggleQrLogin');

// Document Flow Routes
$router->add('GET', '/departments/design', 'DocumentController@design');
$router->add('POST', '/departments/save-department-links', 'DocumentController@saveDepartmentLinks');
$router->add('GET', '/departments/incoming-qc', 'IncomingQcController@index');
$router->add('POST', '/departments/incoming-qc/store', 'IncomingQcController@store');
$router->add('POST', '/departments/incoming-qc/delete', 'IncomingQcController@delete');
$router->add('GET', '/departments/incoming-qc/add-{category}', 'IncomingQcController@addCategoryOption');
$router->add('POST', '/departments/incoming-qc/add-option', 'IncomingQcController@addOption');
$router->add('GET', '/departments/busbar', 'DocumentController@busbar');
$router->add('GET', '/departments/ct', 'DocumentController@ct');
$router->add('GET', '/departments/pt', 'DocumentController@pt');
$router->add('GET', '/departments/breaker', 'DocumentController@breaker');
$router->add('GET', '/departments/final-assembly', 'DocumentController@finalAssembly');
$router->add('POST', '/departments/final-assembly/create-job', 'DocumentController@createJob');
$router->add('POST', '/departments/final-assembly/save-links', 'DocumentController@saveLinks');
$router->add('POST', '/departments/upload', 'DocumentController@upload');
$router->add('POST', '/departments/delete-doc', 'DocumentController@deleteDoc');

// Public access (For QR codes)
$router->add('GET', '/public/final-assembly/{id}', 'DocumentController@publicFinalAssembly');
$router->add('GET', '/public/view/{token}', 'DocumentController@publicView');

return $router;


