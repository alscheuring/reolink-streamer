<?php

use App\Livewire\Admin\CameraManager;
use App\Models\Camera;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('can access camera admin page when authenticated', function () {
    $this->get(route('admin.cameras'))
        ->assertSuccessful()
        ->assertSee('Camera Management');
});

it('redirects to login when not authenticated', function () {
    auth()->logout();

    $this->get(route('admin.cameras'))
        ->assertRedirect(route('login'));
});

it('displays cameras in the admin interface', function () {
    $camera1 = Camera::factory()->create(['name' => 'Front Door Camera']);
    $camera2 = Camera::factory()->create(['name' => 'Back Yard Camera']);

    Livewire::test(CameraManager::class)
        ->assertSee('Front Door Camera')
        ->assertSee('Back Yard Camera')
        ->assertSee('Camera Management');
});

it('can create a new camera', function () {
    Livewire::test(CameraManager::class)
        ->call('create')
        ->assertSet('showCreateForm', true)
        ->set('name', 'Test Camera')
        ->set('rtsp_url', 'rtsp://admin:password@192.168.1.100:554/h264Preview_01_main')
        ->set('snapshot_url', 'http://192.168.1.100/snapshot.jpg')
        ->set('is_active', true)
        ->set('sort_order', 10)
        ->call('store')
        ->assertSet('showCreateForm', false)
        ->assertSessionHas('message', 'Camera created successfully.');

    expect(Camera::where('name', 'Test Camera')->exists())->toBeTrue();
});

it('validates camera creation fields', function () {
    Livewire::test(CameraManager::class)
        ->call('create')
        ->set('name', '') // Empty name
        ->set('rtsp_url', 'invalid-url') // Invalid URL
        ->call('store')
        ->assertHasErrors(['name', 'rtsp_url'])
        ->assertSet('showCreateForm', true);
});

it('can edit an existing camera', function () {
    $camera = Camera::factory()->create([
        'name' => 'Original Name',
        'rtsp_url' => 'rtsp://admin:pass@192.168.1.100:554/stream',
    ]);

    Livewire::test(CameraManager::class)
        ->call('edit', $camera)
        ->assertSet('showEditForm', true)
        ->assertSet('name', 'Original Name')
        ->set('name', 'Updated Name')
        ->call('update')
        ->assertSet('showEditForm', false)
        ->assertSessionHas('message', 'Camera updated successfully.');

    expect($camera->fresh()->name)->toBe('Updated Name');
});

it('can delete a camera', function () {
    $camera = Camera::factory()->create(['name' => 'Camera to Delete']);

    Livewire::test(CameraManager::class)
        ->call('delete', $camera)
        ->assertSessionHas('message', 'Camera deleted successfully.');

    expect(Camera::find($camera->id))->toBeNull();
});

it('can search cameras by name', function () {
    Camera::factory()->create(['name' => 'Front Door Camera']);
    Camera::factory()->create(['name' => 'Back Yard Camera']);
    Camera::factory()->create(['name' => 'Kitchen Camera']);

    Livewire::test(CameraManager::class)
        ->set('search', 'Kitchen')
        ->assertSee('Kitchen Camera')
        ->assertDontSee('Front Door Camera')
        ->assertDontSee('Back Yard Camera');
});

it('can cancel camera creation', function () {
    Livewire::test(CameraManager::class)
        ->call('create')
        ->assertSet('showCreateForm', true)
        ->set('name', 'Test Camera')
        ->call('cancel')
        ->assertSet('showCreateForm', false)
        ->assertSet('name', '');
});

it('resets form when switching between create and edit', function () {
    $camera = Camera::factory()->create(['name' => 'Existing Camera']);

    Livewire::test(CameraManager::class)
        ->call('create')
        ->set('name', 'New Camera')
        ->call('edit', $camera)
        ->assertSet('name', 'Existing Camera')
        ->assertSet('showCreateForm', false)
        ->assertSet('showEditForm', true);
});

it('displays camera status badges correctly', function () {
    $activeCamera = Camera::factory()->create(['name' => 'Active Camera', 'is_active' => true]);
    $inactiveCamera = Camera::factory()->create(['name' => 'Inactive Camera', 'is_active' => false]);

    Livewire::test(CameraManager::class)
        ->assertSee('Active Camera')
        ->assertSee('Inactive Camera')
        ->assertSeeInOrder(['Active Camera', 'Active']) // Active badge
        ->assertSeeInOrder(['Inactive Camera', 'Inactive']); // Inactive badge
});

it('can test camera connection', function () {
    $camera = Camera::factory()->create(['name' => 'Test Camera']);

    Livewire::test(CameraManager::class)
        ->call('testConnection', $camera)
        ->assertSessionHas('connection-test');
});
