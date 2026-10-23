<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'customer_id',
        'project_id',
        'device_id',
        'client_name',
        'project_name',
        'node_code',
        'priority',
        'priority_level',
        'subject',
        'description',
        'assigned_technician',
        'is_tech_warning',
        'status',
        'status_type',
        'status_note',
        'reported_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'is_tech_warning' => 'boolean',
            'reported_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

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
     * Relationship with Device
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
