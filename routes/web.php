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
    return view('default');
});

$router->group(['middleware' => 'auth'], function() use ($router) {

    // Create new Short URL
    $router->post('/create', 'LinkController@createLink');

    // View Short Url redirect history
    $router->post('/link', 'LinkController@listLink');

    // Delete Short Url  (and all associated  activity)
    $router->delete('/link', 'LinkController@deleteLink');

    // View User history
    $router->post('/user', 'UserController@listUser');

    // Delete User (and all associated links & activity)
    $router->delete('/user', 'UserController@deleteUser');

});

// Redirect URL
$router->get('/{link}', 'LinkController@redirect');
