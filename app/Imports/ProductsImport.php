<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows; // <-- PERBAIKAN: Tambahkan ini

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows // <-- PERBAIKAN: Tambahkan ini
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            'name'     => $row['nama'],
            // Memastikan barcode selalu disimpan sebagai string
            'barcode'  => isset($row['barcode']) ? (string) $row['barcode'] : null,
            'price'    => $row['harga'],
            'stock'    => $row['stok'],
        ]);
    }

    /**
     * Menambahkan validasi untuk setiap baris.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'barcode' => 'nullable|max:255|unique:products,barcode',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ];
    }
}
