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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('branch_offices_id')->unsigned()->index();
            $table->date('date');
            $table->string('folio', 100);
            $table->decimal('total', 8, 2);
            $table->boolean('is_familiar')->default(false);
            $table->timestamps();

            $table->foreign('branch_offices_id')->references('id')->on('branch_office');
        });

        Schema::create('sales_concepts', function(Blueprint $table){
            $table->id();
            $table->string('name', 200);
            $table->decimal('price', 8, 2);
            $table->boolean('is_process')->default(false);
            $table->decimal('discount', 8, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('sales_details', function(Blueprint $table){
            $table->id();
            $table->bigInteger('sales_id')->unsigned()->index();
            $table->bigInteger('sales_concepts_id')->unsigned()->index();
            $table->decimal('amount');
            $table->timestamps();

            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('sales_concepts_id')->references('id')->on('sales_concepts')->onDelete('cascade');
        });

        Schema::create('sales_payment', function(Blueprint $table){
            $table->id();
            $table->bigInteger('sales_id')->unsigned()->index();
            $table->string('uuid')->nullable();
            $table->enum('method_payment', ['Efectivo', 'Tarjeta'])->default('Efectivo');
            $table->decimal('amount', 8, 2);
            $table->string('url_receipt', 200)->nullable();
            $table->string('url_payment', 200)->nullable();
            $table->enum('status', ['Cancelado', 'Pagado', 'Pendiente'])->default('Pendiente');
            $table->string('platform', 200);
            $table->string('receipt', 100);
            $table->timestamps();

            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');
        });

        Schema::create('sales_process', function(Blueprint $table){
            $table->id();
            $table->bigInteger('sales_id')->unsigned()->index();
            $table->enum('status', ['Completo', 'Incompleto'])->default('Incompleto');
            $table->decimal('advance_payment', 8, 2);
            $table->decimal('payable', 8, 2);
            $table->enum('contact', ['Teléfono', 'Correo electrónico', 'Volante', 'Redes sociales', 'Visita presencial', 'Otro', 'Web']);
            $table->timestamps();

            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');
        });

        Schema::create('sales_procces_account', function(Blueprint $table){
            $table->id();
            $table->bigInteger('sales_id')->unsigned()->index();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();

            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');
        });

        Schema::create('sales_status', function(Blueprint $table){
            $table->id();
            $table->bigInteger('sales_id')->unsigned()->index();
            $table->enum('status', ['Activo', 'Ficha pendiente', 'Con ficha', 'Ficha pagada', 'Finalizado'])->default('Activo');
            $table->boolean('is_last')->default(true);
            $table->timestamps();

            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');
        });

        Schema::create('sales_users', function(Blueprint $table){
            $table->id();
            $table->bigInteger('users_id')->unsigned()->index();
            $table->bigInteger('sales_id')->unsigned()->index();

            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');
        });

        

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_process');
        Schema::dropIfExists('sales_payment');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('sales_concepts');
    }
};
