<?php

namespace App\Livewire\Admin;

use App\Models\Camera;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class CameraManager extends Component
{
    use WithPagination;

    public bool $showCreateForm = false;

    public bool $showEditForm = false;

    public ?Camera $editingCamera = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|url')]
    public string $rtsp_url = '';

    #[Validate('nullable|url')]
    public string $snapshot_url = '';

    #[Validate('boolean')]
    public bool $is_active = true;

    #[Validate('integer|min:0')]
    public int $sort_order = 0;

    public string $search = '';

    public function mount(): void
    {
        $this->resetForm();
    }

    public function render()
    {
        $cameras = Camera::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->ordered()
            ->paginate(10);

        return view('livewire.admin.camera-manager', compact('cameras'))
            ->layout('layouts.app');
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showCreateForm = true;
        $this->showEditForm = false;
    }

    public function edit(Camera $camera): void
    {
        $this->editingCamera = $camera;
        $this->name = $camera->name;
        $this->rtsp_url = $camera->rtsp_url;
        $this->snapshot_url = $camera->snapshot_url ?? '';
        $this->is_active = $camera->is_active;
        $this->sort_order = $camera->sort_order;
        $this->showEditForm = true;
        $this->showCreateForm = false;
    }

    public function store(): void
    {
        $this->validate();

        Camera::create([
            'name' => $this->name,
            'rtsp_url' => $this->rtsp_url,
            'snapshot_url' => $this->snapshot_url ?: null,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ]);

        $this->resetForm();
        $this->showCreateForm = false;

        session()->flash('message', 'Camera created successfully.');
    }

    public function update(): void
    {
        $this->validate();

        $this->editingCamera->update([
            'name' => $this->name,
            'rtsp_url' => $this->rtsp_url,
            'snapshot_url' => $this->snapshot_url ?: null,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ]);

        $this->resetForm();
        $this->showEditForm = false;

        session()->flash('message', 'Camera updated successfully.');
    }

    public function delete(Camera $camera): void
    {
        $camera->delete();
        session()->flash('message', 'Camera deleted successfully.');
    }

    public function testConnection(Camera $camera): void
    {
        if ($camera->isReachable()) {
            session()->flash('connection-test', "Camera '{$camera->name}' is reachable!");
        } else {
            session()->flash('connection-test', "Camera '{$camera->name}' is not reachable. Check the URL and network connection.");
        }
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showCreateForm = false;
        $this->showEditForm = false;
    }

    protected function resetForm(): void
    {
        $this->name = '';
        $this->rtsp_url = '';
        $this->snapshot_url = '';
        $this->is_active = true;
        $this->sort_order = 0;
        $this->editingCamera = null;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
}
