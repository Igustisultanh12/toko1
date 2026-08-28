<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'payment_method',
        'amount_paid',      // Pastikan ini ada
        'change_amount',    // Pastikan ini ada
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

           /**
        * Mendefinisikan relasi one-to-many ke SaleDetail.
        */
    public function saleDetails()
    {
    return $this->hasMany(SaleDetail::class);
    }
}
