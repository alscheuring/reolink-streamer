<x-layouts::app title="Home">
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900 px-6">
        <div class="max-w-2xl w-full text-center space-y-8">
            <!-- Simple Header -->
            <div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ config('app.name', 'Reolink Streamer') }}
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-300">
                    My security cameras
                </p>
            </div>

            @auth
                <!-- Camera Grid for logged in users -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <livewire:camera-grid />
                </div>

                <!-- Simple Navigation -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <flux:button
                        :href="route('dashboard')"
                        variant="primary"
                        wire:navigate
                    >
                        View All Cameras
                    </flux:button>

                    <flux:button
                        :href="route('admin.cameras')"
                        variant="outline"
                        wire:navigate
                    >
                        Settings
                    </flux:button>
                </div>
            @else
                <!-- Simple login for guests -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                        Sign in to view your cameras
                    </h2>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <flux:button
                            :href="route('login')"
                            variant="primary"
                        >
                            Sign In
                        </flux:button>

                        @if (Route::has('register'))
                            <flux:button
                                :href="route('register')"
                                variant="outline"
                            >
                                Register
                            </flux:button>
                        @endif
                    </div>
                </div>
            @endauth
        </div>
    </div>
</x-layouts::app>