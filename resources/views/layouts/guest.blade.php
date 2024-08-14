<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('/apple-touch-icon-180x180.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('/favicon-16x16.png')}}">
    <link rel="manifest" href="{{asset('/site.webmanifest')}}">
    <link rel="mask-icon" href="{{asset('/safari-pinned-tab.svg')}}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#e1ffde">
    <meta name="theme-color" content="#e1ffde">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-dvh bg-neutral-50 dark:bg-neutral-900 antialiased">
    <header class="max-h-[20%] relative flex flex-wrap sm:justify-start sm:flex-nowrap w-full bg-neutral-50 text-sm py-3 dark:bg-neutral-900 border-collapse border-b-2 dark:border-neutral-700">
        <nav class="max-w-[85rem] w-full mx-auto px-4 sm:flex sm:items-center sm:justify-between">
            <div class="flex items-center justify-between">
                <a class="flex-none text-xl font-semibold dark:text-white focus:outline-none focus:opacity-80" href="{{route('dashboard')}}" aria-label="Brand" wire:navigate>
                    <x-ionicon-globe-outline class="text-neutral-700 dark:text-neutral-200 w-6 h-auto" stroke="currentColor" stroke-width="2"/>
                </a>
                <div class="sm:hidden">
                    <x-button-switch />
                </div>
            </div>
            <div id="hs-navbar-example" class="hidden hs-collapse overflow-hidden transition-all duration-300 basis-full grow sm:block" aria-labelledby="hs-navbar-example-collapse">
                <div class="flex flex-col gap-5 mt-5 sm:flex-row sm:items-center sm:justify-end sm:mt-0 sm:ps-5">
                    <x-button-switch />
                </div>
            </div>
        </nav>
    </header>
    <main class="max-h-[80%]">
        <section class="container mx-auto px-6">
            {{$slot}}
        </section>
    </main>
    @stack('modulejs')
    <x-livewire-alert::scripts />
    <script type="module">
        document.addEventListener("livewire:navigated", ()=> {
            window.HSStaticMethods.autoInit();
        });
    </script>
</body>
</html>
