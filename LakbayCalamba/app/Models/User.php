<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'lakbay_id',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relations (optional)
    public function stamps()
    {
        return $this->hasMany(Stamp::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function hasStampFor($establishmentId)
    {
        return $this->stamps()->where('establishment_id', $establishmentId)->exists();
    }

    public function canReview($establishmentId)
    {
        return $this->hasStampFor($establishmentId) &&
               !$this->reviews()->where('establishment_id', $establishmentId)->exists();
    }
}
