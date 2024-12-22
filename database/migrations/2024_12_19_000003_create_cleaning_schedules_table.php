<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cleaning_schedules', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('cleaner_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->date('scheduled_date'); // スケジュールの日付
            $table->datetime('created_at')->nullable(); // DATETIME型で定義
            $table->datetime('updated_at')->nullable(); // DATETIME型で定義
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cleaning_schedules', static function (Blueprint $table) {
            $table->dropForeign(['cleaner_id']);
            $table->dropForeign(['property_id']);
        });
        Schema::dropIfExists('cleaning_schedules');
    }
};
