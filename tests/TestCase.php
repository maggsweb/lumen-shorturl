<?php

use App\Models\Activity;
use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var User
     */
    protected $alt_user;

    /**
     * Links.
     *
     * @var Collection
     */
    protected $links;

    /**
     * Activity.
     *
     * @var Collection
     */
    protected $activity;

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

        $this->user = User::factory()->create([
            'email'    => 'test@apiuser.com',
            'password' => Hash::make('password'),
            'status'   => 'Active',
        ]); // ->make(); does not save model to db

        $this->user->basicAuthString = base64_encode("{$this->user->email}:password");

        $this->alt_user = User::factory()->create();

        $this->links = Link::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $this->activity = collect();

        $this->links->each(function (Link $link) {
            $activity = Activity::factory()->create([
                'user_id' => $this->user->id,
                'link_id' => $link->id,
                'action'  => 'Create',
            ]);
            $this->activity->add($activity);
        });
    }
}
