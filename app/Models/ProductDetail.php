<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ingredient;

class ProductDetail extends Model
{
    use HasFactory;
    protected $table = "product_details";

    public function ingredients(){
        return $this->belongsTo(Ingredient::class,"ingredient_id");
    }

}
