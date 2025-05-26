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

        Schema::create('countries', function(Blueprint $table){
            $table->id();
            $table->string('name', 100);
            $table->timestamps();
        });

        Schema::create('states', function(Blueprint $table){
            $table->id();
            $table->bigInteger('countries_id')->unsigned()->index();
            $table->string('name', 100);
            $table->timestamps();
        });

        Schema::create('clients', function(Blueprint $table){
            $table->id();
            $table->string('names', 100);
            $table->string('lastname1', 100);
            $table->string('lastname2', 100)->nullable();
            $table->string('curp', 100);
            $table->date('birthdate')->nullable();
            $table->enum('sex', ['M', 'F', 'O'])->nullable();
            $table->string('city', 150)->nullable();
            $table->string('ine', 100)->nullable();
            $table->enum('civil_status', ['Casado', 'Soltero', 'Union libre', 'Viudo'])->nullable();
            $table->bigInteger('country_birth_id')->unsigned()->index()->nullable();
            $table->bigInteger('state_birth_id')->unsigned()->index()->nullable();
            $table->string('city_birth', 150)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->boolean('active')->nullable();
            $table->timestamps();

            $table->foreign('country_birth_id')->references('id')->on('countries');
            $table->foreign('state_birth_id')->references('id')->on('states');

        });

        Schema::create('address', function(Blueprint $table){
            $table->id();
            $table->bigInteger('clients_id')->unsigned()->index();
            $table->string('street');
            $table->string('int_number', 20)->nullable();
            $table->string('ext_number', 20);
            $table->string('postal_code', 10);
            $table->string('colony')->nullable();
            $table->string('city')->nullable();
            $table->bigInteger('countries_id')->unsigned()->index();
            $table->bigInteger('states_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('countries_id')->references('id')->on('countries');
            $table->foreign('states_id')->references('id')->on('states');
            $table->foreign('clients_id')->references('id')->on('clients');
        });

        Schema::create('clients_occupation', function(Blueprint $table){
            $table->id();
            $table->bigInteger('clients_id')->unsigned()->index();
            $table->string('job');
            $table->string('name');
            $table->string('address', 350);
            $table->decimal('salary', 8, 2);
            $table->string('antiquity', 50);
            $table->timestamps();

            $table->foreign('clients_id')->references('id')->on('clients')->onDelete('cascade');
        });

        Schema::create('clients_parents', function(Blueprint $table){
            $table->id();
            $table->bigInteger('clients_id')->unsigned()->index();
            $table->string('relationship', 50);
            $table->string('full_name', 200);
            $table->date('birthdate')->nullable();
            $table->boolean('has_visa')->default(false);
            $table->timestamps();

            $table->foreign('clients_id')->references('id')->on('clients')->onDelete('cascade');
        }); 

        Schema::create('clients_phones', function(Blueprint $table){
            $table->id();
            $table->bigInteger('clients_id')->unsigned()->index();
            $table->enum('type', ['Casa', 'Trabajo', 'Celular', 'Otro'])->default('Celular');
            $table->string('number');
            $table->timestamps();

            $table->foreign('clients_id')->references('id')->on('clients')->onDelete('cascade');
        });

        Schema::create('clients_social_networks', function(Blueprint $table){
            $table->id();
            $table->bigInteger('clients_id')->unsigned()->index();
            $table->string('social_network');
            $table->timestamps();
           
            $table->foreign('clients_id')->references('id')->on('clients')->onDelete('cascade');
        });

        Schema::create('clients_studies', function(Blueprint $table){
            $table->id();
            $table->bigInteger('clients_id')->unsigned()->index();
            $table->string('name', 200);
            $table->string('address', 300);
            $table->string('period', 50);
            $table->timestamps();

            $table->foreign('clients_id')->references('id')->on('clients')->onDelete('cascade');
        });

        Schema::create('clients_passport_history', function(Blueprint $table){
            $table->id();
            $table->bigInteger('clients_id')->unsigned()->index();
            $table->string('number', 50);
            $table->date('expedition_date')->nullable();
            $table->date('expiration_date');
            $table->timestamps();

            $table->foreign('clients_id')->references('id')->on('clients')->onDelete('cascade');
        });

        Schema::create('clients_visa_history', function(Blueprint $table){
            $table->id();
            $table->bigInteger('clients_id')->unsigned()->index();
            $table->string('number', 50);
            $table->date('expedition_date')->nullable();
            $table->date('expiration_date');
            $table->timestamps();

            $table->foreign('clients_id')->references('id')->on('clients')->onDelete('cascade');
        });

        Schema::create('schedule_details', function(Blueprint $table){
            $table->id();
            $table->bigInteger('clients_id')->unsigned()->index();
            $table->date('appointment_date');
            $table->string('office', 150);
            $table->string('schedule', 150);
            $table->enum('status', ['Activo', 'Cancelado', 'Finalizado'])->default('Activo');
            $table->string('observations', 300)->nullable();
            $table->timestamps();

            $table->foreign('clients_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_details');
        Schema::dropIfExists('clients_passport_history');
        Schema::dropIfExists('clients_visa_history');
        Schema::dropIfExists('clients_studies');
        Schema::dropIfExists('clients_social_networks');
        Schema::dropIfExists('clients_phones');
        Schema::dropIfExists('clients_parents');
        Schema::dropIfExists('clients_occupation');
        Schema::dropIfExists('address');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('states');
        Schema::dropIfExists('countries');
    }
};
