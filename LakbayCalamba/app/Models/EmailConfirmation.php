<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmailConfirmation extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'token',
        'new_email',
        'new_password_hash',
        'confirmed',
        'expires_at',
    ];

    protected $casts = [
        'confirmed' => 'boolean',
        'expires_at' => 'datetime',
    ];

    // Types
    const TYPE_EMAIL_CHANGE = 'email_change';
    const TYPE_PASSWORD_CHANGE = 'password_change';

    /**
     * Get the user that owns the email confirmation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a new confirmation token.
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Create a new email confirmation record.
     */
    public static function createConfirmation(int $userId, string $type, ?string $newEmail = null, ?string $newPasswordHash = null): self
    {
        // Delete any existing unconfirmed confirmations for this user and type
        self::where('user_id', $userId)
            ->where('type', $type)
            ->where('confirmed', false)
            ->delete();

        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'token' => self::generateToken(),
            'new_email' => $newEmail,
            'new_password_hash' => $newPasswordHash,
            'expires_at' => Carbon::now()->addHours(24), // 24 hours expiry
        ]);
    }

    /**
     * Check if the confirmation is valid and not expired.
     */
    public function isValid(): bool
    {
        return !$this->confirmed && $this->expires_at->isFuture();
    }

    /**
     * Mark the confirmation as confirmed.
     */
    public function confirm(): void
    {
        $this->update(['confirmed' => true]);
    }

    /**
     * Clean up expired confirmations.
     */
    public static function cleanupExpired(): int
    {
        return self::where('expires_at', '<', Carbon::now())->delete();
    }
}
