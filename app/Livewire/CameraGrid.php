<?php

namespace App\Livewire;

use App\Models\Camera;
use Livewire\Component;

class CameraGrid extends Component
{
    public function render()
    {
        $cameras = Camera::active()
            ->ordered()
            ->limit(6) // Limit to 6 cameras for the grid (3 top + 3 additional)
            ->get();

        return view('livewire.camera-grid', compact('cameras'));
    }

    public function viewCamera(Camera $camera): void
    {
        $this->redirect(route('camera.stream', ['camera' => $camera->id]));
    }
}
