<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Passkey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'credential_id',
        'credential_public_key',
        'aaguid',
        'signature_counter',
        'device_type',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at'      => 'datetime',
        'signature_counter' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function iconName(): string
    {
        $name = strtolower($this->name . ' ' . ($this->device_type ?? ''));

        if (str_contains($name, 'mac') || str_contains($name, 'apple') || str_contains($name, 'touch id') || str_contains($name, 'face id') || str_contains($name, 'iphone') || str_contains($name, 'ipad')) {
            return 'apple';
        }

        if (str_contains($name, 'windows') || str_contains($name, 'hello')) {
            return 'windows';
        }

        if (str_contains($name, 'android')) {
            return 'android';
        }

        if (str_contains($name, 'yubikey') || str_contains($name, 'fido') || str_contains($name, 'usb') || str_contains($name, 'clé')) {
            return 'key';
        }

        return 'fingerprint';
    }
}
