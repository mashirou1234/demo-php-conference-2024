<?php

namespace App\Providers;

use Faker\Provider\ja_JP\Person;
use Illuminate\Support\ServiceProvider;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(FakerGenerator::class, function () {
            $faker = FakerFactory::create('ja_JP'); // 日本語ロケール
            $faker->addProvider(new Person($faker));
            return $faker;
        });
    }
}
