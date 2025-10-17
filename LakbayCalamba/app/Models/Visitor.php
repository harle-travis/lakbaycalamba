<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'establishment_id',
        'user_id',
        'visited_at',
        'is_guest',
        'guest_name',
        'guest_contact'
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'is_guest' => 'boolean',
    ];

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
