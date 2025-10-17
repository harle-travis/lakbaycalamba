<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'lakbay_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class, 'name', 'establishment_name');
    }

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

    /**
     * Check if user has a stamp for a specific establishment
     */
    public function hasStampFor($establishmentId)
    {
        return $this->stamps()->where('establishment_id', $establishmentId)->exists();
    }

    /**
     * Check if user can review a specific establishment
     * (has stamp but no existing review)
     */
    public function canReview($establishmentId)
    {
        return $this->hasStampFor($establishmentId) && 
               !$this->reviews()->where('establishment_id', $establishmentId)->exists();
    }
}
