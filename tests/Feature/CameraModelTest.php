<?php

use App\Models\Camera;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a camera with valid attributes', function () {
    $camera = Camera::factory()->create([
        'name' => 'Front Door Camera',
        'rtsp_url' => 'rtsp://admin:password@192.168.1.100:554/h264Preview_01_main',
        'is_active' => true,
        'sort_order' => 10,
    ]);

    expect($camera)->toBeInstanceOf(Camera::class)
        ->and($camera->name)->toBe('Front Door Camera')
        ->and($camera->rtsp_url)->toBe('rtsp://admin:password@192.168.1.100:554/h264Preview_01_main')
        ->and($camera->is_active)->toBeTrue()
        ->and($camera->sort_order)->toBe(10);
});

it('casts attributes to correct types', function () {
    $camera = Camera::factory()->create([
        'is_active' => '1',
        'sort_order' => '15',
    ]);

    expect($camera->is_active)->toBeBool()
        ->and($camera->sort_order)->toBeInt();
});

it('can scope to active cameras only', function () {
    Camera::factory()->create(['is_active' => true]);
    Camera::factory()->create(['is_active' => false]);
    Camera::factory()->create(['is_active' => true]);

    $activeCameras = Camera::active()->get();

    expect($activeCameras)->toHaveCount(2)
        ->and($activeCameras->every(fn ($camera) => $camera->is_active))->toBeTrue();
});

it('can scope cameras by sort order', function () {
    $camera1 = Camera::factory()->create(['sort_order' => 3, 'name' => 'C Camera']);
    $camera2 = Camera::factory()->create(['sort_order' => 1, 'name' => 'A Camera']);
    $camera3 = Camera::factory()->create(['sort_order' => 2, 'name' => 'B Camera']);

    $orderedCameras = Camera::ordered()->get();

    expect($orderedCameras->first()->id)->toBe($camera2->id)
        ->and($orderedCameras->get(1)->id)->toBe($camera3->id)
        ->and($orderedCameras->last()->id)->toBe($camera1->id);
});

it('generates snapshot url from rtsp url when snapshot_url is null', function () {
    $camera = Camera::factory()->create([
        'rtsp_url' => 'rtsp://admin:password123@192.168.1.150:554/h264Preview_01_main',
        'snapshot_url' => null,
    ]);

    $snapshotUrl = $camera->snapshot_url;

    expect($snapshotUrl)->toContain('192.168.1.150')
        ->and($snapshotUrl)->toContain('cmd=Snap')
        ->and($snapshotUrl)->toContain('password=password123');
});

it('returns existing snapshot_url when set', function () {
    $customUrl = 'http://192.168.1.100/custom/snapshot.jpg';
    $camera = Camera::factory()->create([
        'snapshot_url' => $customUrl,
    ]);

    expect($camera->snapshot_url)->toBe($customUrl);
});

it('returns null snapshot url for invalid rtsp url', function () {
    $camera = Camera::factory()->create([
        'rtsp_url' => 'invalid-url',
        'snapshot_url' => null,
    ]);

    expect($camera->snapshot_url)->toBeNull();
});

it('has fillable attributes', function () {
    $fillable = [
        'name',
        'rtsp_url',
        'snapshot_url',
        'is_active',
        'sort_order',
    ];

    $camera = new Camera;

    expect($camera->getFillable())->toBe($fillable);
});
