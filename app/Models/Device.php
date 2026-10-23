<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'project_id',
        'hostname',
        'type',
        'ip_address',
        'mac_address',
        'status',
        'status_state',
        'rack_location',
        'snmp_community',
        'snmp_port',
        'uptime',
        'last_ping_ms',
        'packet_loss',
        'cpu_load',
        'ram_load',
        'rx_rate',
        'tx_rate',
    ];

    /**
     * Relationship with Customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relationship with Project
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relationship with Monitoring Alerts
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(MonitoringAlert::class);
    }

    /**
     * Relationship with Tickets
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
