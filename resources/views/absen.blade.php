<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Absen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="grid md:grid-cols-2 grid-cols-1 gap-x-16 gap-y-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:absen.absensi-form />
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:absen.absensi-tabel />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
