<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cleaner;
use App\Models\Property;
use App\Models\CleaningSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AssignCleaningSchedule extends Command
{
    protected $signature = 'cleaning:assign-schedule';
    protected $description = '1週間の清掃スケジュールを生成します';

    public function handle(): void
    {
        $startDate = Carbon::now()->next('Monday');
        $endDate = $startDate->copy()->addWeek()->subDay(2); // 金曜日まで

        $weekdays = $startDate->toPeriod($endDate, '1 day');
        $cleaners = Cleaner::all();
        $properties = Property::all();

        if ($cleaners->isEmpty() || $properties->isEmpty()) {
            $this->error('清掃担当者または物件が登録されていません。');
            Log::error('清掃担当者または物件が登録されていません。スケジュール生成を中止しました。');
            return;
        }

        $assignments = [];
        $standbyCleaners = [];

        foreach ($weekdays as $date) {
            $this->info("{$date->format('Y-m-d')} のスケジュールを生成中...");
            $dailyAssignments = $this->assignDailySchedule($cleaners, $properties, $date);
            $this->info("スケジュール生成件数: " . count($dailyAssignments));

            foreach ($dailyAssignments as $assignment) {
                $assignments[] = $assignment;
            }

            // 予備担当者を設定
            $standbyCleaner = $this->assignStandbyCleaner($cleaners);
            if ($standbyCleaner) {
                $standbyCleaners[] = [
                    'scheduled_date' => $date->format('Y-m-d'),
                    'standby_cleaner_id' => $standbyCleaner->id,
                ];
            } else {
                Log::warning("予備担当者が見つかりませんでした。対象日: {$date->format('Y-m-d')}");
            }
        }

        // スケジュールをデータベースに保存
        CleaningSchedule::insert($assignments);

        // 予備担当者もスケジュールとして保存
        foreach ($standbyCleaners as $standby) {
            CleaningSchedule::create([
                'scheduled_date' => $standby['scheduled_date'],
                'standby_cleaner_id' => $standby['standby_cleaner_id'],
                'cleaner_id' => null, // 予備担当者なのでNULLに設定
                'property_id' => null, // 物件も割り当てない
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->info('スケジュール生成完了！');
        Log::info('清掃スケジュールが正常に生成されました。');
    }

    private function assignDailySchedule($cleaners, $properties, $date): array
    {
        $assignments = [];
        $regions = [
            '埼玉南部' => 3,
            '東京23区' => 8,
            '横浜区' => 3,
        ];

        foreach ($regions as $region => $requiredCleaners) {
            $regionCleaners = $cleaners->where('region', $region)->shuffle()->take($requiredCleaners);
            $regionProperties = $properties->where('region', $region)->shuffle();

            foreach ($regionCleaners as $cleaner) {
                $property = $regionProperties->pop();

                if ($property) {
                    $assignments[] = [
                        'cleaner_id' => $cleaner->id,
                        'property_id' => $property->id,
                        'scheduled_date' => $date->format('Y-m-d'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        return $assignments;
    }

    private function assignStandbyCleaner($cleaners)
    {
        return $cleaners->shuffle()->first();
    }
}
