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
        Schema::create('reminders', function (Blueprint $table) {
            $table->string('id', 100)->unique();
            $table->string('user_id');
            $table->string('medication_id');
            $table->string('dosage_frequency');
            $table->string('next_reminder_at')->nullable();
            $table->enum('status', ['INACTIVE','ACTIVE','PENDING'])->default('ACTIVE');
            $table->timestamps();
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('RESTRICT');
            $table->foreign(['medication_id'])->references(['id'])->on('medications')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
