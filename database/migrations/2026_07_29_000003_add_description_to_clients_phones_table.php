<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients_phones', function (Blueprint $table) {
            $table->string('description', 150)->nullable()->after('number');
        });
    }

    public function down(): void
    {
        Schema::table('clients_phones', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
