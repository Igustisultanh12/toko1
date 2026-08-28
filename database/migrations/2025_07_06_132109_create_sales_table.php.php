<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PENGAMAN: Jika tabel users belum ada, buatkan dulu agar sales tidak error
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->string('role')->default('cashier'); // <--- Harus ada ini Pak!
                $table->timestamps();
            });
        }

        // BUAT TABEL SALES DENGAN STRUKTUR BARU
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            
            // Kolom Nomor Transaksi Unik Sesuai Pesanan Bapak
            $table->string('transaction_number')->unique();
            
            // Definisi Manual Kolom User ID
            $table->unsignedBigInteger('user_id'); 
            
            $table->decimal('total_amount', 15, 2);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->string('payment_method')->default('cash');
            $table->string('payment_status')->default('pending');
            $table->timestamps();

            // Pasang kabel Foreign Key secara eksplisit setelah kolom didefinisikan
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};