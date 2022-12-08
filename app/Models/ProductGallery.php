<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductGallery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'products_id', 'url', 'is_featured'
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'products_id');
    }
}
