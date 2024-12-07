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
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('role',['doctor','client','admin','deactivated'])->default('client');
            $table->string('provider_id')->nullable();
            $table->string('provider_type')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->string('governorate')->nullable();
            $table->string('phone')->nullable()->unique(); 
            $table->integer('age')->nullable();
            $table->enum('gender',['male','female'])->nullable();
            $table->string(column: 'address')->nullable();
            
           

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
