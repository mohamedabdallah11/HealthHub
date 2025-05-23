<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{   
    protected $fillable = ['name'];
    use HasFactory;
    public function doctors(){
        return $this->belongsToMany(Specialty::class,'doctor_specialty');
    }
}
