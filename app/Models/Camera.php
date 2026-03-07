<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    /** @use HasFactory<\Database\Factories\CameraFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'rtsp_url',
        'snapshot_url',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('name');
    }

    public function getSnapshotUrlAttribute(?string $value): ?string
    {
        return $value ?: $this->generateSnapshotUrl();
    }

    /**
     * Generate a snapshot URL from RTSP URL for Reolink cameras
     */
    protected function generateSnapshotUrl(): ?string
    {
        if (! $this->rtsp_url) {
            return null;
        }

        // Extract host and credentials from RTSP URL
        // Format: rtsp://user:pass@host:port/path
        if (preg_match('/rtsp:\/\/([^@]+@)?([^:\/]+)(:(\d+))?/', $this->rtsp_url, $matches)) {
            $auth = isset($matches[1]) ? rtrim($matches[1], '@') : '';
            $host = $matches[2];
            $port = isset($matches[4]) ? $matches[4] : '554';

            // Default Reolink snapshot endpoint
            $snapshotUrl = "http://{$host}/cgi-bin/api.cgi?cmd=Snap&channel=0&rs=wuuPhkmUCeI9WG7C&user=admin";

            if ($auth && str_contains($auth, ':')) {
                [$user, $pass] = explode(':', $auth, 2);
                $snapshotUrl .= "&password={$pass}";
            }

            return $snapshotUrl;
        }

        return null;
    }

    /**
     * Test if the camera is reachable
     */
    public function isReachable(): bool
    {
        $snapshotUrl = $this->snapshot_url;
        if (! $snapshotUrl) {
            return false;
        }

        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'method' => 'HEAD',
            ],
        ]);

        $headers = @get_headers($snapshotUrl, false, $context);

        return $headers && str_contains($headers[0], '200');
    }
}
