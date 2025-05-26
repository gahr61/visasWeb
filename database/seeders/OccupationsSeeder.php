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
            

            ['name' => 'Asistente administrativo'],
            ['name' => 'Secretaria'],
            ['name' => 'Recepcionista'],
            ['name' => 'Gerente de oficina'],
            ['name' => 'Contador'],
            ['name' => 'Auditor'],
            ['name' => 'Médico general'],
            ['name' => 'Enfermero/a'],
            ['name' => 'Dentista'],
            ['name' => 'Psicólogo/a'],
            ['name' => 'Técnico en radiología'],
            ['name' => 'Nutricionista'],
            ['name' => 'Maestro/a de primaria'],
            ['name' => 'Profesor/a de secundaria'],
            ['name' => 'Profesor/a universitario'],
            ['name' => 'Auxiliar educativo'],
            ['name' => 'Orientador/a escolar'],
            ['name' => 'Desarrollador/a web'],
            ['name' => 'Ingeniero/a de software'],
            ['name' => 'Analista de sistemas'],
            ['name' => 'Técnico en soporte'],
            ['name' => 'Administrador de redes'],
            ['name' => 'Chef'],
            ['name' => 'Mesero/a'],
            ['name' => 'Bartender'],
            ['name' => 'Estilista'],
            ['name' => 'Conductor/a'],
            ['name' => 'Vigilante'],
            ['name' => 'Albañil'],
            ['name' => 'Carpintero/a'],
            ['name' => 'Electricista'],
            ['name' => 'Plomero/a'],
            ['name' => 'Pintor/a'],
            ['name' => 'Vendedor/a'],
            ['name' => 'Ejecutivo de ventas'],
            ['name' => 'Representante comercial'],
            ['name' => 'Community manager'],
            ['name' => 'Publicista'],
            ['name' => 'Diseñador/a gráfico/a'],
            ['name' => 'Fotógrafo/a'],
            ['name' => 'Actor/actriz'],
            ['name' => 'Músico'],
            ['name' => 'Editor/a de video'],
            ['name' => 'Chofer'],
            ['name' => 'Repartidor/a'],
            ['name' => 'Operador/a de montacargas'],
            ['name' => 'Despachador/a'],
            ['name' => 'Operario/a de producción'],
            ['name' => 'Técnico/a industrial'],
            ['name' => 'Ingeniero/a de procesos'],
            ['name' => 'Ensamblador/a'],
            ['name' => 'Agricultor/a'],
            ['name' => 'Jardinero/a'],
            ['name' => 'Ingeniero/a ambiental'],
            ['name' => 'Veterinario/a'],
            ['name' => 'Otro'],
        ]);
    }
}
