<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Constituency extends Model
{
    use HasFactory;

    public function aspirants()
    {
        return $this->hasMany(Aspirant::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
