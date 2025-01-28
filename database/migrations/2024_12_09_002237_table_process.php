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
        Schema::create('process', function(Blueprint $table){
            $table->id();
            $table->bigInteger('clients_id')->unsigned()->index();
            $table->enum('type', ['Pasaporte', 'Visa']);
            $table->enum('subtype', ['Primera vez', 'Renovación']);
            $table->enum('age_type', ['Mayor de edad', 'Menor de edad']);
            $table->enum('option_type', ['Vencida o por vencer', 'Extraviado', 'Deteriorado', 'Cambio de datos', 'Vencido +4 años']);
            $table->enum('visa_type', ['Turista', 'Trabajo'])->nullabe();
            $table->string('observations', 300)->nullable();
            $table->boolean('complete')->default(false);
            $table->timestamps();

            $table->foreign('clients_id')->references('id')->on('clients')->onDelete('cascade');
        });

        Schema::create('process_status', function(Blueprint $table){
            $table->id();
            $table->bigInteger('process_id')->unsigned()->index();
            $table->enum('status', ['Inicio', 'Por pagar', 'Informado', 'Pagado', 'Entregado', 'Finalizado']);
            $table->boolean('is_last')->default(true);
            $table->timestamps();

            $table->foreign('process_id')->references('id')->on('process')->onDelete('cascade');
        });

        Schema::create('process_history', function(Blueprint $table){
            $table->id();
            $table->bigInteger('process_id')->unsigned()->index();
            $table->boolean('has_tried_visa')->default(false);
            $table->date('date')->nullable();
            $table->string('observations', 500);
            $table->timestamps();

            $table->foreign('process_id')->references('id')->on('process')->onDelete('cascade');
        });

        Schema::create('process_client_companion', function(Blueprint $table){
            $table->id();
            $table->bigInteger('process_id')->unsigned()->index();
            $table->string('full_name');
            $table->timestamps();

            $table->foreign('process_id')->references('id')->on('process')->onDelete('cascade');
        });

        Schema::create('residence', function(Blueprint $table){
            $table->id();
            $table->bigInteger('process_id')->unsigned()->index();
            $table->string('full_name');
            $table->string('cel_phone', 50);
            $table->string('work_phone', 50)->nullable();
            $table->string('personal_phone', 50)->nullable();
            $table->timestamps();

            $table->foreign('process_id')->references('id')->on('process')->onDelete('cascade');
        });

        Schema::create('passport', function(Blueprint $table){
            $table->id();
            $table->bigInteger('process_id')->unsigned()->index();
            $table->bigInteger('states_id')->unsigned()->index()->nullable();
            $table->string('number', 50);
            $table->date('expedition_date')->nullable();
            $table->date('expiration_date');
            $table->bigInteger('expedition_countries_id')->unsigned()->index()->nullable();
            $table->bigInteger('expedition_states_id')->unsigned()->index()->nullable();
            $table->string('expedition_city')->nullable();
            $table->timestamps();

            $table->foreign('process_id')->references('id')->on('process')->onDelete('cascade');
        });

        Schema::create('process_documents', function(Blueprint $table){
            $table->id();
            $table->string('type');
            $table->string('documents', 400);
            $table->timestamps();
        });

        Schema::create('visas', function(Blueprint $table){
            $table->id();
            $table->bigInteger('process_id')->unsigned()->index();
            $table->bigInteger('states_id')->unsigned()->index()->nullable();
            $table->string('number', 50);
            $table->date('expedition_date')->nullable();
            $table->date('expiration_date');
            $table->bigInteger('expedition_countries_id')->unsigned()->index()->nullable();
            $table->bigInteger('expedition_states_id')->unsigned()->index()->nullable();
            $table->string('expedition_city')->nullable();
            $table->timestamps();

            $table->foreign('process_id')->references('id')->on('process')->onDelete('cascade');
        });

        Schema::create('visas_details', function(Blueprint $table){
            $table->id();
            $table->bigInteger('visas_id')->unsigned()->index();
            $table->date('travel_date')->nullable();
            $table->string('addresS_eeuu', 350)->nullable();
            $table->date('travel_date_eeuu')->nullable();
            $table->string('time_stay_eeuu', 100)->nullable();
            $table->string('travel_reason', 300)->nullable();
            $table->string('cover_expenses', 200)->nullable();//quien cubre el costo del viaje
            $table->boolean('has_visit_eeuu')->default(false);
            $table->date('date_visit_eeuu')->nullable();
            $table->string('time_visit_eeuu', 100)->nullable();
            $table->string('clave_ds_160', 100)->nullable();
            $table->boolean('travel_before')->default(false); //ha viajado en los ulstimos 7 años a otro pais
            $table->string('travel_before_countries', 400)->nullable(); //pais a los que ha viajado
            $table->timestamps();

            $table->foreign('visas_id')->references('id')->on('visas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visas_details');
        Schema::dropIfExists('visas');
        Schema::dropIfExists('process_documents');
        Schema::dropIfExists('passport');
        Schema::dropIfExists('residence');
        Schema::dropIfExists('process_client_companion');
        Schema::dropIfExists('process_history');
        Schema::dropIfExists('process_status');
        Schema::dropIfExists('process');
    }
};
