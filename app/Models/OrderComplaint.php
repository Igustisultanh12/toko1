<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'reason',
        'description',
        'expected_solution',
        'photos',
        'video',
        'status',
        'admin_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'photos'      => 'array',
        'resolved_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function resolvedByUser()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending'  => 'Menunggu Ditinjau',
            'reviewed' => 'Sedang Ditinjau Admin',
            'approved' => 'Komplain Disetujui',
            'rejected' => 'Komplain Ditolak',
            'resolved' => 'Selesai / Selesai Diganti',
            default    => 'Pending',
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending'  => '<span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-[10px] font-black uppercase tracking-wider border border-amber-200 animate-pulse">Menunggu Ditinjau</span>',
            'reviewed' => '<span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-[10px] font-black uppercase tracking-wider border border-blue-200">Sedang Ditinjau</span>',
            'approved' => '<span class="px-3 py-1 bg-emerald-100 text-[#00661A] rounded-full text-[10px] font-black uppercase tracking-wider border border-emerald-200">Disetujui</span>',
            'rejected' => '<span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-full text-[10px] font-black uppercase tracking-wider border border-rose-200">Ditolak</span>',
            'resolved' => '<span class="px-3 py-1 bg-gray-900 text-white rounded-full text-[10px] font-black uppercase tracking-wider">Selesai</span>',
            default    => '<span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-[10px] font-black uppercase tracking-wider">Pending</span>',
        };
    }
}
