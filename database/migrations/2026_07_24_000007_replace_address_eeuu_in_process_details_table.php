<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('process_details', function (Blueprint $table) {
            $table->string('street_eeuu')->nullable()->after('address_eeuu');
            $table->string('number_eeuu', 20)->nullable()->after('street_eeuu');
            $table->string('city_eeuu')->nullable()->after('number_eeuu');
            $table->bigInteger('state_eeuu')->unsigned()->index()->nullable()->after('city_eeuu');
            $table->string('zip_eeuu', 10)->nullable()->after('state_eeuu');

            $table->foreign('state_eeuu')->references('id')->on('states');

            $table->dropColumn('address_eeuu');
        });
    }

    public function down(): void
    {
        Schema::table('process_details', function (Blueprint $table) {
            $table->dropForeign(['state_eeuu']);
            $table->dropColumn(['street_eeuu', 'number_eeuu', 'city_eeuu', 'state_eeuu', 'zip_eeuu']);
            $table->string('address_eeuu', 350)->nullable();
        });
    }
};
