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
        Schema::table('clients_occupation', function (Blueprint $table) {
            $table->date('hire_date')->nullable()->after('name');
            $table->text('activities_description')->nullable()->after('antiquity');

            $table->string('street')->nullable()->after('activities_description');
            $table->string('int_number', 20)->nullable()->after('street');
            $table->string('ext_number', 20)->nullable()->after('int_number');
            $table->string('postal_code', 10)->nullable()->after('ext_number');
            $table->string('colony')->nullable()->after('postal_code');
            $table->string('city')->nullable()->after('colony');
            $table->bigInteger('countries_id')->unsigned()->index()->nullable()->after('city');
            $table->bigInteger('states_id')->unsigned()->index()->nullable()->after('countries_id');

            $table->foreign('countries_id')->references('id')->on('countries');
            $table->foreign('states_id')->references('id')->on('states');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients_occupation', function (Blueprint $table) {
            $table->dropForeign(['countries_id']);
            $table->dropForeign(['states_id']);

            $table->dropColumn([
                'hire_date', 'activities_description',
                'street', 'int_number', 'ext_number', 'postal_code', 'colony', 'city',
                'countries_id', 'states_id'
            ]);
        });
    }
};
