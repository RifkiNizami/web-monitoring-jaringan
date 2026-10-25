<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkSlaMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'uptime_percentage',
        'downtime_minutes',
        'incidents_count',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'uptime_percentage' => 'float',
        'downtime_minutes' => 'integer',
        'incidents_count' => 'integer',
    ];
}
