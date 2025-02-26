<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_confirm_token', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sales_id')->unsigned()->index();
            $table->string('token');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_confirm_token');
    }
};
