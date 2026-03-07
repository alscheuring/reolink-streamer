<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Reolink Streamer') }} - Professional Security Camera Streaming</title>

    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <!-- Header Navigation -->
    <header class="absolute top-0 left-0 right-0 z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/20 dark:border-slate-700/20">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white">
                        {{ config('app.name', 'Reolink Streamer') }}
                    </h1>
                </div>

                <!-- Navigation -->
                @if (Route::has('login'))
                    <nav class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">
                                Sign In
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
        <!-- Hero Section -->
        <div class="relative pt-24">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 pt-16 pb-16 text-center">
                <!-- Hero Content -->
                <div class="max-w-4xl mx-auto">
                    <h1 class="text-5xl lg:text-7xl font-bold tracking-tight text-slate-900 dark:text-white mb-8 leading-tight">
                        Professional
                        <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            Security Streaming
                        </span>
                    </h1>

                    <p class="text-xl lg:text-2xl text-slate-600 dark:text-slate-300 mb-12 leading-relaxed max-w-3xl mx-auto">
                        Monitor your property with confidence. Stream, manage, and view all your Reolink cameras from one powerful, intuitive interface.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white text-lg font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                               class="inline-flex items-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white text-lg font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                Get Started
                            </a>
                            <a href="{{ route('login') }}"
                               class="px-8 py-4 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white text-lg font-medium rounded-lg transition-colors">
                                Sign In
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Decorative Element -->
            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-full h-full overflow-hidden -z-10">
                <div class="absolute top-20 left-1/4 w-72 h-72 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
                <div class="absolute top-32 right-1/4 w-72 h-72 bg-indigo-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse delay-1000"></div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-20">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                    Everything you need for security monitoring
                </h2>
                <p class="text-xl text-slate-600 dark:text-slate-300 max-w-2xl mx-auto">
                    Built specifically for Reolink cameras with professional-grade features and enterprise reliability.
                </p>
            </div>

            <!-- Feature Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-20">
                <!-- Real-time Streaming -->
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg border border-slate-200/50 dark:border-slate-700/50 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">
                        Real-time Streaming
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                        Watch live feeds from all your Reolink cameras with low latency RTSP streaming technology.
                    </p>
                </div>

                <!-- Smart Management -->
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg border border-slate-200/50 dark:border-slate-700/50 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">
                        Smart Management
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                        Easily add, configure, and organize your cameras with an intuitive admin interface.
                    </p>
                </div>

                <!-- Responsive Design -->
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg border border-slate-200/50 dark:border-slate-700/50 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a1 1 0 001-1v-1H7v1a1 1 0 001 1zM10 2v2h4V2M7 4h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">
                        Responsive Design
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                        Access your cameras from any device - desktop, tablet, or mobile with full functionality.
                    </p>
                </div>

                <!-- Secure Access -->
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg border border-slate-200/50 dark:border-slate-700/50 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">
                        Secure Access
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                        Protected by Laravel's robust authentication system with role-based access control.
                    </p>
                </div>

                <!-- Easy Integration -->
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg border border-slate-200/50 dark:border-slate-700/50 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a1 1 0 01-1-1V9a1 1 0 011-1h1a2 2 0 100-4H4a1 1 0 01-1-1V4a1 1 0 011-1h3a1 1 0 001-1z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">
                        Easy Integration
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                        Seamlessly connects with your existing Reolink camera setup using standard RTSP protocols.
                    </p>
                </div>

                <!-- High Performance -->
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg border border-slate-200/50 dark:border-slate-700/50 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-teal-100 dark:bg-teal-900 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">
                        High Performance
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                        Built on Laravel 12 and Livewire 4 for optimal performance and real-time updates.
                    </p>
                </div>
            </div>

            <!-- Camera Preview Section -->
            @auth
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-8 lg:p-12 shadow-xl border border-slate-200/50 dark:border-slate-700/50">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-4">
                            Your Camera Dashboard
                        </h2>
                        <p class="text-lg text-slate-600 dark:text-slate-300">
                            Quick preview of your connected cameras
                        </p>
                    </div>

                    <!-- Camera Grid Component -->
                    <div class="max-w-4xl mx-auto">
                        <livewire:camera-grid />
                    </div>

                    <div class="text-center mt-8">
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center px-6 py-3 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-medium rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            View All Cameras
                        </a>
                    </div>
                </div>
            @else
                <!-- CTA for Non-authenticated Users -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 lg:p-12 text-center text-white shadow-2xl">
                    <h2 class="text-2xl lg:text-3xl font-bold mb-4">
                        Ready to secure your property?
                    </h2>
                    <p class="text-xl mb-8 text-blue-100">
                        Start monitoring your Reolink cameras with our professional streaming platform.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}"
                           class="px-6 py-3 border border-white text-white hover:bg-white hover:text-blue-600 font-medium rounded-lg transition-colors">
                            Create Account
                        </a>
                        <a href="{{ route('login') }}"
                           class="px-6 py-3 text-white hover:bg-white/10 font-medium rounded-lg transition-colors">
                            Sign In
                        </a>
                    </div>
                </div>
            @endauth
        </div>

        <!-- Footer -->
        <footer class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm border-t border-slate-200/50 dark:border-slate-700/50">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">
                        Reolink Streamer
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 mb-6">
                        Professional security camera streaming platform
                    </p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Built with Laravel 12, Livewire 4, and Tailwind CSS
                    </p>
                </div>
            </div>
        </footer>
    </main>

    @livewireScripts
</body>
</html>