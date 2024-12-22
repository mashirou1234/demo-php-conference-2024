<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cleaning_schedules', static function (Blueprint $table) {
            $table->unsignedBigInteger('standby_cleaner_id')->nullable()->after('property_id');
            $table->foreign('standby_cleaner_id')->references('id')->on('cleaners')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('cleaning_schedules', static function (Blueprint $table) {
            $table->dropForeign(['standby_cleaner_id']);
            $table->dropColumn('standby_cleaner_id');
        });
    }
};
