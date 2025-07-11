<?php

namespace App\Models;
use App\Models\Doctor;
use App\Models\Client;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'age',
        'gender',
        'provider_id',
        'provider_type',
        'email_verified_at'
  
        
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    protected static function booted()
    {
        static::creating(function ($user) {
            $user->slug = Str::slug($user->name) . '-' . uniqid();
        });
    }
    public function doctor() {
        return $this->hasOne(Doctor::class);
    }
    public function client() {
        return $this->hasOne(Client::class);
    }
    public function bookings() {
        return $this->hasMany(Booking::class);
    }

}
