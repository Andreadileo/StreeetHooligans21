<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id','sku','size','color','stock','price_cents'
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function priceInEuro(): float {
        $base = $this->price_cents ?? $this->product->price_cents;
        return $base / 100;
    }
}
