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
        Schema::create('doctors', function (Blueprint $table) {
            $table->string('id', 100)->unique();
            $table->string('user_id');
            $table->string('doctor_id')->nullable();
            $table->enum('status', ['ACTIVE', 'PENDING', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
