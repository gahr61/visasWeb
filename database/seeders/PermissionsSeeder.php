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
        //\DB::table('permissions')->truncate();


        \DB::table('permissions')->insert([
            'name'          => 'config_menu',
            'display_name'  => 'Menú Configuración',
            'description'   => 'Permite ver el menu de gestión de permisos, roles, usuarios y sistema(empresa).',
            'guard_name'	=> 'web'
        ]);

        \DB::table('permissions')->insert([
            'name'          => 'config_system_permissions',
            'display_name'  => 'Listado de permisos',
            'description'   => 'Permite ver el listado de los permisos registrados en el sistema.',
            'guard_name'	=> 'web'
        ]);


        \DB::table('permissions')->insert([
            'name'          => 'config_system_roles',
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
            'name'          => 'config_system_roles_create',
            'display_name'  => 'Registro de roles',
            'description'   => 'Permite registrar un nuevo rol en el sistema.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_roles_update',
            'display_name'  => 'Edición de roles',
            'description'   => 'Permite actualizar un rol en el sistema.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_roles_delete',
            'display_name'  => 'Eliminar roles',
            'description'   => 'Permite eliminar un rol en el sistema.',
            'guard_name'	=> 'web'
        ]);

        \DB::table('permissions')->insert([
            'name'          => 'config_system_users',
            'display_name'  => 'Listado Empleados',
            'description'   => 'Permite ver el listado de empleados que se encuentran en el sistema.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_users_create',
            'display_name'  => 'Registro de empleados',
            'description'   => 'Permite registrar un nuevo empleado en el sistema.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_users_update',
            'display_name'  => 'Edición de empleados',
            'description'   => 'Permite actualizar un empleado en el sistema.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_users_delete',
            'display_name'  => 'Eliminar empleados',
            'description'   => 'Permite eliminar un empleado en el sistema.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_users_change_password',
            'display_name'  => 'Actualizar contraseña de empleados',
            'description'   => 'Permite actualizar la contraseña de un empleado en el sistema.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_users_role_assign',
            'display_name'  => 'Asignar rol a usuario',
            'description'   => 'Permite asignar un rol a un usuario desde el formulario.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_users_role_client',
            'display_name'  => 'Ver rol de cliente',
            'description'   => 'Permite ver y asignar rol a un cliente.',
            'guard_name'	=> 'web'
        ]);


        \DB::table('permissions')->insert([
            'name'          => 'config_system_menu',
            'display_name'  => 'Menú Sistema',
            'description'   => 'Permite ver el menu de gestión de sistema (conceptos, comisiones, ocupaciones, sucursales).',
            'guard_name'	=> 'web'
        ]);

         \DB::table('permissions')->insert([
            'name'          => 'config_system_concepts',
            'display_name'  => 'Listado de conceptos',
            'description'   => 'Permite ver el listado de conceptos registrados.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_concepts_create',
            'display_name'  => 'Registro de concepto',
            'description'   => 'Permite registrar un concepto',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_concepts_update',
            'display_name'  => 'Edición de concepto',
            'description'   => 'Permite editar un concepto',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_concepts_history',
            'display_name'  => 'Historial de concepto',
            'description'   => 'Permite ver el historial de un concepto',
            'guard_name'	=> 'web'
        ]);

        \DB::table('permissions')->insert([
            'name'          => 'config_system_commissions',
            'display_name'  => 'Listado de comisiones',
            'description'   => 'Permite ver el listado de comisiones registradas.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_commissions_create',
            'display_name'  => 'Registro de comisión',
            'description'   => 'Permite registrar una comisión',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_commissions_update',
            'display_name'  => 'Edición de comisión',
            'description'   => 'Permite editar una comisión',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_commissions_delete',
            'display_name'  => 'Eliminar una comisión',
            'description'   => 'Permite eliminar una comisión',
            'guard_name'	=> 'web'
        ]);

        \DB::table('permissions')->insert([
            'name'          => 'config_system_occupation',
            'display_name'  => 'Listado de ocupaciones',
            'description'   => 'Permite ver el listado de ocupaciones registradas.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_occupation_create',
            'display_name'  => 'Registro de ocupacion',
            'description'   => 'Permite registrar una ocupacion',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_occupation_update',
            'display_name'  => 'Edición de ocupacion',
            'description'   => 'Permite editar una ocupacion',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_occupation_delete',
            'display_name'  => 'Eliminar una ocupacion',
            'description'   => 'Permite eliminar una ocupacion',
            'guard_name'	=> 'web'
        ]);

        \DB::table('permissions')->insert([
            'name'          => 'config_system_sites',
            'display_name'  => 'Listado de sitios',
            'description'   => 'Permite ver el listado de sitios registrados.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_sites_create',
            'display_name'  => 'Registro de sitio',
            'description'   => 'Permite registrar un sitio',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_sites_update',
            'display_name'  => 'Edición de sitio',
            'description'   => 'Permite editar un sitio',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'config_system_sites_delete',
            'display_name'  => 'Eliminar un sitio',
            'description'   => 'Permite eliminar un sitio',
            'guard_name'	=> 'web'
        ]);

        \DB::table('permissions')->insert([
            'name'          => 'procedure_menu',
            'display_name'  => 'Menú Trámites',
            'description'   => 'Permite ver el menu de trámites (pasaporte, visa).',
            'guard_name'	=> 'web'
        ]);

        \DB::table('permissions')->insert([
            'name'          => 'procedure_passport',
            'display_name'  => 'Listado de pasaportes',
            'description'   => 'Permite ver el listado de pasaportes registrados.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'procedure_passport_create',
            'display_name'  => 'Registro de pasaporte',
            'description'   => 'Permite registrar un pasaporte',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'procedure_passport_update',
            'display_name'  => 'Edición de pasaporte',
            'description'   => 'Permite editar un pasaporte',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'procedure_passport_delete',
            'display_name'  => 'Eliminar un pasaporte',
            'description'   => 'Permite eliminar un pasaporte',
            'guard_name'	=> 'web'
        ]);

        \DB::table('permissions')->insert([
            'name'          => 'procedure_visa',
            'display_name'  => 'Listado de visas',
            'description'   => 'Permite ver el listado de visas registrados.',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'procedure_visa_create',
            'display_name'  => 'Registro de visa',
            'description'   => 'Permite registrar un visa',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'procedure_visa_update',
            'display_name'  => 'Edición de visa',
            'description'   => 'Permite editar un visa',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'procedure_visa_delete',
            'display_name'  => 'Eliminar un visa',
            'description'   => 'Permite eliminar un visa',
            'guard_name'	=> 'web'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'procedure_visa_view_form',
            'display_name'  => 'Ver formulario',
            'description'   => 'Permite ver el formulario de llenado para tramite de visa',
            'guard_name'	=> 'web'
        ]);

       
    }   
}
