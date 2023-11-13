<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AspirantUpdateRequest extends Model
{
    use HasFactory;

    public function aspirant()
    {
        return $this->belongsTo(Aspirant::class);
    }

    public function constituency()
    {
        return $this->belonsTo(Constituency::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
