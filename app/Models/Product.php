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
    protected $fillable = [
        'name',
        'barcode',
        'description',
        'image',
        'gallery',
        'stock',
        'price',
        'discount_percent',
    ];

    protected $casts = [
        'gallery'          => 'array',
        'price'            => 'double',
        'stock'            => 'integer',
        'discount_percent' => 'double',
    ];

    protected $appends = ['image_url', 'gallery_urls'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return route('media.file', ['path' => $this->image]);
        }
        return null;
    }

    public function getGalleryUrlsAttribute()
    {
        $urls = [];
        if (!empty($this->image)) {
            $urls[] = route('media.file', ['path' => $this->image]);
        }

        if (!empty($this->gallery) && is_array($this->gallery)) {
            foreach ($this->gallery as $imgPath) {
                if (!empty($imgPath)) {
                    $urls[] = route('media.file', ['path' => $imgPath]);
                }
            }
        }

        return array_values(array_unique($urls));
    }
}
