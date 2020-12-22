<?php

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
//    use \Laravel\Lumen\Testing\DatabaseMigrations;
    use \Laravel\Lumen\Testing\DatabaseTransactions;

    /**
     * @var User
     */
    protected $user;

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication(): Application
    {

        return require __DIR__.'/../bootstrap/app.php';
//        $app = require __DIR__ . '/../bootstrap/app.php';
//        $app->alias(\Illuminate\Http\Request::class, 'request');
//        return $app;
    }

    /**
     * Setup test User
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        // ->make(); does not save model to db

//        app(Request::class);

    }

}
