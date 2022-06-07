<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BorrowerTransaction;

class Borrower extends Model
{
    use HasFactory;

    public function borrowertransaction()
    {
        return $this->hasMany(BorrowerTransaction::class);
    }
}
