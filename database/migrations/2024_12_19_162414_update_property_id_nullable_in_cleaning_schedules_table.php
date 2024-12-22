<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cleaning_schedules', static function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('cleaning_schedules', static function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable(false)->change();
        });
    }
};
