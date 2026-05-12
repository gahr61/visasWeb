<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_details', function (Blueprint $table) {
            $table->string('city', 150)->nullable()->after('office');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_details', function (Blueprint $table) {
            $table->dropColumn('city');
        });
    }
};
