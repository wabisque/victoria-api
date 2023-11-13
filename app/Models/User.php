<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $casts = [
        'password' => 'hashed',
    ];

    public function aspirant()
    {
        return $this->hasOne(Aspirant::class);
    }

    public function boss()
    {
        return $this->belongsTo(User::class, 'boss_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
