<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor_Specialty extends Model
{
    use HasFactory;
    protected $fillable = ['doctor_id','specialty_id'];
    protected $table = 'doctor_specialty';
    public function doctor(){
        return $this->belongsTo(Doctor::class);
    }
    public function specialty(){
        return $this->belongsTo(Specialty::class);
    }

}
