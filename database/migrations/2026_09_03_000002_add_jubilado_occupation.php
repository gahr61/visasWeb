<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $occupationName = 'Jubilado';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $existing = DB::table('occupations')->where('name', $this->occupationName)->first();

        if ($existing) {
            DB::table('occupations')->where('id', $existing->id)->update(['is_protected' => true]);

            return;
        }

        DB::table('occupations')->insert([
            'name' => $this->occupationName,
            'is_protected' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('occupations')->where('name', $this->occupationName)->delete();
    }
};
