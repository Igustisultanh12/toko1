<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // PERBAIKAN: Tambahkan properti $fillable di bawah ini
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'price_at_transaction',
        'discount_at_transaction',
    ];

    /**
     * Get the product for the sale detail.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the sale for the sale detail.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
