<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dinas Absen Scan Barcode') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-neutral-400 dark:bg-neutral-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:dinas.dinas-scan-form />
                </div>
            </div>
        </div>
    </div>

    <x-banner-message>
        <x-slot:pesan>
            Scan barcode yang telah didapat dari petugas untuk di scan !
        </x-slot:pesan>
    </x-banner-message>
</x-app-layout>
