<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\SalesConcepts;

class ConceptsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('sales_concepts')->delete();

        $concept = new SalesConcepts();
        $concept->name = 'Visa - Trámite y Transporta';
        $concept->price = 3000;
        $concept->is_process = true;
        $concept->save();

        \DB::table('sales_concepts_history')->insert([
            'sales_concepts_id' => $concept->id,
            'price' => $concept->price,
            'change_date' => date('Y-m-d')
        ]);

        $concept = new SalesConcepts();
        $concept->name = 'Visa - Trámite';
        $concept->price = 1950;
        $concept->is_process = true;
        $concept->save();

        \DB::table('sales_concepts_history')->insert([
            'sales_concepts_id' => $concept->id,
            'price' => $concept->price,
            'change_date' => date('Y-m-d')
        ]);

        $concept = new SalesConcepts();
        $concept->name = 'Pasaporte';
        $concept->price = 350;
        $concept->is_process = true;
        $concept->save();

        \DB::table('sales_concepts_history')->insert([
            'sales_concepts_id' => $concept->id,
            'price' => $concept->price,
            'change_date' => date('Y-m-d')
        ]);
    }
}
