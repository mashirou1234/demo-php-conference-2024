<?php

namespace Tests\Feature;

use App\Models\Cleaner;
use App\Models\Property;
use App\Models\CleaningSchedule;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->app = require __DIR__.'/../../bootstrap/app.php';
    $this->app->make(Kernel::class)->bootstrap();
    Cleaner::query()->delete();
    Property::query()->delete();
    CleaningSchedule::query()->delete();
});

it('generates cleaning schedules correctly', function () {
    Cleaner::factory()->count(3)->create(['region' => '埼玉南部']);
    Cleaner::factory()->count(8)->create(['region' => '東京23区']);
    Cleaner::factory()->count(3)->create(['region' => '横浜区']);

    Property::factory()->count(12)->create(['region' => '埼玉南部']);
    Property::factory()->count(58)->create(['region' => '東京23区']);
    Property::factory()->count(20)->create(['region' => '横浜区']);

    Artisan::call('cleaning:assign-schedule');

    expect(CleaningSchedule::count())->toBeGreaterThanOrEqual(5);
});

it('assigns cleaners correctly by region', function () {
    Cleaner::factory()->count(3)->create(['region' => '埼玉南部']);
    Cleaner::factory()->count(8)->create(['region' => '東京23区']);
    Cleaner::factory()->count(3)->create(['region' => '横浜区']);

    Property::factory()->count(12)->create(['region' => '埼玉南部']);
    Property::factory()->count(58)->create(['region' => '東京23区']);
    Property::factory()->count(20)->create(['region' => '横浜区']);

    Artisan::call('cleaning:assign-schedule');

    $saitamaSchedules = CleaningSchedule::whereHas('property', static fn ($query) => $query->where('region', '埼玉南部'))->get();
    $tokyoSchedules = CleaningSchedule::whereHas('property', static fn ($query) => $query->where('region', '東京23区'))->get();
    $yokohamaSchedules = CleaningSchedule::whereHas('property', static fn ($query) => $query->where('region', '横浜区'))->get();

    $saitamaProperties = Property::where('region', '埼玉南部')->count();
    $tokyoProperties = Property::where('region', '東京23区')->count();
    $yokohamaProperties = Property::where('region', '横浜区')->count();

    // プロパティの数が正しいか確認
    expect($saitamaProperties)->toBe(12)
        ->and($tokyoProperties)->toBe(58)
        ->and($yokohamaProperties)->toBe(20)
        // クリーナーの数が正しいか確認
        ->and($saitamaSchedules->pluck('cleaner_id')->unique()->count())->toBe(3)
        ->and($tokyoSchedules->pluck('cleaner_id')->unique()->count())->toBe(8)
        ->and($yokohamaSchedules->pluck('cleaner_id')->unique()->count())->toBe(3);

});

it('assigns a standby cleaner correctly', function () {
    Cleaner::factory()->count(3)->create(['region' => '埼玉南部']);
    Cleaner::factory()->count(8)->create(['region' => '東京23区']);
    Cleaner::factory()->count(3)->create(['region' => '横浜区']);

    Property::factory()->count(12)->create(['region' => '埼玉南部']);
    Property::factory()->count(58)->create(['region' => '東京23区']);
    Property::factory()->count(20)->create(['region' => '横浜区']);

    Artisan::call('cleaning:assign-schedule');

    $standbyCleaners = CleaningSchedule::whereNotNull('standby_cleaner_id')->pluck('standby_cleaner_id')->unique();
    expect($standbyCleaners->count())->toBeGreaterThanOrEqual(1);
});

mutates(CleaningSchedule::class);
test('CleaningSchedule が Cleaner に 関連付けられている', function () {
    $cleaner = Cleaner::factory()->create();
    $schedule = CleaningSchedule::factory()->create(['cleaner_id' => $cleaner->id]);

    expect($schedule->cleaner)->toBeInstanceOf(Cleaner::class)
        ->and($schedule->cleaner->id)->toBe($cleaner->id);
});

//mutates(CleaningSchedule::class);
test('Property が CleaningSchedule に 関連付けられている', function () {
    $property = Property::factory()->create();
    $schedule = CleaningSchedule::factory()->create(['property_id' => $property->id]);

    expect($property->schedules->contains($schedule))->toBeTrue();
});

//mutates(CleaningSchedule::class);
test('CleaningSchedule モデル の fillable 属性 を テスト', function () {
    $schedule = new CleaningSchedule();

    $data = [
        'cleaner_id' => 1,
        'property_id' => 1,
        'scheduled_date' => now()->toDateString(),
        'standby_cleaner_id' => 2,
    ];

    $schedule->fill($data);

    expect($schedule->toArray())->toMatchArray($data);
});

mutates(Cleaner::class);
test('Cleaner モデル の fillable 属性 を テスト', function () {
    $cleaner = new Cleaner();

    $data = [
        'name' => '佐藤 太郎',
        'region' => '埼玉南部',
    ];

    $cleaner->fill($data);

    expect($cleaner->toArray())->toMatchArray($data);
});

mutates(Property::class);
test('Property モデル の fillable 属性 を テスト', function () {
    $property = new Property();

    $data = [
        'name' => 'Sample Office',
        'address' => '123 Tokyo St.',
        'region' => '東京23区',
    ];

    $property->fill($data);

    expect($property->toArray())->toMatchArray($data);
});

