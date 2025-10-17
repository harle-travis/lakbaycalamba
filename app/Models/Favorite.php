<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'establishment_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the favorite
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the establishment that is favorited
     */
    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }
}
