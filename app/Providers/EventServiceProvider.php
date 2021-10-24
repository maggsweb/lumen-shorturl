<?php

namespace App\Providers;

use App\Events\ExampleEvent;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ExampleEvent::class => [
            \App\Listeners\ExampleListener::class,
        ],
    ];
}
