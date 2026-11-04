<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * Tabel database terkait.
     */
    protected $table = 'activity_logs';

    /**
     * Tabel ini hanya memiliki created_at, tidak ada updated_at.
     */
    public const UPDATED_AT = null;

    /**
     * Atribut yang dapat diisi massal.
     */
    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'module',
        'action',
        'subject_label',
        'subject_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    /**
     * Konversi tipe atribut bawaan.
     */
    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relasi ke model User (nullable jika user telah dihapus atau aksi anonim).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
