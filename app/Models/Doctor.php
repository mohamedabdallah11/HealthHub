<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','bio','experinece_year','fees'];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function clients () {
        return $this->belongsToMany(Client::class,'client_doctor');
    }
}
