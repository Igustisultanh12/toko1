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
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address');
            $table->text('customer_notes')->nullable();
            $table->string('courier')->default('J&T Express');
            $table->string('tracking_number')->nullable(); // Nomor Resi
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->string('payment_method')->default('qris');
            $table->string('payment_status')->default('pending'); // pending, paid, failed, expired
            $table->string('status')->default('pending_payment'); // pending_payment, paid, processing, shipped, completed, cancelled
            $table->text('qris_url')->nullable();
            $table->string('doku_invoice_id')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->decimal('price', 14, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 14, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
