<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $permissionName = 'procedure_visa_view_password';

    public function up(): void
    {
        $exists = DB::table('permissions')->where('name', $this->permissionName)->exists();

        if ($exists) {
            return;
        }

        DB::table('permissions')->insert([
            'name'         => $this->permissionName,
            'display_name' => 'Ver contraseña de cuenta DS-160',
            'description'  => 'Permite ver la contraseña de la cuenta del portal DS-160 de un cliente',
            'guard_name'   => 'web',
        ]);
    }

    public function down(): void
    {
        DB::table('permissions')->where('name', $this->permissionName)->delete();
    }
};
