<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_languages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clients_id');
            $table->string('language', 100);
            $table->tinyInteger('percentage');
            $table->timestamps();
            $table->foreign('clients_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_languages');
    }
};
