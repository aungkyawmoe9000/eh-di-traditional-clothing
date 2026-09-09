<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Eh Di traditional Clothing Shop</title>

    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="bg-stone-50 dark:bg-stone-950 text-stone-800 dark:text-stone-100 flex items-center justify-center min-h-screen antialiased p-4">
    <div class="relative w-full max-w-md bg-white dark:bg-stone-900 rounded-2xl shadow-xl border border-stone-200/80 dark:border-stone-800 p-8 text-center transition-all duration-300 hover:shadow-2xl">
        <!-- Top decorative traditional accent border -->
        <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-amber-500 via-rose-500 to-emerald-500 rounded-t-2xl"></div>

        <!-- Logo Container -->
        <div class="flex justify-center mb-6 mt-2">
            <div class="p-1 rounded-full bg-stone-50 dark:bg-stone-800 shadow-inner">
                <img src="{{ asset('image/logo.png') }}" alt="Eh Di traditional Clothing Shop Logo" class="h-28 w-28 object-contain rounded-full">
            </div>
        </div>

        <!-- Tagline / Small title -->
        <p class="text-xs uppercase tracking-widest font-semibold text-amber-600 dark:text-amber-400 mb-2">POS & Inventory Management System</p>

        <!-- Prominent H1 -->
        <h1 class="text-3xl font-extrabold tracking-tight text-stone-900 dark:text-white mb-4">
            Eh Di traditional Clothing Shop
        </h1>

        <!-- Divider with a traditional style bullet -->
        <div class="flex items-center justify-center gap-2 mb-6">
            <span class="h-px w-12 bg-stone-300 dark:bg-stone-700"></span>
            <span class="text-stone-400 dark:text-stone-600 text-xs">◆ ❖ ◆</span>
            <span class="h-px w-12 bg-stone-300 dark:bg-stone-700"></span>
        </div>

        <p class="text-sm text-stone-600 dark:text-stone-300 mb-8 leading-relaxed max-w-sm mx-auto">
            Experience the elegance of traditional attire with our seamless point-of-sale and smart stock management solution.
        </p>

        <!-- Auth Checks -->
        <div class="space-y-3">
            @guest
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="block w-full py-3 px-6 text-sm font-semibold text-white bg-stone-900 hover:bg-stone-800 dark:bg-stone-100 dark:text-stone-900 dark:hover:bg-white rounded-xl shadow-md transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0">
                        Log in
                    </a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="block w-full py-3 px-6 text-sm font-semibold text-stone-900 bg-white hover:bg-stone-50 border border-stone-200 dark:bg-stone-800 dark:text-white dark:hover:bg-stone-700 dark:border-stone-700 rounded-xl shadow-md transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0">
                        Register
                    </a>
                @endif
            @endguest

            @auth
                <a href="{{ route('dashboard') }}" class="block w-full py-3 px-6 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl shadow-md transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0">
                    Go to Dashboard
                </a>
            @endauth
        </div>

        <!-- Footer Note -->
        <p class="mt-8 text-xs text-stone-400 dark:text-stone-500">
            &copy; {{ date('Y') }} Eh Di traditional Clothing Shop. All rights reserved.
        </p>
    </div>
</body>
</html>
