<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('countries')->delete();

        // Ruta al archivo JSON
        $json = File::get(database_path('data/countries.json'));

        // Remove BOM if it exists
        if (substr($json, 0, 3) === "\xEF\xBB\xBF") {
            $json = substr($json, 3);
        }

        $countries = json_decode($json, true)['countries'];

        if ($countries === null) {
            throw new \Exception("Invalid JSON data in countries.json.");
        }
        
        // Insertar datos en la tabla
        foreach ($countries as $country) {
            DB::table('countries')->insert([
                'id' => $country['id'],
                'name' => $country['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
