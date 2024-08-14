<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use Livewire\Attributes\Renderless;

new 
#[Renderless]
class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<header wire:ignore class="sticky top-3 inset-x-0 flex flex-wrap md:justify-start md:flex-nowrap z-50 w-full before:absolute before:inset-0 before:max-w-[66rem] before:mx-2 before:lg:mx-auto before:rounded-[26px] before:bg-neutral-800/30 before:backdrop-blur-md">
    <nav class="relative max-w-[66rem] w-full py-2.5 ps-5 pe-2 md:flex md:items-center md:justify-between md:py-0 mx-2 lg:mx-auto">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <a class="flex-none rounded-md text-xl inline-block font-semibold focus:outline-none focus:opacity-80" href="#" aria-label="Preline">
                <x-ionicon-home-outline class="text-neutral-700 dark:text-neutral-200 w-6 h-auto" stroke="currentColor" stroke-width="2"/>
            </a>
            <!-- End Logo -->

            <div class="md:hidden">
            <button type="button" class="hs-collapse-toggle size-8 flex justify-center items-center text-sm font-semibold rounded-full bg-neutral-800 text-white disabled:opacity-50 disabled:pointer-events-none" id="hs-navbar-floating-dark-collapse" aria-expanded="false" aria-controls="hs-navbar-floating-dark" aria-label="Toggle navigation" data-hs-collapse="#hs-navbar-floating-dark">
                <x-ionicon-menu-outline class="text-neutral-700 dark:text-neutral-200 w-6 h-auto" stroke="currentColor" stroke-width="2"/>
            </button>
            </div>
        </div>

        <!-- Collapse -->
        <div id="hs-navbar-floating-dark" class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow md:block" aria-labelledby="hs-navbar-floating-dark-collapse">
            <div class="flex flex-col md:flex-row md:items-center md:justify-end py-2 md:py-0 md:ps-7">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Beranda') }}
                </x-nav-link>
                <x-nav-link :href="route('gis')" :active="request()->routeIs('gis')" wire:navigate>
                    {{ __('GIS') }}
                </x-nav-link>
                <x-nav-link :href="route('absen')" :active="request()->routeIs('absen')" wire:navigate>
                    {{ __('Absen') }}
                </x-nav-link>
                <div class="hs-dropdown [--strategy:static] md:[--strategy:fixed] [--adaptive:none] [--is-collapse:true] md:[--is-collapse:false] p-3 ps-px sm:px-3 md:py-4">
                    <button id="hs-dropdown-floating-dark-user-absen" type="button" class="hs-dropdown-toggle flex items-center w-full text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:text-gray-700" aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                        User Absen
                        <x-ionicon-chevron-down-outline class="hs-dropdown-open:-rotate-180 md:hs-dropdown-open:rotate-0 duration-300 shrink-0 ms-auto md:ms-1 size-4" />
                    </button>
                    <div class="hs-dropdown-menu transition-[opacity,margin] duration-[0.1ms] md:duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 md:w-48 hidden z-10 bg-neutral-800 md:shadow-md rounded-lg before:absolute top-full before:-top-5 before:start-0 before:w-full before:h-5" role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-floating-dark-user-absen">
                        <div class="py-1 md:px-1 space-y-1 before:bg-neutral-800/30 before:backdrop-blur-md">
                            <a class="flex items-center gap-x-3.5 p-2 md:px-3 rounded-lg text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:text-gray-700" href="{{route('dinas')}}" wire:navigate>
                             Buat Absen Barcode
                            </a>
                            <a class="flex items-center gap-x-3.5 p-2 md:px-3 rounded-lg text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:text-gray-700" href="{{route('dinas-scan')}}">
                             Scan Absen Barcode
                            </a>
                        </div>
                    </div>
                </div>
                <div class="hs-dropdown [--strategy:static] md:[--strategy:fixed] [--adaptive:none] [--is-collapse:true] md:[--is-collapse:false] p-3 ps-px sm:px-3 md:py-4">
                    <button id="hs-dropdown-floating-dark" type="button" class="hs-dropdown-toggle flex items-center w-full text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:text-gray-700" aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                        Presensi
                        <x-ionicon-chevron-down-outline class="hs-dropdown-open:-rotate-180 md:hs-dropdown-open:rotate-0 duration-300 shrink-0 ms-auto md:ms-1 size-4" />
                    </button>
                    <div class="hs-dropdown-menu transition-[opacity,margin] duration-[0.1ms] md:duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 md:w-48 hidden z-10 bg-neutral-800 md:shadow-md rounded-lg before:absolute top-full before:-top-5 before:start-0 before:w-full before:h-5" role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-floating-dark">
                        <div class="py-1 md:px-1 space-y-1 before:bg-neutral-800/30 before:backdrop-blur-md">
                            <a class="flex items-center gap-x-3.5 p-2 md:px-3 rounded-lg text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:text-gray-700" href="{{route('presensi')}}" wire:navigate>
                                Scan Presensi
                            </a>
                            <a class="flex items-center gap-x-3.5 p-2 md:px-3 rounded-lg text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:text-gray-700" href="{{route('user-presensi')}}" wire:navigate>
                                User Presensi
                            </a>
                        </div>
                    </div>
                </div>
                <div class="hs-dropdown [--strategy:static] md:[--strategy:fixed] [--adaptive:none] [--is-collapse:true] md:[--is-collapse:false] p-3 ps-px sm:px-3 md:py-4">
                    <button id="hs-dropdown-floating-profile" type="button" class="hs-dropdown-toggle flex items-center w-full text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:text-gray-700" aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                        <div x-data="{{ json_encode(['npp' => auth()->user()->npp]) }}" x-text="npp" x-on:profile-updated.window="npp = $event.detail.npp"></div>
                        <x-ionicon-chevron-down-outline class="hs-dropdown-open:-rotate-180 md:hs-dropdown-open:rotate-0 duration-300 shrink-0 ms-auto md:ms-1 size-4" />
                    </button>
                    <div class="hs-dropdown-menu transition-[opacity,margin] duration-[0.1ms] md:duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 md:w-48 hidden z-10 bg-neutral-800 md:shadow-md rounded-lg before:absolute top-full before:-top-5 before:start-0 before:w-full before:h-5" role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-floating-dark">
                        <div class="py-1 md:px-1 space-y-1">
                            <a class="flex items-center gap-x-3.5 p-2 md:px-3 rounded-lg text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:text-gray-700" href="{{route('profile')}}" wire:navigate>
                                Profile
                            </a>
                            <a wire:click="logout" class="flex items-center gap-x-3.5 p-2 md:px-3 rounded-lg text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:text-gray-700" href="#" wire:navigate>
                                Keluar
                            </a>
                        </div>
                    </div>
                </div>
                <x-button-switch/>
        </div>
        <!-- End Collapse -->
    </nav>
</header>
