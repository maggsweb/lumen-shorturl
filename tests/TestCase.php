<?php

use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use \Laravel\Lumen\Testing\DatabaseMigrations;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var User
     */
    protected $alt_user;

    /**
     * @var array Links
     */
    protected $links;

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * Setup test User.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(); // ->make(); does not save model to db
        $this->alt_user = User::factory()->create();

        $this->links = Link::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);
    }
}
