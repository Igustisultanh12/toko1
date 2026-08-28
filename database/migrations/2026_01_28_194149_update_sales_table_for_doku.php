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
        Schema::table('sales', function (Blueprint $table) {
        // Jika belum ada kolom status
        if (!Schema::hasColumn('sales', 'status')) {
            $table->string('status')->default('pending')->after('payment_method');
        }
        // Kolom untuk menyimpan nomor referensi dari DOKU
        if (!Schema::hasColumn('sales', 'reference_number')) {
            $table->string('reference_number')->nullable()->after('status');
        }
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
