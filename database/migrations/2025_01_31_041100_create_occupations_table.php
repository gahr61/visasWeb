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
        Schema::create('occupations', function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('clients_occupation', function(Blueprint $table){
            $table->dropColumn('job');
            $table->bigInteger('occupations_id')->unsigned()->index()->after('clients_id');

            $table->foreign('occupations_id')->references('id')->on('occupations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients_occupations', function(Blueprint $table){
            $table->dropForeign(['occupations_id']);
            $table->dropColumn('occupations_id');

            $table->string('job');
        });

        Schema::dropIfExists('occupations');
    }
};
