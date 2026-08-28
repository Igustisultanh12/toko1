<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'address',
    ];

    /**
     * Mendefinisikan relasi "hasMany" ke model Sale.
     * Setiap pelanggan (customer) bisa memiliki banyak penjualan (sales).
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
