<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function doctor(){
        return $this->belongsTo(Doctor::class);
    }
    public function appointment(){
        return $this->belongsTo(Appointment::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
