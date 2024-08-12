<div wire:ignore {{$attributes->merge(['class' => ''])}} >
    <button type="button" class="hs-dark-mode-active:hidden block hs-dark-mode font-medium text-gray-800 rounded-full hover:bg-gray-200 focus:outline-none focus:bg-gray-200 dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" data-hs-theme-click-value="dark">
        <span class="group inline-flex shrink-0 justify-center items-center size-9">
            <x-ionicon-sunny-outline class="w-4/5 h-auto"/>
        </span>
    </button>
    <button type="button" class="hs-dark-mode-active:block hidden hs-dark-mode font-medium text-gray-800 rounded-full hover:bg-gray-200 focus:outline-none focus:bg-gray-200 dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" data-hs-theme-click-value="light">
        <span class="group inline-flex shrink-0 justify-center items-center size-9">
            <x-ionicon-moon-sharp class="w-4/5 h-auto"/>
        </span>
    </button>
</div>