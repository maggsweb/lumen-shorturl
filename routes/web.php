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
$router->get('/', function () {
    return view('default');
});

$router->group(['middleware' => 'auth'], function () use ($router) {

    // Create new Short URL
    $router->post('/create', 'LinkController@createLink');

    // View Short Urls for User
    $router->post('/link', 'UserController@listLinks');

    // View User history (optionally filtered by short URL)
    $router->post('/user', 'UserController@listUser');

    // Delete Short Url (and associated activity)
    $router->delete('/link', 'LinkController@deleteLink');

    // Delete User Account (and all associated links & activity)
    $router->delete('/user', 'UserController@deleteUser');
});

// Redirect URL
$router->get('/{link}', 'LinkController@redirect');
