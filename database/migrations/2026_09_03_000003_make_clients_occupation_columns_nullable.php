<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE clients_occupation MODIFY name VARCHAR(255) NULL');
        DB::statement('ALTER TABLE clients_occupation MODIFY salary DECIMAL(8,2) NULL');
        DB::statement('ALTER TABLE clients_occupation MODIFY antiquity VARCHAR(50) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE clients_occupation MODIFY name VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE clients_occupation MODIFY salary DECIMAL(8,2) NOT NULL');
        DB::statement('ALTER TABLE clients_occupation MODIFY antiquity VARCHAR(50) NOT NULL');
    }
};
