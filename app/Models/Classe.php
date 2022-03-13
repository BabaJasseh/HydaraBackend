<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subject;
use App\Models\Student;
use PDO;

class Classe extends Model
{
    use HasFactory;

    // public function results()
    // {
    //    return $this->hasMany(Result::class);   /////////// May be needed
    // }

    public function subjects(){
        return $this->hasMany(Subject::class);
    }

    public function students(){
        return $this->hasMany(Student::class);
    }

    
}

