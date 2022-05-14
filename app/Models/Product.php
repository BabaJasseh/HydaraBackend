<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sale;
use App\Models\Category;
use App\Models\Stock;
use App\Models\Sellerinventory;

class Product extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class); // before it was belongs to many
    }

    public function sellerinventories()
    {
        return $this->hasMany(Sellerinventory::class); // before it was belongs to many
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
