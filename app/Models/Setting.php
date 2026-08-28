<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment).
     * 'key' digunakan sebagai identitas (misal: shop_name)
     * 'value' digunakan sebagai nilai simpanannya.
     */
    protected $fillable = [
        'key', 
        'value'
    ];
}