<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LandingClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'location',
        'logo',
        'website_url',
        'order',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Relasi ke pembuat konten (User)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accessor untuk URL logo lengkap
     */
    public function getLogoUrlAttribute(): string
    {
        if (empty($this->logo)) {
            return asset('assets/images/netfly-logo.png');
        }

        if (Str::startsWith($this->logo, ['http://', 'https://'])) {
            return $this->logo;
        }

        // Jika path sudah diawali dengan 'assets/' atau 'uploads/' atau 'storage/'
        if (Str::startsWith($this->logo, ['assets/', 'uploads/', 'storage/'])) {
            return asset($this->logo);
        }

        return asset('uploads/clients/' . ltrim($this->logo, '/'));
    }
}
