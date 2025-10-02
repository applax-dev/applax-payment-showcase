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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->string('image')->nullable();
            $table->string('gateway_product_id')->nullable()->unique();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('gateway_data')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
