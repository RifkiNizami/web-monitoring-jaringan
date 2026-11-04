<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class PruneActivityLogs extends Command
{
    /**
     * Nama dan tanda tangan command konsol.
     *
     * @var string
     */
    protected $signature = 'activity-logs:prune {--days=90 : Jumlah hari retensi log yang dipertahankan}';

    /**
     * Deskripsi command konsol.
     *
     * @var string
     */
    protected $description = 'Hapus log aktivitas sistem yang telah melewati batas usia hari yang ditentukan';

    /**
     * Eksekusi command konsol.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');

        if ($days <= 0) {
            $this->error('Parameter --days harus berupa angka bulat positif lebih dari 0.');
            return self::FAILURE;
        }

        $cutoff = now()->subDays($days);
        $deleted = ActivityLog::where('created_at', '<', $cutoff)->delete();

        $this->info("Berhasil membersihkan {$deleted} catatan log aktivitas yang lebih lama dari {$days} hari (sebelum {$cutoff->format('d M Y H:i')}).");

        return self::SUCCESS;
    }
}
