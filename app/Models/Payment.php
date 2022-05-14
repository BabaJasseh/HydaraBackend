<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sale;

class Payment extends Model
{
    use HasFactory;

    public function sale(){
        return $this->belongsToOne(Sale::class);  
    }
}
