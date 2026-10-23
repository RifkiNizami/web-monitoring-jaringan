<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'pic_name',
        'email',
        'phone',
        'address',
        'category',
        'package',
        'note',
        'status',
    ];

    /**
     * Relationship with Projects
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Relationship with Devices
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    /**
     * Relationship with Tickets
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Relationship with Monitoring Alerts
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(MonitoringAlert::class);
    }

    /**
     * Relationship with Users / Account Representatives
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
