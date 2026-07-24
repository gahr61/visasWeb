<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients_studies', function (Blueprint $table) {
            $table->enum('degree_level', ['Primaria', 'Secundaria', 'Preparatoria', 'Licenciatura', 'Maestria', 'Doctorado'])
                ->nullable()->after('name');

            $table->string('street')->nullable()->after('period');
            $table->string('int_number', 20)->nullable()->after('street');
            $table->string('ext_number', 20)->nullable()->after('int_number');
            $table->string('postal_code', 10)->nullable()->after('ext_number');
            $table->string('colony')->nullable()->after('postal_code');
            $table->string('city')->nullable()->after('colony');
            $table->bigInteger('countries_id')->unsigned()->index()->nullable()->after('city');
            $table->bigInteger('states_id')->unsigned()->index()->nullable()->after('countries_id');

            $table->foreign('countries_id')->references('id')->on('countries');
            $table->foreign('states_id')->references('id')->on('states');

            $table->dropColumn('address');
        });
    }

    public function down(): void
    {
        Schema::table('clients_studies', function (Blueprint $table) {
            $table->dropForeign(['countries_id']);
            $table->dropForeign(['states_id']);

            $table->dropColumn([
                'degree_level',
                'street', 'int_number', 'ext_number', 'postal_code', 'colony', 'city',
                'countries_id', 'states_id'
            ]);

            $table->string('address', 300)->nullable();
        });
    }
};
