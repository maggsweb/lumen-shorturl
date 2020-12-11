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
    $router->post('/create', 'LinkController@create');

    // View Url history
    $router->post('/list', 'LinkController@list');

    // Delete link  (and all associated  activity)  use cascade
    //$router->delete('/link', 'LinkController@delete');

    // View User history
    $router->post('/user', 'UserController@list');

    // Delete User (and all associated links & activity)  use cascade


});

// Redirect URL
$router->get('/{link}', 'LinkController@redirect');
