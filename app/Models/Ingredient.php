<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductDetail;

class Ingredient extends Model
{
    use HasFactory;

    public function product_details(){
        return $this->hasMany(ProductDetail::class);
    }
}
