<?php

namespace Database\Factories;

use App\Models\Activity;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\Request;

class ActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => null,
            'link_id' => null,
            'action' => null,
            'created_at'=> Carbon::now(),
            'ip_address'=> $this->faker->ipv4,
        ];
    }
}
