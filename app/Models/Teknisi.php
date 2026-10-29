<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teknisi extends Model
{
    use HasFactory;

    protected $table = 'teknisi';

    protected $fillable = [
        'nama',
        'alamat',
        'nomor_hp',
    ];

    /**
     * Format phone number to clean international number for WhatsApp (e.g. 628123456789)
     */
    public function getCleanWhatsappNumberAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->nomor_hp ?? '');

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Generate direct WhatsApp Web / App link with custom prefilled message
     */
    public function getWhatsappUrlAttribute(): string
    {
        $phone = $this->clean_whatsapp_number;
        if (empty($phone)) {
            return '#';
        }

        $message = urlencode("Halo {$this->nama}, kami dari tim NOC NetFly ingin menginformasikan koordinasi tugas teknis.");
        return "https://wa.me/{$phone}?text={$message}";
    }
}
