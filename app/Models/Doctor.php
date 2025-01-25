<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Appointment;
use App\Models\Booking;
class Doctor extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','bio','experience_year','fees'];

  
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function clients () {
        return $this->belongsToMany(Client::class,'client_doctor');
    }
    public function specialties() {
        return $this->belongsToMany(Specialty::class,'doctor_specialty');
    }
    public function appointments() {
        return $this->hasMany(Appointment::class);
    }
    public function bookings() {
        return $this->hasMany(Booking::class);
    }

}
