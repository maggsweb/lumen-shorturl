<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'uuid'        => Str::uuid()->toString(),
            'status'      => $this->faker->randomElement(['Active', 'InActive']),
            'name'        => $this->faker->name,
            'email'       => $this->faker->safeEmail,
            'password'    => Hash::make($this->faker->password),
            'application' => $this->faker->company,
            'created_at'  => Carbon::now(),
        ];
    }
}
