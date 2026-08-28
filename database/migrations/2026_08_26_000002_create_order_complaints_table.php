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
        Schema::create('order_complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('order_number')->index();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('reason'); // Alasan komplain
            $table->text('description'); // Keterangan detail
            $table->string('expected_solution'); // Solusi yang diharapkan
            $table->json('photos')->nullable(); // Foto bukti (multi-image max 25MB total)
            $table->string('video')->nullable(); // Video bukti (max 15MB)
            $table->string('status')->default('pending'); // pending, reviewed, approved, rejected, resolved
            $table->text('admin_notes')->nullable(); // Catatan balasan admin/kasir
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_complaints');
    }
};
