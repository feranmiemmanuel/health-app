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
        Schema::create('user_hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id')->nullable();
            $table->string('doctor_id')->nullable();
            $table->string('hospital_id');
            $table->timestamps();
            $table->foreign(['patient_id'])->references(['id'])->on('users')->onDelete('RESTRICT');
            $table->foreign(['doctor_id'])->references(['id'])->on('users')->onDelete('RESTRICT');
            $table->foreign(['hospital_id'])->references(['id'])->on('hospitals')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_hospitals');
    }
};
