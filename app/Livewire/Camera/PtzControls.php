<?php

namespace App\Livewire\Camera;

use App\Models\Camera;
use Livewire\Component;

class PtzControls extends Component
{
    public Camera $camera;

    public bool $isLoading = false;

    public string $currentOperation = '';

    public function mount(Camera $camera): void
    {
        $this->camera = $camera;

        // Check if PTZ actually works via API for this camera
        if ($this->camera->has_ptz && ! $this->camera->hasFunctionalPtz()) {
            $this->addError('ptz_compatibility', 'PTZ controls work in the Reolink app but are not available via API for this camera model/firmware version. Use the Reolink mobile app for PTZ control.');
        }
    }

    public function pan(string $direction): void
    {
        if (! $this->camera->has_ptz) {
            $this->addError('ptz', 'PTZ controls are not available for this camera.');

            return;
        }

        $this->setLoading("Panning {$direction}");

        try {
            $success = $this->camera->pan($direction, 25);

            if ($success) {
                $this->dispatch('ptz-command-sent', operation: "Pan {$direction}");
            } else {
                $this->addError('ptz', "Failed to pan {$direction}. Check camera connection.");
            }
        } catch (\Exception $e) {
            $this->addError('ptz', "PTZ command failed: {$e->getMessage()}");
        } finally {
            $this->clearLoading();
        }
    }

    public function tilt(string $direction): void
    {
        if (! $this->camera->has_ptz) {
            $this->addError('ptz', 'PTZ controls are not available for this camera.');

            return;
        }

        $this->setLoading("Tilting {$direction}");

        try {
            $success = $this->camera->tilt($direction, 25);

            if ($success) {
                $this->dispatch('ptz-command-sent', operation: "Tilt {$direction}");
            } else {
                $this->addError('ptz', "Failed to tilt {$direction}. Check camera connection.");
            }
        } catch (\Exception $e) {
            $this->addError('ptz', "PTZ command failed: {$e->getMessage()}");
        } finally {
            $this->clearLoading();
        }
    }

    public function zoom(string $direction): void
    {
        if (! $this->camera->has_ptz) {
            $this->addError('ptz', 'PTZ controls are not available for this camera.');

            return;
        }

        $this->setLoading("Zooming {$direction}");

        try {
            $success = $this->camera->zoom($direction, 25);

            if ($success) {
                $this->dispatch('ptz-command-sent', operation: "Zoom {$direction}");
            } else {
                $this->addError('ptz', "Failed to zoom {$direction}. Check camera connection.");
            }
        } catch (\Exception $e) {
            $this->addError('ptz', "PTZ command failed: {$e->getMessage()}");
        } finally {
            $this->clearLoading();
        }
    }

    public function stop(): void
    {
        if (! $this->camera->has_ptz) {
            return;
        }

        $this->setLoading('Stopping movement');

        try {
            $success = $this->camera->stopPtz();

            if ($success) {
                $this->dispatch('ptz-command-sent', operation: 'Stop movement');
            }
        } catch (\Exception $e) {
            $this->addError('ptz', "Failed to stop PTZ movement: {$e->getMessage()}");
        } finally {
            $this->clearLoading();
        }
    }

    private function setLoading(string $operation): void
    {
        $this->isLoading = true;
        $this->currentOperation = $operation;
    }

    private function clearLoading(): void
    {
        $this->isLoading = false;
        $this->currentOperation = '';
    }

    public function render()
    {
        return view('livewire.camera.ptz-controls');
    }
}
