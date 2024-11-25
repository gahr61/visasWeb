<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('permissions')->delete();


        \DB::table('permissions')->insert([
            'name'          => 'config_menu',
            'display_name'  => 'Menú Configuración',
            'description'   => 'Permite ver el menu de gestión de permisos, roles, usuarios y sistema(empresa).',
            'guard_name'	=> 'web'
        ]);

        \DB::table('permissions')->insert([
            'name'          => 'config_permissions',
            'display_name'  => 'Listado de permisos',
            'description'   => 'Permite ver el listado de los permisos registrados en el sistema.',
            'guard_name'	=> 'web'
        ]);


        \DB::table('permissions')->insert([
            'name'          => 'config_roles',
            'display_name'  => 'Listado Roles',
            'description'   => 'Permite ver el listado de roles que se encuentran en el sistema.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_permissions_assign',
            'display_name'  => 'Asignar permisos a rol',
            'description'   => 'Permite asignar permisos a un rol.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_roles_create',
            'display_name'  => 'Registro de roles',
            'description'   => 'Permite registrar un nuevo rol en el sistema.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_roles_update',
            'display_name'  => 'Edición de roles',
            'description'   => 'Permite actualizar un rol en el sistema.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_roles_delete',
            'display_name'  => 'Eliminar roles',
            'description'   => 'Permite eliminar un rol en el sistema.',
            'guard_name'	=> 'web'
        ]);

        \DB::table('permissions')->insert([
            'name'          => 'config_users',
            'display_name'  => 'Listado Empleados',
            'description'   => 'Permite ver el listado de empleados que se encuentran en el sistema.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_users_create',
            'display_name'  => 'Registro de empleados',
            'description'   => 'Permite registrar un nuevo empleado en el sistema.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_users_update',
            'display_name'  => 'Edición de empleados',
            'description'   => 'Permite actualizar un empleado en el sistema.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_users_delete',
            'display_name'  => 'Eliminar empleados',
            'description'   => 'Permite eliminar un empleado en el sistema.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_users_change_password',
            'display_name'  => 'Actualizar contraseña de empleados',
            'description'   => 'Permite actualizar la contraseña de un empleado en el sistema.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_users_role_assign',
            'display_name'  => 'Asignar rol a usuario',
            'description'   => 'Permite asignar un rol a un usuario desde el formulario.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_users_role_client',
            'display_name'  => 'Ver rol de cliente',
            'description'   => 'Permite ver y asignar rol a un cliente.',
            'guard_name'	=> 'web'
        ]);


        \DB::table('permissions')->insert([
            'name'          => 'config_sites',
            'display_name'  => 'Listado de sitios',
            'description'   => 'Permite ver el listado de sitios registrados.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_sites_create',
            'display_name'  => 'Registro de sitio',
            'description'   => 'Permite registrar un sitio',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_sites_update',
            'display_name'  => 'Edición de sitio',
            'description'   => 'Permite editar un sitio',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_sites_delete',
            'display_name'  => 'Eliminar un sitio',
            'description'   => 'Permite eliminar un sitio',
            'guard_name'	=> 'web'
        ]);
       
    }
}
