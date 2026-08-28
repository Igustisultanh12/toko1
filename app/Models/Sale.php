<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    /**
     * Izinkan kolom-kolom ini diisi secara massal.
     * Harus sinkron dengan migrasi 'Super Clean' kita.
     */
    protected $fillable = [
        'transaction_number',
        'user_id',
        'customer_name',
        'total_amount',
        'amount_paid',
        'payment_method',
        'payment_status',
        'status',
        'reference_number',
    ];

    /**
     * Konversi tipe data otomatis.
     */
    protected $casts = [
        'total_amount' => 'double',
        'amount_paid'  => 'double',
        'created_at'   => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * HELPER: Hitung kembalian secara otomatis (tanpa perlu kolom di DB).
     */
    public function getChangeAmountAttribute()
    {
        return max(0, $this->amount_paid - $this->total_amount);
    }

    /**
     * RELASI KE USER (KASIR)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELASI KE DETAIL PENJUALAN
     */
    public function details()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id');
    }

    /**
     * FORMAT MATA UANG (ACCESSORS)
     */
    public function getFormattedTotalAttribute() {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedPaidAttribute() {
        return 'Rp ' . number_format($this->amount_paid, 0, ',', '.');
    }

    public function getFormattedChangeAttribute() {
        return 'Rp ' . number_format($this->change_amount, 0, ',', '.');
    }

    /**
     * BADGE STATUS UNTUK DASHBOARD ADMIN
     */
    public function getStatusLabelAttribute()
    {
        return match($this->payment_status) {
            'success' => '<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-green-200">LUNAS</span>',
            'failed'  => '<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-200">GAGAL</span>',
            default   => '<span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-amber-200">PENDING</span>',
        };
    }

    /**
     * HITUNG TOTAL ITEM DALAM SATU NOTA
     */
    public function getTotalItemsAttribute()
    {
        return $this->details->sum('quantity');
    }
}