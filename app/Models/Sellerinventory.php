<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Sellerinventory extends Model
{
    use HasFactory;

    public function product(){
        return $this->belongsToOne(Product::class);  // it was hasMany 
    }
}
