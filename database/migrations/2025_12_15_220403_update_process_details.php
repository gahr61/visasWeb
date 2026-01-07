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
        Schema::table('process_details', function(Blueprint $table){
            $table->boolean('visa_approved')->default(false);
            $table->string('travel_countries')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('process_details', function(Blueprint $table){
            $table->dropColumn('visa_approved');
            $table->dropColumn('travel_countries');
        });
    }
};
