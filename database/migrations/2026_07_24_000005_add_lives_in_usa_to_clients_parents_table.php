<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients_parents', function (Blueprint $table) {
            $table->boolean('lives_in_usa')->default(false)->after('has_visa');
        });
    }

    public function down(): void
    {
        Schema::table('clients_parents', function (Blueprint $table) {
            $table->dropColumn('lives_in_usa');
        });
    }
};
