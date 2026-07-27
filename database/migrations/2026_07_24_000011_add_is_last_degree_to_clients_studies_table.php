<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients_studies', function (Blueprint $table) {
            $table->boolean('is_last_degree')->default(false)->after('degree_level');
        });
    }

    public function down(): void
    {
        Schema::table('clients_studies', function (Blueprint $table) {
            $table->dropColumn('is_last_degree');
        });
    }
};
