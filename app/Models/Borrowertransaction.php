<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Borrower;

class BorrowerTransaction extends Model
{
    use HasFactory;

    

    public function borrower(){
        return $this->belongsTo(Borrower::class);
    }
}
