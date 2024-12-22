<?php

namespace Database\Factories;

use App\Models\Cleaner;
use Illuminate\Database\Eloquent\Factories\Factory;

class CleanerFactory extends Factory
{
    protected $model = Cleaner::class;

    public function definition(): array
    {
        // 手動で日本語の名前とエリアを指定
        $lastNames = ['佐藤', '鈴木', '高橋', '田中', '伊藤'];
        $firstNames = ['太郎', '花子', '次郎', '美香', '健太'];
        $regions = ['埼玉南部', '東京23区', '横浜区'];

        return [
            'name' => $lastNames[array_rand($lastNames)] . ' ' . $firstNames[array_rand($firstNames)], // ランダムな名前を生成
            'region' => $regions[array_rand($regions)], // ランダムなエリアを選択
        ];
    }
}
