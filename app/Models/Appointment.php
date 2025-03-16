<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'doctor_id',
        'date',
        'start_time',
        'end_time',
        'session_duration',
        'is_available',
        'max_patients'
    ];
    
    public function doctor(){
        return $this->belongsTo(Doctor::class);
    }
    public function bookings(){
        return $this->hasMany(Booking::class);
    }
}
