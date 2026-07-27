<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relationship_types', function(Blueprint $table){
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        $names = [
            'Padre', 'Madre', 'Cónyuge', 'Hermano', 'Hermana', 'Hijo', 'Hija',
            'Abuelo', 'Abuela', 'Nieto', 'Nieta', 'Tío', 'Tía', 'Sobrino', 'Sobrina',
            'Primo', 'Prima', 'Cuñado', 'Cuñada', 'Yerno', 'Nuera', 'Suegro', 'Suegra',
            'Padrastro', 'Madrastra', 'Hermanastro', 'Hermanastra', 'Primo hermano',
            'Primos segundos', 'Padrino/Madrina', 'Ahijado', 'Ahijada', 'Amigo', 'Amiga'
        ];

        $now = now();

        \DB::table('relationship_types')->insert(array_map(fn($name) => [
            'name' => $name, 'created_at' => $now, 'updated_at' => $now
        ], $names));
    }

    public function down(): void
    {
        Schema::dropIfExists('relationship_types');
    }
};
