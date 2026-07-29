<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE schedule_details
            MODIFY status ENUM('Activo', 'Cancelado', 'Finalizado', 'Pendiente', 'Confirmado', 'Completado') NOT NULL DEFAULT 'Activo'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE schedule_details
            MODIFY status ENUM('Activo', 'Cancelado', 'Finalizado') NOT NULL DEFAULT 'Activo'
        ");
    }
};
