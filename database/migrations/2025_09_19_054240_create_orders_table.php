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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('gateway_order_id')->nullable()->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->enum('status', [
                'draft', 'pending', 'issued', 'viewed', 'paid', 'failed',
                'cancelled', 'expired', 'overdue', 'hold',
                'received', 'rejected', 'in_progress'
            ])->default('draft');
            $table->decimal('total', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->json('gateway_data')->nullable();
            $table->json('payment_urls')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['customer_id', 'status']);
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
