<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_notes',
        'courier',
        'tracking_number',
        'total_amount',
        'payment_method',
        'payment_status',
        'status',
        'qris_url',
        'doku_invoice_id',
        'confirmed_by',
        'paid_at',
        'confirmed_at',
        'shipped_at',
        'completed_at',
    ];

    protected $casts = [
        'total_amount' => 'double',
        'paid_at'      => 'datetime',
        'confirmed_at' => 'datetime',
        'shipped_at'   => 'datetime',
        'completed_at' => 'datetime',
        'created_at'   => 'datetime:Y-m-d H:i:s',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function complaints()
    {
        return $this->hasMany(OrderComplaint::class, 'order_id');
    }

    public function latestComplaint()
    {
        return $this->hasOne(OrderComplaint::class, 'order_id')->latestOfMany();
    }

    public function confirmedByUser()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending_payment' => '<span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-black uppercase border border-amber-200">Menunggu Pembayaran</span>',
            'paid'            => '<span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase border border-blue-200 animate-pulse">Sudah Bayar / Perlu Konfirmasi</span>',
            'processing'      => '<span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-black uppercase border border-indigo-200">Sedang Disiapkan</span>',
            'shipped'         => '<span class="px-3 py-1 bg-emerald-50 text-[#00880F] rounded-full text-[10px] font-black uppercase border border-emerald-200">Sedang Dikirim</span>',
            'completed'       => '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-[10px] font-black uppercase border border-green-300">Selesai</span>',
            'cancelled'       => '<span class="px-3 py-1 bg-rose-50 text-rose-600 rounded-full text-[10px] font-black uppercase border border-rose-200">Dibatalkan</span>',
            default           => '<span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-[10px] font-black uppercase">Draft</span>',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending_payment' => 'Menunggu Pembayaran QRIS',
            'paid'            => 'Pembayaran Lunas (Menunggu Konfirmasi Toko)',
            'processing'      => 'Pesanan Dikonfirmasi & Sedang Disiapkan',
            'shipped'         => 'Pesanan Sedang Dikirim',
            'completed'       => 'Pesanan Selesai / Diterima',
            'cancelled'       => 'Pesanan Dibatalkan',
            default           => 'Draft',
        };
    }
}
