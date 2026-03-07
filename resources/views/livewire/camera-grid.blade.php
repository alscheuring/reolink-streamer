<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        @forelse($cameras->take(3) as $camera)
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 cursor-pointer group hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors"
                wire:click="viewCamera({{ $camera->id }})"
            >
                @if($camera->snapshot_url)
                    <img
                        src="{{ $camera->snapshot_url }}"
                        alt="{{ $camera->name }}"
                        class="absolute inset-0 w-full h-full object-cover"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    />
                    <div class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-800" style="display: none;">
                        <flux:icon.video-camera class="w-16 h-16 text-gray-400" />
                    </div>
                @else
                    <div class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-800">
                        <flux:icon.video-camera class="w-16 h-16 text-gray-400" />
                    </div>
                @endif

                <!-- Overlay with camera info -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="absolute bottom-0 left-0 right-0 p-4">
                        <h3 class="text-white font-medium text-sm truncate">{{ $camera->name }}</h3>
                        <div class="flex items-center mt-1">
                            <div class="w-2 h-2 rounded-full bg-green-400 mr-2"></div>
                            <span class="text-white/80 text-xs">Live</span>
                        </div>
                    </div>
                    <div class="absolute top-4 right-4">
                        <flux:icon.play class="w-6 h-6 text-white" />
                    </div>
                </div>

                <!-- Loading state for snapshot -->
                <div class="absolute inset-0 flex items-center justify-center bg-gray-50 dark:bg-gray-900"
                     x-data="{ loading: true }"
                     x-show="loading"
                     x-init="setTimeout(() => loading = false, 2000)">
                    <flux:icon.camera class="w-8 h-8 text-gray-400 animate-pulse" />
                </div>
            </div>
        @empty
            @for($i = 0; $i < 3; $i++)
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-800">
                        <flux:icon.video-camera class="w-12 h-12 text-gray-400 mb-2" />
                        <span class="text-gray-500 text-sm">No Camera</span>
                    </div>
                </div>
            @endfor
        @endforelse
    </div>

    <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        @if($cameras->count() > 3)
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 p-4 h-full">
                @foreach($cameras->skip(3)->take(3) as $camera)
                    <div
                        class="relative aspect-video overflow-hidden rounded-lg border border-neutral-200 dark:border-neutral-700 cursor-pointer group hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors"
                        wire:click="viewCamera({{ $camera->id }})"
                    >
                        @if($camera->snapshot_url)
                            <img
                                src="{{ $camera->snapshot_url }}"
                                alt="{{ $camera->name }}"
                                class="absolute inset-0 w-full h-full object-cover"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                            />
                            <div class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-800" style="display: none;">
                                <flux:icon.video-camera class="w-12 h-12 text-gray-400" />
                            </div>
                        @else
                            <div class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-800">
                                <flux:icon.video-camera class="w-12 h-12 text-gray-400" />
                            </div>
                        @endif

                        <!-- Overlay with camera info -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="absolute bottom-0 left-0 right-0 p-3">
                                <h3 class="text-white font-medium text-xs truncate">{{ $camera->name }}</h3>
                                <div class="flex items-center mt-1">
                                    <div class="w-1.5 h-1.5 rounded-full bg-green-400 mr-1.5"></div>
                                    <span class="text-white/80 text-xs">Live</span>
                                </div>
                            </div>
                            <div class="absolute top-3 right-3">
                                <flux:icon.play class="w-5 h-5 text-white" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-full">
                <flux:icon.video-camera class="w-20 h-20 text-gray-300 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Additional Cameras</h3>
                <p class="text-gray-500 text-center max-w-sm">
                    Add more cameras to see them displayed here.
                    <a href="{{ route('admin.cameras') }}" class="text-indigo-600 hover:text-indigo-500 font-medium" wire:navigate>
                        Manage cameras
                    </a>
                </p>
            </div>
        @endif
    </div>
</div>