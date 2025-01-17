<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Permission;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('users')->delete();        
        
        $user = User::create([
            'names'=>'Rogelio',
            'lastname1'=>'Gamez',
            'lastname2'=>'',
            'email'=>'sistemas@visas-premier.com',
            'role'=>'administrador_de_sistema',
            'password'=>bcrypt('s0p0rt3'),
            'change_password_required'=>false
        ]);        

        $permisos = Permission::all();
        foreach ($permisos as $permiso) {
        	//asigna permiso a usuario
            $user->givePermissionTo($permiso->name);
            //$rol->givePermissionTo($permission);
     	}

     	//asigna rol a usuario
        $user->assignRole('administrador_de_sistema');
         
    }
}
