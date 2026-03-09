<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Health check for Docker containers
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'app' => config('app.name'),
        'version' => '1.0.0',
    ], 200);
})->name('health');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('admin/cameras', App\Livewire\Admin\CameraManager::class)->name('admin.cameras');
    Route::get('camera/{camera}/stream', [App\Http\Controllers\CameraController::class, 'stream'])->name('camera.stream');
});

require __DIR__.'/settings.php';
