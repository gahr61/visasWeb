<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OccupationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('occupations')->delete();
        
        DB::table('occupations')->insert([
            ['name' => 'Desarrollador/a'],
            ['name' => 'Diseñador/a'],
            ['name' => 'Ingeniero/a'],
            ['name' => 'Médico/a'],
            ['name' => 'Abogado/a'],
            ['name' => 'Profesor/a'],
            ['name' => 'Arquitecto/a'],
            ['name' => 'Contador/a'],
            ['name' => 'Estudiante'],
            ['name' => 'Empresario/a'],
            ['name' => 'Psicólogo/a'],
            ['name' => 'Trabajador/a Social'],
            ['name' => 'Especialista en Marketing'],
            ['name' => 'Vendedor/a'],
            ['name' => 'Enfermero/a'],
            ['name' => 'Chef'],
            ['name' => 'Jardinero/a'],
            ['name' => 'Electricista'],
            ['name' => 'Plomero/a'],
            ['name' => 'Chofer'],
            ['name' => 'Pintor/a'],
            ['name' => 'Artista'],
            ['name' => 'Músico/a'],
            ['name' => 'Escritor/a'],
            ['name' => 'Científico/a'],
            ['name' => 'Fotógrafo/a'],
            ['name' => 'Periodista'],
            ['name' => 'Otros'],
        ]);
    }
}
