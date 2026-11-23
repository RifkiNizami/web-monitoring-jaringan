<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $hostname
 * @property string|null $unit_type
 * @property string $ip_address
 * @property string|null $customer
 * @property string|null $project
 * @property string|null $model
 * @property string|null $rack_location
 * @property string $status
 * @property string|null $status_label
 * @property string|null $uptime
 * @property string|null $rx_mbps
 * @property string|null $tx_mbps
 * @property float $cpu_load
 * @property float $ram_load
 * @property string|null $temperature
 * @property string|null $voltage
 * @property float $packet_loss
 * @property int|null $last_ping_ms
 * @property string|null $connection_type
 * @property int $api_port
 * @property string|null $api_username
 * @property string|null $api_password
 * @property array|null $interfaces
 * @property int|null $customer_id
 * @property int|null $project_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class MonitoringDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'customer_id',
        'project_id',
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
        'packet_loss',
        'last_ping_ms',
        'connection_type',
        'api_port',
        'api_username',
        'api_password',
        'interfaces',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'project_id' => 'integer',
        'cpu_load' => 'float',
        'ram_load' => 'float',
        'packet_loss' => 'float',
        'last_ping_ms' => 'integer',
        'api_port' => 'integer',
        'interfaces' => 'array',
    ];

    /**
     * Relasi dengan Customer (Pelanggan Pemilik Node / Sirkuit)
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Alias relasi customer
     */
    public function customerRelation(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Relasi dengan Proyek Jaringan
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Alias relasi project
     */
    public function projectRelation(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
