<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // PERBAIKAN: Tambahkan properti $fillable di bawah ini
    protected $fillable = [
        'name',
        'barcode',
        'description',
        'stock',
        'price',
        'discount_percent',
    ];
}
