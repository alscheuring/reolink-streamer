<div>
    @if($camera->has_ptz)
        <!-- PTZ Control Panel -->
        <flux:card class="relative space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading size="sm">PTZ Controls</flux:heading>
                @if($isLoading)
                    <div class="flex items-center gap-2">
                        <flux:icon.arrow-path class="w-4 h-4 animate-spin" />
                        <flux:text size="sm" variant="muted">{{ $currentOperation }}</flux:text>
                    </div>
                @endif
            </div>

            <div>
                <!-- Display errors -->
                @error('ptz')
                    <flux:callout color="red" icon="exclamation-triangle" class="mb-4">
                        <flux:callout.text>{{ $message }}</flux:callout.text>
                    </flux:callout>
                @enderror

                @error('ptz_compatibility')
                    <flux:callout color="amber" icon="information-circle" class="mb-4">
                        <flux:callout.heading>API Limitation</flux:callout.heading>
                        <flux:callout.text>{{ $message }}</flux:callout.text>
                    </flux:callout>
                @enderror

                <!-- Directional Controls -->
                <div class="space-y-6">
                    <!-- Pan/Tilt Controls -->
                    <div class="flex flex-col items-center space-y-2">
                        <flux:heading size="xs" class="mb-2">Pan & Tilt</flux:heading>

                        <!-- Up Button -->
                        <flux:button
                            wire:click="tilt('up')"
                            wire:loading.attr="disabled"
                            variant="outline"
                            size="sm"
                            icon="chevron-up"
                            class="w-12 h-12"
                        >
                        </flux:button>

                        <!-- Left, Stop, Right -->
                        <div class="flex items-center space-x-2">
                            <flux:button
                                wire:click="pan('left')"
                                wire:loading.attr="disabled"
                                variant="outline"
                                size="sm"
                                icon="chevron-left"
                                class="w-12 h-12"
                            >
                            </flux:button>

                            <flux:button
                                wire:click="stop"
                                wire:loading.attr="disabled"
                                variant="primary"
                                size="sm"
                                class="w-16 h-12"
                            >
                                STOP
                            </flux:button>

                            <flux:button
                                wire:click="pan('right')"
                                wire:loading.attr="disabled"
                                variant="outline"
                                size="sm"
                                icon="chevron-right"
                                class="w-12 h-12"
                            >
                            </flux:button>
                        </div>

                        <!-- Down Button -->
                        <flux:button
                            wire:click="tilt('down')"
                            wire:loading.attr="disabled"
                            variant="outline"
                            size="sm"
                            icon="chevron-down"
                            class="w-12 h-12"
                        >
                        </flux:button>
                    </div>

                    <!-- Zoom Controls -->
                    <div class="border-t pt-4">
                        <flux:heading size="xs" class="mb-3 text-center">Zoom</flux:heading>
                        <div class="flex justify-center space-x-4">
                            <flux:button
                                wire:click="zoom('out')"
                                wire:loading.attr="disabled"
                                variant="outline"
                                icon="minus"
                                size="sm"
                            >
                                Zoom Out
                            </flux:button>

                            <flux:button
                                wire:click="zoom('in')"
                                wire:loading.attr="disabled"
                                variant="outline"
                                icon="plus"
                                size="sm"
                            >
                                Zoom In
                            </flux:button>
                        </div>
                    </div>

                    <!-- Camera Status -->
                    <div class="border-t pt-4">
                        <div class="flex items-center justify-between text-sm">
                            <flux:text variant="muted">PTZ Status:</flux:text>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-green-400"></div>
                                <flux:text size="sm" class="text-green-600 dark:text-green-400">Ready</flux:text>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Loading overlay -->
            @if($isLoading)
                <div class="absolute inset-0 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm rounded-lg flex items-center justify-center">
                    <div class="flex flex-col items-center space-y-2">
                        <flux:icon.arrow-path class="w-8 h-8 animate-spin text-primary-600" />
                        <flux:text size="sm" class="text-gray-600 dark:text-gray-300">{{ $currentOperation }}</flux:text>
                    </div>
                </div>
            @endif
        </flux:card>
    @else
        <!-- PTZ Not Available -->
        <flux:card class="space-y-6">
            <flux:heading size="sm">PTZ Controls</flux:heading>

            <div class="text-center">
                <flux:icon.exclamation-triangle class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                <flux:text variant="muted" class="block mb-2">PTZ Not Available</flux:text>
                <flux:text size="sm" variant="muted">This camera does not support pan, tilt, and zoom controls.</flux:text>
            </div>
        </flux:card>
    @endif
</div>

<script>
    // Listen for PTZ command events
    document.addEventListener('livewire:ptz-command-sent', (event) => {
        // Optional: Show a toast notification or update UI
        console.log('PTZ Command:', event.detail.operation);

        // Optional: Refresh the camera stream after PTZ movement
        // You can add a small delay and then refresh the camera image
        setTimeout(() => {
            if (typeof refreshStream === 'function') {
                refreshStream();
            }
        }, 1000);
    });
</script>