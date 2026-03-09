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
        'has_ptz',
        'ptz_username',
        'ptz_password',
        'ptz_api_url',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'has_ptz' => 'boolean',
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

    /**
     * Generate PTZ API URL for Reolink cameras
     */
    public function getPtzApiUrl(): ?string
    {
        if ($this->ptz_api_url) {
            return $this->ptz_api_url;
        }

        if (! $this->rtsp_url) {
            return null;
        }

        // Extract host from RTSP URL
        if (preg_match('/rtsp:\/\/([^@]+@)?([^:\/]+)(:(\d+))?/', $this->rtsp_url, $matches)) {
            $host = $matches[2];

            return "http://{$host}/cgi-bin/api.cgi";
        }

        return null;
    }

    /**
     * Get PTZ credentials
     */
    public function getPtzCredentials(): array
    {
        // If PTZ credentials are set, use them
        if ($this->ptz_username) {
            return [
                'user' => $this->ptz_username,
                'password' => $this->ptz_password ?? '',
            ];
        }

        // Otherwise, try to extract from RTSP URL
        if ($this->rtsp_url && preg_match('/rtsp:\/\/([^:]+):([^@]+)@/', $this->rtsp_url, $matches)) {
            return [
                'user' => $matches[1],
                'password' => $matches[2],
            ];
        }

        // Default fallback
        return [
            'user' => 'admin',
            'password' => '',
        ];
    }

    /**
     * Send PTZ command to Reolink camera (v3.1.0+ firmware)
     */
    public function sendPtzCommand(string $command, array $params = []): bool
    {
        if (! $this->has_ptz) {
            return false;
        }

        $apiUrl = $this->getPtzApiUrl();
        if (! $apiUrl) {
            return false;
        }

        $credentials = $this->getPtzCredentials();

        // New v3.1.0+ firmware format: uses 'action' and 'param' instead of 'arg'
        $commandData = [
            'cmd' => $command,
        ];

        // For PTZ movement commands, use the new format
        if ($command === 'PtzCtrl') {
            $commandData['action'] = 0;
            $commandData['param'] = $params;
        } else {
            // For other commands, try both new and old formats
            if (! empty($params)) {
                $commandData['param'] = $params;
            }
        }

        $jsonPayload = json_encode([$commandData]);
        $authUrl = $apiUrl.'?user='.urlencode($credentials['user']).'&password='.urlencode($credentials['password']);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                    'User-Agent: Reolink Client',
                ],
                'content' => $jsonPayload,
                'timeout' => 10,
            ],
        ]);

        $response = @file_get_contents($authUrl, false, $context);

        if ($response !== false) {
            $decoded = json_decode($response, true);
            if (isset($decoded[0]['code'])) {
                return $decoded[0]['code'] === 0;
            }
        }

        return false;
    }

    /**
     * Check if this camera model supports API PTZ control
     */
    public function hasFunctionalPtz(): bool
    {
        if (! $this->has_ptz) {
            return false;
        }

        // Test with PTZ Guard command (known to work in v3.1.0+)
        $result = $this->sendPtzCommand('GetPtzGuard', []);

        return $result;
    }

    /**
     * Pan camera (left/right)
     *
     * @param  string  $direction  'left' or 'right'
     * @param  int  $speed  1-50
     */
    public function pan(string $direction, int $speed = 25): bool
    {
        $operation = $direction === 'left' ? 'Left' : 'Right';

        return $this->sendPtzCommand('PtzCtrl', [
            'channel' => 0,
            'op' => $operation,
            'speed' => $speed,
        ]);
    }

    /**
     * Tilt camera (up/down)
     *
     * @param  string  $direction  'up' or 'down'
     * @param  int  $speed  1-50
     */
    public function tilt(string $direction, int $speed = 25): bool
    {
        $operation = $direction === 'up' ? 'Up' : 'Down';

        return $this->sendPtzCommand('PtzCtrl', [
            'channel' => 0,
            'op' => $operation,
            'speed' => $speed,
        ]);
    }

    /**
     * Zoom camera
     *
     * @param  string  $direction  'in' or 'out'
     * @param  int  $speed  1-50
     */
    public function zoom(string $direction, int $speed = 25): bool
    {
        $operation = $direction === 'in' ? 'ZoomInc' : 'ZoomDec';

        return $this->sendPtzCommand('PtzCtrl', [
            'channel' => 0,
            'op' => $operation,
            'speed' => $speed,
        ]);
    }

    /**
     * Stop PTZ movement
     */
    public function stopPtz(): bool
    {
        return $this->sendPtzCommand('PtzCtrl', [
            'channel' => 0,
            'op' => 'Stop',
        ]);
    }
}
