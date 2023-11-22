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
        Schema::create('medications', function (Blueprint $table) {
            $table->string('id', 100)->unique();
            $table->string('name');
            $table->string('dosage');
            $table->string('user_id');
            $table->enum('status', ['INACTIVE','ACTIVE','PENDING'])->default('ACTIVE');
            $table->timestamps();
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
