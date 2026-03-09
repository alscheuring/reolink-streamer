<x-layouts::app :title="$camera->name . ' - Live Stream'">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button
                    variant="outline"
                    icon="arrow-left"
                    :href="route('dashboard')"
                    wire:navigate
                >
                    Back to Dashboard
                </flux:button>
                <div>
                    <flux:heading size="lg">{{ $camera->name }}</flux:heading>
                    <flux:text variant="muted">Live Camera Stream</flux:text>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-green-400"></div>
                <flux:text size="sm" class="text-green-600 dark:text-green-400">Live</flux:text>
            </div>
        </div>

        <div class="flex-1 flex gap-4">
            <!-- Main Stream Area -->
            <div class="flex-1 flex flex-col gap-4">
                <div class="relative flex-1 min-h-96 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-black">
                @if($camera->snapshot_url)
                    <!-- For now, show the snapshot image as a placeholder -->
                    <img
                        src="{{ $camera->snapshot_url }}"
                        alt="{{ $camera->name }} live stream"
                        class="w-full h-full object-contain"
                        id="cameraStream"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    />
                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900" id="streamError" style="display: none;">
                        <flux:icon.exclamation-triangle class="w-16 h-16 text-yellow-400 mb-4" />
                        <h3 class="text-white text-lg font-medium mb-2">Stream Unavailable</h3>
                        <p class="text-gray-300 text-center">Unable to load camera feed. Please check camera connection.</p>
                    </div>
                @else
                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900">
                        <flux:icon.video-camera class="w-20 h-20 text-gray-400 mb-4" />
                        <h3 class="text-white text-lg font-medium mb-2">Stream Configuration Required</h3>
                        <p class="text-gray-300 text-center mb-4">Camera snapshot URL not configured.</p>
                        <flux:button
                            variant="primary"
                            :href="route('admin.cameras')"
                            wire:navigate
                        >
                            Configure Camera
                        </flux:button>
                    </div>
                @endif

                <!-- Stream Controls Overlay -->
                <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between bg-black/50 rounded-lg p-3 backdrop-blur-sm">
                    <div class="flex items-center gap-4">
                        <flux:button variant="ghost" icon="play" size="sm" class="text-white hover:bg-white/20">
                            Play
                        </flux:button>
                        <flux:button variant="ghost" icon="speaker-x-mark" size="sm" class="text-white hover:bg-white/20">
                            Mute
                        </flux:button>
                        <flux:button variant="ghost" icon="arrows-pointing-out" size="sm" class="text-white hover:bg-white/20">
                            Fullscreen
                        </flux:button>
                    </div>
                    <div class="flex items-center gap-2 text-white text-sm">
                        <span>Quality:</span>
                        <flux:button variant="ghost" size="sm" class="text-white hover:bg-white/20">
                            Auto
                        </flux:button>
                    </div>
                </div>

                <!-- Stream Information -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:card>
                        <div class="p-4">
                            <flux:text variant="muted" size="sm">Camera Status</flux:text>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-2 h-2 rounded-full bg-green-400"></div>
                                <flux:text class="font-medium">Online</flux:text>
                            </div>
                        </div>
                    </flux:card>

                    <flux:card>
                        <div class="p-4">
                            <flux:text variant="muted" size="sm">Stream URL</flux:text>
                            <flux:text class="font-mono text-xs mt-1 break-all">{{ Str::limit($camera->rtsp_url, 30) }}</flux:text>
                        </div>
                    </flux:card>

                    <flux:card>
                        <div class="p-4">
                            <flux:text variant="muted" size="sm">Actions</flux:text>
                            <div class="flex gap-2 mt-2">
                                <flux:button size="sm" variant="outline" icon="arrow-path" onclick="refreshStream()">
                                    Refresh
                                </flux:button>
                                <flux:button size="sm" variant="outline" icon="cog-6-tooth" :href="route('admin.cameras')" wire:navigate>
                                    Settings
                                </flux:button>
                            </div>
                        </div>
                    </flux:card>
                </div>
            </div>

            <!-- PTZ Controls Sidebar -->
            <div class="w-80 flex-shrink-0">
                <livewire:camera.ptz-controls :camera="$camera" />
            </div>
        </div>
    </div>

    <!-- Auto-refresh script for the snapshot -->
    <script>
        function refreshStream() {
            const img = document.getElementById('cameraStream');
            if (img) {
                // Add timestamp to force refresh
                const originalSrc = img.src.split('&_t=')[0];
                img.src = originalSrc + '&_t=' + new Date().getTime();
            }
        }

        // Auto-refresh every 5 seconds for live feel
        setInterval(refreshStream, 5000);
    </script>
</x-layouts::app>