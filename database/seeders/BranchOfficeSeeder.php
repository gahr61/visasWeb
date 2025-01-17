<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchOfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('branch_office')->delete();

        \DB::table('branch_office')->insert([
            'name' => 'Oficina Rio Nazas'
        ]);
        \DB::table('branch_office')->insert([
            'name' => 'Oficina Rio Panuco'
        ]);
    }
}
