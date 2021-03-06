<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Category;
use App\Models\Payment;

class Sale extends Model
{
    use HasFactory;

    public function products(){
        return $this->belongsToMany(Product::class);  // it was hasMany 
    }

    public function category(){
        return $this->belongsTo(Category::class); 
    }
    public function payments(){
        return $this->hasMany(Payment::class); 
    }

    
    


}
