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
        Schema::table('clients_parents', function (Blueprint $table) {
            $table->bigInteger('country_birth_id')->unsigned()->index()->nullable()->after('birthdate');
            $table->bigInteger('state_birth_id')->unsigned()->index()->nullable()->after('country_birth_id');

            $table->foreign('country_birth_id')->references('id')->on('countries');
            $table->foreign('state_birth_id')->references('id')->on('states');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients_parents', function (Blueprint $table) {
            $table->dropForeign(['country_birth_id']);
            $table->dropForeign(['state_birth_id']);
            $table->dropColumn(['country_birth_id', 'state_birth_id']);
        });
    }
};
