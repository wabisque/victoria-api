<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aspirant extends Model
{
    use HasFactory;

    public function aspirantUpdateRequests()
    {
        return $this->hasMany(AspirantUpdateRequest::class);
    }

    public function constituency()
    {
        return $this->belongsTo(Constituency::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
