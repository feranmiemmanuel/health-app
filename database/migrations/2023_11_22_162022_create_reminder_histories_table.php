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
        Schema::create('reminder_histories', function (Blueprint $table) {
            $table->string('id', 100)->unique();
            $table->string('reminder_id');
            $table->string('user_id');
            $table->enum('status', ['SKIPPED','TAKEN','PENDING'])->default('PENDING');
            $table->timestamp('reminded_at');
            $table->timestamps();
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('RESTRICT');
            $table->foreign(['reminder_id'])->references(['id'])->on('reminders')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder_histories');
    }
};
