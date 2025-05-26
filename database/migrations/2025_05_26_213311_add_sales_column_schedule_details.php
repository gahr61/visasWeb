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
        Schema::table('schedule_details', function(Blueprint $table){
            $table->bigInteger('sales_id')->unsigned()->index();

            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('clients_occupations', function(Blueprint $table){
            $table->dropForeign(['sales_id']);
            $table->dropColumn('sales_id');
        });
    }
};
