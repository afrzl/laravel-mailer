<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mailer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'mail_body',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_from_address',
        'mail_from_name',
        'mail_encryption',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'mail_port' => 'integer',
    ];

    protected $hidden = [
        'mail_password',
    ];

    /**
     * Get the user that owns the mailer
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all mailer items for this mailer
     */
    public function items(): HasMany
    {
        return $this->hasMany(MailerItem::class);
    }

    /**
     * Get pending mailer items
     */
    public function pendingItems(): HasMany
    {
        return $this->hasMany(MailerItem::class)->where('status', 'pending');
    }

    /**
     * Get sent mailer items
     */
    public function sentItems(): HasMany
    {
        return $this->hasMany(MailerItem::class)->where('status', 'sent');
    }

    /**
     * Get failed mailer items
     */
    public function failedItems(): HasMany
    {
        return $this->hasMany(MailerItem::class)->where('status', 'failed');
    }

    /**
     * Get total recipients count
     */
    public function getTotalRecipientsAttribute(): int
    {
        return $this->items()->count();
    }

    /**
     * Get sent count
     */
    public function getSentCountAttribute(): int
    {
        return $this->items()->where('status', 'sent')->count();
    }

    /**
     * Get failed count
     */
    public function getFailedCountAttribute(): int
    {
        return $this->items()->where('status', 'failed')->count();
    }

    /**
     * Get pending count
     */
    public function getPendingCountAttribute(): int
    {
        return $this->items()->where('status', 'pending')->count();
    }
}
