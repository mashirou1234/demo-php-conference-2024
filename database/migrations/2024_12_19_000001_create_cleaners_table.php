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
        Schema::create('cleaners', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('region'); // 関東首都圏、23区、横浜区など
            $table->datetime('created_at')->nullable(); // DATETIME型で定義
            $table->datetime('updated_at')->nullable(); // DATETIME型で定義
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cleaners');
    }
};
