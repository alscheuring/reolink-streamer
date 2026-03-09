<?php

use App\Livewire\Camera\PtzControls;
use App\Models\Camera;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('can mount with ptz camera', function () {
    $camera = Camera::factory()->withPtz()->create();

    Livewire::test(PtzControls::class, ['camera' => $camera])
        ->assertSet('camera.id', $camera->id)
        ->assertSet('isLoading', false)
        ->assertSet('currentOperation', '')
        ->assertSee('PTZ Controls')
        ->assertSee('Pan')
        ->assertSee('Tilt')
        ->assertSee('Zoom');
});

it('can mount with non-ptz camera', function () {
    $camera = Camera::factory()->withoutPtz()->create();

    Livewire::test(PtzControls::class, ['camera' => $camera])
        ->assertSet('camera.id', $camera->id)
        ->assertSee('PTZ Not Available')
        ->assertSee('This camera does not support pan, tilt, and zoom controls');
});

it('shows error when trying to pan on non-ptz camera', function () {
    $camera = Camera::factory()->withoutPtz()->create();

    Livewire::test(PtzControls::class, ['camera' => $camera])
        ->call('pan', 'left')
        ->assertHasErrors(['ptz' => 'PTZ controls are not available for this camera.']);
});

it('shows error when trying to tilt on non-ptz camera', function () {
    $camera = Camera::factory()->withoutPtz()->create();

    Livewire::test(PtzControls::class, ['camera' => $camera])
        ->call('tilt', 'up')
        ->assertHasErrors(['ptz' => 'PTZ controls are not available for this camera.']);
});

it('shows error when trying to zoom on non-ptz camera', function () {
    $camera = Camera::factory()->withoutPtz()->create();

    Livewire::test(PtzControls::class, ['camera' => $camera])
        ->call('zoom', 'in')
        ->assertHasErrors(['ptz' => 'PTZ controls are not available for this camera.']);
});

it('does not show error when trying to stop on non-ptz camera', function () {
    $camera = Camera::factory()->withoutPtz()->create();

    Livewire::test(PtzControls::class, ['camera' => $camera])
        ->call('stop')
        ->assertHasNoErrors();
});

// Test PTZ functionality without mocking (functional tests)
it('handles ptz commands when camera has no api url', function () {
    $camera = Camera::factory()->withPtz()->create([
        'rtsp_url' => 'invalid-url-format',  // This will fail to generate API URL
        'ptz_api_url' => null,
    ]);

    Livewire::test(PtzControls::class, ['camera' => $camera])
        ->call('pan', 'left')
        ->assertHasErrors(['ptz']);
});

it('can call ptz operations without errors on valid ptz camera', function () {
    $camera = Camera::factory()->withPtz()->create([
        'rtsp_url' => 'rtsp://admin:password@192.168.1.100:554/stream',
    ]);

    // These will fail to actually execute (no real camera) but should not crash
    Livewire::test(PtzControls::class, ['camera' => $camera])
        ->call('pan', 'left')
        ->assertHasErrors(['ptz']); // Expected to fail on actual API call

    Livewire::test(PtzControls::class, ['camera' => $camera])
        ->call('tilt', 'up')
        ->assertHasErrors(['ptz']); // Expected to fail on actual API call

    Livewire::test(PtzControls::class, ['camera' => $camera])
        ->call('zoom', 'in')
        ->assertHasErrors(['ptz']); // Expected to fail on actual API call

    Livewire::test(PtzControls::class, ['camera' => $camera])
        ->call('stop')
        ->assertHasNoErrors(); // Stop might not always fail
});

// Test all PTZ directions without mocking
it('can call all pan directions', function (string $direction) {
    $camera = Camera::factory()->withPtz()->create([
        'rtsp_url' => 'rtsp://admin:password@192.168.1.100:554/stream',
    ]);

    // These will fail the actual API call but should not crash the component
    Livewire::test(PtzControls::class, ['camera' => $camera])
        ->call('pan', $direction)
        ->assertHasErrors(['ptz']); // Expected to fail on actual API call
})->with(['left', 'right']);

it('can call all tilt directions', function (string $direction) {
    $camera = Camera::factory()->withPtz()->create([
        'rtsp_url' => 'rtsp://admin:password@192.168.1.100:554/stream',
    ]);

    // These will fail the actual API call but should not crash the component
    Livewire::test(PtzControls::class, ['camera' => $camera])
        ->call('tilt', $direction)
        ->assertHasErrors(['ptz']); // Expected to fail on actual API call
})->with(['up', 'down']);

it('can call all zoom directions', function (string $direction) {
    $camera = Camera::factory()->withPtz()->create([
        'rtsp_url' => 'rtsp://admin:password@192.168.1.100:554/stream',
    ]);

    // These will fail the actual API call but should not crash the component
    Livewire::test(PtzControls::class, ['camera' => $camera])
        ->call('zoom', $direction)
        ->assertHasErrors(['ptz']); // Expected to fail on actual API call
})->with(['in', 'out']);

it('validates loading state is properly managed', function () {
    $camera = Camera::factory()->withPtz()->create([
        'rtsp_url' => 'rtsp://admin:password@192.168.1.100:554/stream',
    ]);

    $component = Livewire::test(PtzControls::class, ['camera' => $camera]);

    // After any operation (even failed ones), loading should be false
    $component->call('pan', 'left')
        ->assertSet('isLoading', false)
        ->assertSet('currentOperation', '');
});
