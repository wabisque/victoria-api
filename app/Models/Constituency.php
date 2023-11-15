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

    public function aspirantCreationRequests()
    {
        return $this->hasMany(AspirantCreationRequest::class);
    }

    public function aspirantUpdateRequests()
    {
        return $this->hasMany(AspirantUpdateRequest::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
