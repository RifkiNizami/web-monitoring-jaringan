<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'message',
        'priority',
        'status',
        'scheduled_at',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Relasi ke pembuat pengumuman (User)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Helper Badge Styling berdasarkan Kategori
     */
    public function getCategoryBadgeAttribute(): array
    {
        return match (strtolower($this->category)) {
            'maintenance' => [
                'label' => 'Maintenance',
                'bg' => 'bg-amber-50 text-amber-700 border-amber-200',
                'dot' => 'bg-amber-500',
                'icon' => 'wrench',
            ],
            'gangguan' => [
                'label' => 'Gangguan Jaringan',
                'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
                'dot' => 'bg-rose-500',
                'icon' => 'alert-triangle',
            ],
            'pembaruan' => [
                'label' => 'Pembaruan Sistem',
                'bg' => 'bg-blue-50 text-blue-700 border-blue-200',
                'dot' => 'bg-blue-500',
                'icon' => 'refresh-cw',
            ],
            default => [
                'label' => 'Pengumuman Info',
                'bg' => 'bg-slate-100 text-slate-700 border-slate-200',
                'dot' => 'bg-slate-500',
                'icon' => 'info',
            ],
        };
    }

    /**
     * Helper Priority Styling
     */
    public function getPriorityBadgeAttribute(): array
    {
        return match (strtolower($this->priority)) {
            'urgent' => ['label' => 'MENDESAK', 'class' => 'bg-rose-100 text-rose-800 border-rose-300'],
            'tinggi' => ['label' => 'TINGGI', 'class' => 'bg-orange-100 text-orange-800 border-orange-300'],
            'rendah' => ['label' => 'RENDAH', 'class' => 'bg-slate-100 text-slate-600 border-slate-200'],
            default => ['label' => 'NORMAL', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-300'],
        };
    }
}
