<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('states')->delete();

         // Ruta al archivo JSON
         $json = File::get(database_path('data/states.json'));
         
         // Remove BOM if it exists
        if (substr($json, 0, 3) === "\xEF\xBB\xBF") {
            $json = substr($json, 3);
        }

        $states = json_decode($json, true)['states'];

        if ($states === null) {
            throw new \Exception("Invalid JSON data in countries.json.");
        }

 
         // Insertar datos en la tabla
         foreach ($states as $state) {
             DB::table('states')->insert([
                 'id' => $state['id'],
                 'countries_id' => $state['id_country'],
                 'name' => $state['name'],
                 'created_at' => now(),
                 'updated_at' => now(),
             ]);
         }

    }
}
