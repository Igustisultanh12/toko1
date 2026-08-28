<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'price_at_transaction',
        'discount_at_transaction',
    ];

    /**
     * Casting data agar tipe data angka tetap konsisten.
     * Mencegah error "string vs integer" saat perhitungan matematika.
     */
    protected $casts = [
        'sale_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
        'price_at_transaction' => 'double',
        'discount_at_transaction' => 'double',
    ];

    /**
     * RELASI: Mendapatkan data Produk.
     * Sangat penting untuk menampilkan nama barang di Struk/PDF.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * RELASI: Mendapatkan data Penjualan induk.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * ACCESSOR: Menghitung Harga setelah Diskon.
     * Contoh: $detail->price_after_discount
     */
    public function getPriceAfterDiscountAttribute()
    {
        $discountAmount = ($this->price_at_transaction * $this->discount_at_transaction) / 100;
        return $this->price_at_transaction - $discountAmount;
    }

    /**
     * ACCESSOR: Menghitung Subtotal per item.
     * Rumus: (Harga - Diskon) * Qty
     * Contoh di Blade: {{ number_format($item->subtotal) }}
     */
    public function getSubtotalAttribute()
    {
        return $this->price_after_discount * $this->quantity;
    }
}