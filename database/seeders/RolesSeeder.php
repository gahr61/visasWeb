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

		Role::create([
			'name'          => 'administrador_de_empresa',
			'display_name'	=>	'Administrador de empresa',             
			'description'   =>  'Permite mostrar los modulos de administración de empresa',
			'guard_name'    => 'web'
       	]);

		Role::create([
			'name'          => 	'volanteo',
			'display_name'	=>	'Volanteo',             
			'description'   =>  'Permite mostrar el modulo de reportes para usuarios',
			'guard_name'    => 	'web'
       	]);

		Role::create([
			'name'          => 'ventas',
			'display_name'	=>	'Ventas',             
			'description'   =>  'Permite mostrar el modulo de trámites y ventas',
			'guard_name'    => 'web'
       	]);
    }
}
