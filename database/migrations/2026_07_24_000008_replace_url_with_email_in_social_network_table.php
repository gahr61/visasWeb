<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_network', function (Blueprint $table) {
            $table->string('email')->nullable()->after('username');
            $table->dropColumn('url');
        });
    }

    public function down(): void
    {
        Schema::table('social_network', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->string('url')->nullable();
        });
    }
};
