<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $token_hash
 * @property Carbon|null $last_used_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 */
class ApiToken extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'token_hash',
        'last_used_at',
        'expires_at',
    ];

    protected $hidden = [
        'token_hash',
    ];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Terbitkan Bearer Token baru untuk pengguna dan kembalikan plain-text token hanya sekali.
     *
     * @return array{token: self, plain_text_token: string}
     */
    public static function issueFor(User $user, string $name = 'NetFly-Mobile', int $ttlDays = 30): array
    {
        $plain = bin2hex(random_bytes(32));
        $record = self::create([
            'user_id' => $user->id,
            'name' => mb_substr(trim($name) ?: 'NetFly-Mobile', 0, 100),
            'token_hash' => hash('sha256', $plain),
            'last_used_at' => now(),
            'expires_at' => $ttlDays > 0 ? now()->addDays($ttlDays) : null,
        ]);

        return [
            'token' => $record,
            'plain_text_token' => $record->id.'|'.$plain,
        ];
    }

    /**
     * Cari dan verifikasi Bearer Token menggunakan hash SHA-256 constant-time.
     */
    public static function findValidToken(string $rawBearer): ?self
    {
        $rawBearer = trim($rawBearer);
        if ($rawBearer === '') {
            return null;
        }

        $record = null;
        $plain = $rawBearer;

        if (str_contains($rawBearer, '|')) {
            [$idPart, $secretPart] = explode('|', $rawBearer, 2);
            if (! ctype_digit($idPart) || $secretPart === '') {
                return null;
            }
            $candidate = self::with('user.customer')->find((int) $idPart);
            if (! $candidate || ! hash_equals($candidate->token_hash, hash('sha256', $secretPart))) {
                return null;
            }
            $record = $candidate;
        } else {
            $record = self::with('user.customer')
                ->where('token_hash', hash('sha256', $plain))
                ->first();
        }

        if (! $record) {
            return null;
        }

        if ($record->expires_at && $record->expires_at->isPast()) {
            $record->delete();

            return null;
        }

        return $record;
    }
}
