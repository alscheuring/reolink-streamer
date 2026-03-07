<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Camera>
 */
class CameraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $locations = ['Front Door', 'Back Yard', 'Living Room', 'Garage', 'Kitchen', 'Driveway', 'Pool Area', 'Side Gate'];
        $locationIndex = array_rand($locations);
        $ip = '192.168.1.'.rand(100, 200);
        $username = 'admin';
        $password = 'password123';

        return [
            'name' => $locations[$locationIndex].' Camera',
            'rtsp_url' => "rtsp://{$username}:{$password}@{$ip}:554/h264Preview_01_main",
            'snapshot_url' => "http://{$ip}/cgi-bin/api.cgi?cmd=Snap&channel=0&rs=wuuPhkmUCeI9WG7C&user={$username}&password={$password}",
            'is_active' => rand(1, 10) > 1, // 90% chance of being active
            'sort_order' => rand(0, 100),
        ];
    }
}
