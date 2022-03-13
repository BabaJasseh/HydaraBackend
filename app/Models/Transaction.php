<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Depositor;

class Transaction extends Model
{
    use HasFactory;

    

    public function depositor(){
        return $this->belongsTo(Depositor::class);
    }
}
