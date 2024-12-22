<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . 'オフィス',
            'address' => $this->faker->address(),
            'region' => $this->faker->randomElement(['埼玉南部', '東京23区', '横浜区']),
        ];
    }
}
