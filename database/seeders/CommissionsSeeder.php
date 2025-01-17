<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('commissions')->delete();

        \DB::table('commissions')->insert([
            'concept'=>'Visa'
        ]);
        \DB::table('commissions')->insert([
            'concept'=>'Pasaporte'
        ]);
        \DB::table('commissions')->insert([
            'concept'=>'Pasaporte compartido'
        ]);
    }
}
