<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AspirantCreationRequest extends Model
{
    use HasFactory;

    protected $casts = [
        'status_applied_at' => 'datetime'
    ];

    public function constituency()
    {
        return $this->belongsTo(Constituency::class);
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
