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
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
            $table->string('gateway_event_id')->nullable();
            $table->json('payload');
            $table->string('signature')->nullable();
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamps();

            $table->index(['event_type', 'status']);
            $table->index(['status', 'next_retry_at']);
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
