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
    'title'
])

<div class="card dark:text-gray-100">
    <div class="card-header">
        <h3 class="card-title">{{ $title }}</h3>
    </div>
    <div class="card-body border-bottom py-3">
        <div class="text-muted">
            Menampilkan <span class="badge badge-secondary" wire:model.live="perPage" id="perPage"> {{ $perPage }}</span> data
        </div>
        <div class="ms-auto text-muted">
            <x-input-label for="search" :value="__('Cari:')" />
            <x-text-input wire:model.live.debounce.300ms="search" type="text" aria-label="Cari GIS" />
        </div>
        <div class="rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto rounded-t-lg">
                <table class="min-w-full divide-y-2 divide-gray-200 bg-white text-sm dark:divide-gray-700 dark:bg-gray-900 datatable">
                    <x-table.table-head :columns="$columns" :sortColumn="$sortColumn" :sortDirection="$sortDirection" />
                    <x-table.table-body :isModalEdit="$isModalEdit" :routeEdit="$routeEdit" :routeView="$routeView" :items="$items" :columns="$columns" :page="$page"
                        :perPage="$perPage" />
                </table>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex align-items-center">
        <div class="d-flex">
            <label for="block text-sm font-medium text-gray-900">Per Page</label>
            <select class="mt-1.5 rounded-lg border-gray-300 text-gray-700 sm:text-sm" wire:model.live="perPage" id="perPage">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        {{ $items->links() }}
       
    </div>
</div>