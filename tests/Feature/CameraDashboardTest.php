<?php

use App\Livewire\CameraGrid;
use App\Models\Camera;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('can access dashboard when authenticated', function () {
    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Dashboard');
});

it('redirects to login when not authenticated for dashboard', function () {
    auth()->logout();

    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

it('displays camera grid on dashboard', function () {
    Camera::factory()->count(3)->create(['is_active' => true]);

    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSeeLivewire(CameraGrid::class);
});

it('shows active cameras in grid component', function () {
    $activeCamera = Camera::factory()->create([
        'name' => 'Active Camera',
        'is_active' => true,
    ]);
    $inactiveCamera = Camera::factory()->create([
        'name' => 'Inactive Camera',
        'is_active' => false,
    ]);

    Livewire::test(CameraGrid::class)
        ->assertSee('Active Camera')
        ->assertDontSee('Inactive Camera');
});

it('limits cameras to 6 in grid display', function () {
    Camera::factory()->count(10)->create(['is_active' => true]);

    $component = Livewire::test(CameraGrid::class);
    $cameras = $component->viewData('cameras');

    expect($cameras)->toHaveCount(6);
});

it('orders cameras by sort_order and name', function () {
    $camera1 = Camera::factory()->create(['name' => 'Z Camera', 'sort_order' => 1, 'is_active' => true]);
    $camera2 = Camera::factory()->create(['name' => 'A Camera', 'sort_order' => 2, 'is_active' => true]);
    $camera3 = Camera::factory()->create(['name' => 'B Camera', 'sort_order' => 1, 'is_active' => true]);

    $component = Livewire::test(CameraGrid::class);
    $cameras = $component->viewData('cameras');

    expect($cameras->first()->name)->toBe('B Camera') // sort_order 1, alphabetically first
        ->and($cameras->get(1)->name)->toBe('Z Camera') // sort_order 1, alphabetically second
        ->and($cameras->get(2)->name)->toBe('A Camera'); // sort_order 2
});

it('redirects to camera stream when clicking camera', function () {
    $camera = Camera::factory()->create(['is_active' => true]);

    Livewire::test(CameraGrid::class)
        ->call('viewCamera', $camera)
        ->assertRedirect(route('camera.stream', ['camera' => $camera->id]));
});

it('can access camera stream page', function () {
    $camera = Camera::factory()->create([
        'name' => 'Test Camera',
        'rtsp_url' => 'rtsp://admin:password@192.168.1.100:554/stream',
    ]);

    $this->get(route('camera.stream', ['camera' => $camera->id]))
        ->assertSuccessful()
        ->assertSee('Test Camera')
        ->assertSee('Live Camera Stream')
        ->assertSee('Back to Dashboard');
});

it('returns 404 for non-existent camera stream', function () {
    $this->get(route('camera.stream', ['camera' => 999]))
        ->assertNotFound();
});

it('shows proper camera information on stream page', function () {
    $camera = Camera::factory()->create([
        'name' => 'Kitchen Camera',
        'rtsp_url' => 'rtsp://admin:password@192.168.1.100:554/h264Preview_01_main',
        'snapshot_url' => 'http://192.168.1.100/snapshot.jpg',
    ]);

    $this->get(route('camera.stream', ['camera' => $camera->id]))
        ->assertSee('Kitchen Camera')
        ->assertSee('Live')
        ->assertSee('Camera Status')
        ->assertSee('Stream URL');
});

it('displays no cameras message when no active cameras exist', function () {
    // Create only inactive cameras
    Camera::factory()->count(2)->create(['is_active' => false]);

    Livewire::test(CameraGrid::class)
        ->assertSee('No Camera')
        ->assertSee('Manage cameras');
});

it('shows additional cameras in bottom section', function () {
    // Create 6 cameras (3 top + 3 additional)
    Camera::factory()->count(6)->create(['is_active' => true]);

    $component = Livewire::test(CameraGrid::class);
    $cameras = $component->viewData('cameras');

    // Should show all 6 cameras
    expect($cameras)->toHaveCount(6);
});

it('shows placeholder when fewer than 4 cameras available', function () {
    // Create only 2 active cameras
    Camera::factory()->count(2)->create(['is_active' => true]);

    Livewire::test(CameraGrid::class)
        ->assertSee('Additional Cameras')
        ->assertSee('Add more cameras to see them displayed here');
});

it('handles camera with no snapshot url gracefully', function () {
    $camera = Camera::factory()->create([
        'name' => 'No Snapshot Camera',
        'is_active' => true,
        'snapshot_url' => null,
    ]);

    Livewire::test(CameraGrid::class)
        ->assertSee('No Snapshot Camera');
});

it('can navigate to admin cameras from dashboard', function () {
    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Manage cameras');
});
