<?php

namespace Database\Factories;

use App\Models\CleaningSchedule;
use App\Models\Cleaner;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class CleaningScheduleFactory extends Factory
{
    protected $model = CleaningSchedule::class;

    public function definition(): array
    {
        return [
            'cleaner_id' => Cleaner::factory(), // Cleanerを自動生成
            'property_id' => Property::factory(), // Propertyを自動生成
            'scheduled_date' => $this->faker->date(),
            'standby_cleaner_id' => null, // 必要に応じて変更
        ];
    }
}
