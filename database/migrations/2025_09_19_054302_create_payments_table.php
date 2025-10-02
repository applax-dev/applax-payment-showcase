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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('gateway_payment_id')->nullable()->unique();
            $table->enum('status', [
                'pending', 'processing', 'completed', 'failed',
                'cancelled', 'refunded', 'partially_refunded'
            ])->default('pending');
            $table->enum('method', [
                'card', 'apple_pay', 'google_pay', 'paypal',
                'klarna', 'bank_transfer', 'volt', 'zimpler',
                'alipay', 'wechat', 'moto', 'other'
            ]);
            $table->decimal('amount', 10, 2);
            $table->decimal('refunded_amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->string('reference')->nullable();
            $table->text('failure_reason')->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['method', 'status']);
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
