<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\PetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PetCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => Account::factory(),
            'name' => $this->faker->name(),
        ];
    }
}
