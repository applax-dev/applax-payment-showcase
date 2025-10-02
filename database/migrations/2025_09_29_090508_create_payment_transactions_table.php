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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique();
            $table->enum('type', [
                'payment', 'refund', 'partial_refund', 'chargeback',
                'reversal', 'fee', 'adjustment'
            ]);
            $table->enum('status', [
                'pending', 'processing', 'completed', 'failed', 'cancelled'
            ])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->string('gateway_transaction_id')->nullable();
            $table->text('description')->nullable();
            $table->text('reason')->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['payment_id', 'created_at']);
            $table->index(['type', 'status']);
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};