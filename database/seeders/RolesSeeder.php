<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('roles')->delete();
     	$role = Role::create([
            'name'          => 'administrador_de_sistema',
     		'display_name'	=>	'Administrador de sistema',             
            'description'   =>  'Permite utilizar y administrar todos los modulos del sistema',
            'guard_name'    => 'web'
     	]);

     	$permisos = Permission::all();
     	foreach ($permisos as $permiso) {
            $role->givePermissionTo($permiso);
     	}
    }
}
