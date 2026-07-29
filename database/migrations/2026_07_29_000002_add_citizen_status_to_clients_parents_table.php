<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients_parents', function (Blueprint $table) {
            $table->enum('citizen_status', ['Ciudadano', 'Residente'])->nullable()->after('lives_in_usa');
        });
    }

    public function down(): void
    {
        Schema::table('clients_parents', function (Blueprint $table) {
            $table->dropColumn('citizen_status');
        });
    }
};
