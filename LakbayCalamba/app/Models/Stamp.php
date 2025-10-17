<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stamp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'establishment_id',
        'visit_date',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // When a stamp is deleted, also delete the corresponding visitor record
        static::deleting(function ($stamp) {
            \App\Models\Visitor::where('user_id', $stamp->user_id)
                               ->where('establishment_id', $stamp->establishment_id)
                               ->whereDate('visited_at', $stamp->visit_date->format('Y-m-d'))
                               ->delete();
        });
    }
}
