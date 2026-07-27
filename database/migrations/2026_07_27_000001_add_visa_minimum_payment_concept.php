<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $conceptName = 'Pago mínimo de trámite de visa';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $exists = DB::table('sales_concepts')->where('name', $this->conceptName)->exists();

        if ($exists) {
            return;
        }

        $price = 700;

        $id = DB::table('sales_concepts')->insertGetId([
            'name' => $this->conceptName,
            'price' => $price,
            'is_process' => false,
            'is_protected' => true,
            'discount' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sales_concepts_history')->insert([
            'sales_concepts_id' => $id,
            'price' => $price,
            'change_date' => date('Y-m-d'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('sales_concepts')->where('name', $this->conceptName)->delete();
    }
};
