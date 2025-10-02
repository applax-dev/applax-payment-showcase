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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('gateway_client_id')->nullable()->unique();
            $table->json('gateway_data')->nullable();
            $table->timestamp('last_order_at')->nullable();
            $table->timestamps();

            $table->index(['email', 'created_at']);
            $table->index('last_order_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
