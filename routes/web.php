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
    //return $router->app->version();
    return view('homepage');
});


$router->group(['middleware' => 'auth'], function() use ($router) {

    // Create new URL
    $router->post('/create', 'LinkController@createLink');

    // View Url history
    $router->post('/link', 'LinkController@listLink');

    // Delete link  (and all associated  activity)  use cascade
    $router->delete('/link', 'LinkController@deleteLink');

    // View User history
    $router->post('/user', 'UserController@listUser');

    // Delete User (and all associated links & activity)  use cascade
    $router->delete('/user', 'UserController@deleteUser');

});

// Test
$router->get('/test', 'ExampleController@test');
$router->post('/test', 'ExampleController@create');

// Redirect URL
$router->get('/{link}', 'LinkController@redirect');
