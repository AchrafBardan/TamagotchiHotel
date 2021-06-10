<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Tamagotchi;
use Illuminate\Database\Eloquent\Factories\Factory;

class TamagotchiFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tamagotchi::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'name' => $this->faker->name(),
            'coins' => 0,
            'health'=> 0,
            'level'=> 0,
            'boredom'=> 0,
            'alive'=> true,
        ];
    }


}
