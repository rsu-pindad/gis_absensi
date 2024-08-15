@props([
    'sortColumn',
    'items',
    'columns',
    'page',
    'perPage',
    'sortDirection',
    'isModalEdit' => false,
    'routeEdit' => null,
    'routeView' => null,
    'title',
    'componentEditName' => null,
])

<div class="card dark:text-neutral-100">
    <div class="card-header">
        <h3 class="card-title">{{ $title }}</h3>
    </div>
    <div class="card-body border-bottom py-3">
        <div class="text-muted">
            Menampilkan <span class="badge badge-secondary" wire:model.live="perPage" id="perPage"> {{ $perPage }}</span> data
        </div>
        <div class="ms-auto text-muted">
            <div class="py-4">
                <x-input-label for="search" :value="__('Cari:')" />
                <x-text-input wire:model.live.throttle.500ms="search" type="text" aria-label="cari-data" placeholder="cari data.." class="md:w-full" />
            </div>
        </div>
        <div class="rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto rounded-t-lg">
                <table class="min-w-full divide-y-2 divide-gray-200 bg-neutral-100 text-sm dark:divide-gray-700 dark:bg-neutral-900 datatable">
                    <x-table.table-head :columns="$columns" :sortColumn="$sortColumn" :sortDirection="$sortDirection" />
                    <x-table.table-body :isModalEdit="$isModalEdit" :routeEdit="$routeEdit" :routeView="$routeView" :items="$items" :columns="$columns" :page="$page"
                        :perPage="$perPage" :componentEditName="$componentEditName" />
                </table>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex align-items-center">
        <div class="d-flex">
            <x-input-label class="block text-sm font-medium text-gray-900" for="perPage" :value="__('per halaman')" />
            <select wire:model.live="perPage" id="perPage" class="border-gray-300 dark:border-gray-700 dark:bg-neutral-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm sm:text-sm">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        {{ $items->links(data: ['scrollTo' => false]) }}
       
    </div>
</div>