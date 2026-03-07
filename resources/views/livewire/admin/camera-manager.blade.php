<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="lg">Camera Management</flux:heading>
        <flux:button icon="plus" variant="primary" wire:click="create">
            Add Camera
        </flux:button>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success" class="mb-4">
            {{ session('message') }}
        </flux:callout>
    @endif

    @if (session()->has('connection-test'))
        <flux:callout variant="info" class="mb-4">
            {{ session('connection-test') }}
        </flux:callout>
    @endif

    <div class="mb-4">
        <flux:field>
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search cameras..." />
        </flux:field>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Camera
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        RTSP URL
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Order
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($cameras as $camera)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if ($camera->snapshot_url)
                                        <img class="h-10 w-10 rounded-md object-cover"
                                             src="{{ $camera->snapshot_url }}"
                                             alt="Camera thumbnail"
                                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yMCAyNkMyMyAyNiAyNSAyNCAyNSAyMUMyNSAxOCAyMyAxNiAyMCAxNkMxNyAxNiAxNSAxOCAxNSAyMUMxNSAyNCAxNyAyNiAyMCAyNloiIGZpbGw9IiM5CA0ODUzIi8+CjxwYXRoIGQ9Ik0xMiAxNEgzMEMzMS4xIDEyIDMyIDEyLjkgMzIgMTRWMjZDMzIgMjcuMSAzMS4xIDI4IDMwIDI4SDEyQzEwLjkgMjggMTAgMjcuMSAxMCAyNlYxNEMxMCAxMi45IDEwLjkgMTIgMTIgMTJaIiBzdHJva2U9IiM2MzczODQiIHN0cm9rZS13aWR0aD0iMiIgZmlsbD0ibm9uZSIvPgo8L3N2Zz4K'">
                                    @else
                                        <div class="h-10 w-10 rounded-md bg-gray-300 flex items-center justify-center">
                                            <flux:icon.video-camera class="h-5 w-5 text-gray-500" />
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $camera->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-mono truncate max-w-xs" title="{{ $camera->rtsp_url }}">
                                {{ Str::limit($camera->rtsp_url, 40) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($camera->is_active)
                                <flux:badge variant="success">Active</flux:badge>
                            @else
                                <flux:badge variant="outline">Inactive</flux:badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $camera->sort_order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <flux:button size="sm" variant="outline" icon="wifi" wire:click="testConnection({{ $camera->id }})">
                                Test
                            </flux:button>
                            <flux:button size="sm" variant="outline" icon="pencil" wire:click="edit({{ $camera->id }})">
                                Edit
                            </flux:button>
                            <flux:button size="sm" variant="danger" icon="trash"
                                         wire:click="delete({{ $camera->id }})"
                                         wire:confirm="Are you sure you want to delete this camera?">
                                Delete
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No cameras found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $cameras->links() }}
    </div>

    <!-- Create Camera Modal -->
    <flux:modal wire:model="showCreateForm">
        <div class="p-6">
            <flux:heading size="lg" class="mb-6">Add New Camera</flux:heading>

            <form wire:submit="store" class="space-y-4">
                <flux:field>
                    <flux:label>Camera Name</flux:label>
                    <flux:input wire:model="name" placeholder="e.g., Front Door Camera" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>RTSP URL</flux:label>
                    <flux:input wire:model="rtsp_url" placeholder="rtsp://username:password@192.168.1.100:554/h264Preview_01_main" />
                    <flux:error name="rtsp_url" />
                </flux:field>

                <flux:field>
                    <flux:label>Snapshot URL (Optional)</flux:label>
                    <flux:input wire:model="snapshot_url" placeholder="http://192.168.1.100/cgi-bin/api.cgi?cmd=Snap..." />
                    <flux:error name="snapshot_url" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Sort Order</flux:label>
                        <flux:input type="number" wire:model="sort_order" min="0" />
                        <flux:error name="sort_order" />
                    </flux:field>

                    <flux:field>
                        <div class="flex items-center mt-6">
                            <flux:switch wire:model="is_active" />
                            <flux:text class="ml-2">Camera is active</flux:text>
                        </div>
                    </flux:field>
                </div>

                <div class="flex justify-end space-x-2 pt-4">
                    <flux:button variant="outline" wire:click="cancel">Cancel</flux:button>
                    <flux:button type="submit" variant="primary">Create Camera</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Edit Camera Modal -->
    <flux:modal wire:model="showEditForm">
        <div class="p-6">
            <flux:heading size="lg" class="mb-6">Edit Camera</flux:heading>

            <form wire:submit="update" class="space-y-4">
                <flux:field>
                    <flux:label>Camera Name</flux:label>
                    <flux:input wire:model="name" placeholder="e.g., Front Door Camera" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>RTSP URL</flux:label>
                    <flux:input wire:model="rtsp_url" placeholder="rtsp://username:password@192.168.1.100:554/h264Preview_01_main" />
                    <flux:error name="rtsp_url" />
                </flux:field>

                <flux:field>
                    <flux:label>Snapshot URL (Optional)</flux:label>
                    <flux:input wire:model="snapshot_url" placeholder="http://192.168.1.100/cgi-bin/api.cgi?cmd=Snap..." />
                    <flux:error name="snapshot_url" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Sort Order</flux:label>
                        <flux:input type="number" wire:model="sort_order" min="0" />
                        <flux:error name="sort_order" />
                    </flux:field>

                    <flux:field>
                        <div class="flex items-center mt-6">
                            <flux:switch wire:model="is_active" />
                            <flux:text class="ml-2">Camera is active</flux:text>
                        </div>
                    </flux:field>
                </div>

                <div class="flex justify-end space-x-2 pt-4">
                    <flux:button variant="outline" wire:click="cancel">Cancel</flux:button>
                    <flux:button type="submit" variant="primary">Update Camera</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>