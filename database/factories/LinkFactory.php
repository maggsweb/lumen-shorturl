<?php

namespace Database\Factories;

use App\Models\Link;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Link::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id'   => null, // pass in at create
            'short'     => $this->faker->regexify('[A-Za-z]{10}'),
            'long'      => $this->faker->url,
            'created_at'=> Carbon::now(),
        ];
    }
}
