<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'hostname',
        'unit_type',
        'ip_address',
        'customer',
        'project',
        'model',
        'rack_location',
        'status',
        'status_label',
        'uptime',
        'rx_mbps',
        'tx_mbps',
        'cpu_load',
        'ram_load',
        'temperature',
        'voltage',
        'packet_loss',
        'last_ping_ms',
        'connection_type',
        'api_port',
        'api_username',
        'api_password',
        'interfaces',
    ];

    protected $casts = [
        'cpu_load' => 'float',
        'ram_load' => 'float',
        'packet_loss' => 'float',
        'last_ping_ms' => 'integer',
        'api_port' => 'integer',
        'interfaces' => 'array',
    ];
}
