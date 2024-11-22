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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('names', 100);
            $table->string('lastname1', 100);
            $table->string('lastname2', 100)->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role', 100);
            $table->boolean('active')->default(true);
            $table->boolean('change_password_required')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('users_details', function(Blueprint $table){
            $table->id();
            $table->bigInteger('users_id')->unsigned()->index();
            $table->decimal('goal', 8, 2)->default(0);
            $table->decimal('salary', 8, 2)->default(0);
            $table->timestamps();

            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('commissions', function(Blueprint $table){
            $table->id();
            $table->string('concept', 200);
            $table->timestamps();
        });

        Schema::create('users_commisions', function(Blueprint $table){
            $table->id();
            $table->bigInteger('commissions_id')->unsigned()->index();
            $table->bigInteger('users_id')->unsigned()->index();
            $table->decimal('amount', 8, 2)->default(0);

            $table->foreign('commissions_id')->references('id')->on('commissions');
            $table->foreign('users_id')->references('id')->on('users');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->bigInteger('users_id')->unsigned()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();

            $table->foreign('users_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
