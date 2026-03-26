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
        Schema::create('pharmacy_users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name',100);
            $table->string('last_name', 100);
            $table->string('email', 250)->nullable();
            $table->string('mobile',15)->unique();
            $table->enum('gender',['M','F','O'])->comment('M=>Male, F=>Female, O=>other');
            $table->string('registration_no', 500)->unique()->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_users');
    }
};
