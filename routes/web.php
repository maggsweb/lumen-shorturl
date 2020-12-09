<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Laravel\Lumen\Routing\Router;

/** @var Router $router */
$router->get('/', function () use ($router) {
    // @TODO some sort of default page...
    return $router->app->version();
});

$router->group(['middleware' => 'auth'], function() use ($router) {
    // Create new URL
    $router->post('/create', 'UrlController@create');
    // View Url history
    $router->post('/url', 'UrlController@read');
    // View User history
    $router->post('/user', 'UserController@read');
});

// Redirect URL
$router->post('/{url}', 'UrlController@redirect');
