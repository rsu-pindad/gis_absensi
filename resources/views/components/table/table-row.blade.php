@props(['item', 'key', 'page', 'perPage', 'columns', 'isModalEdit' => false,  'routeEdit'=> null, 'routeView'=> null, 'componentEditName' => null])

<tr wire:key="{{ $item->id . $page }}">
    <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">{{ ++$key + $perPage * ($page - 1) }}.</td>
    @foreach ($columns as $column)
    <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
        @if ($column['isData'])
        {!! $this->customFormat($column['column'], $column['hasRelation'] ? $item->{$column['column']}->{$column['columnRelation']} : $item->{$column['column']}) !!}
        @elseif ($column['column'] === 'action')
        <div class="inline-flex rounded-lg border border-gray-50 bg-neutral-300 dark:border-gray-800 dark:bg-neutral-800">
            @if(($routeView))
            <a href="{{ route($routeView, $item->id) }}" class="flex btn px-1 py-1 rounded-md text-blue-400">
                <x-heroicon-s-eye class="w-6 h-8 p-1 text-blue-500" />
            </a>
            @endif
            @if($isModalEdit)
            {{-- <button wire:loading.attr="disabled" wire:click="edit({{ $item->id }})" class="flex btn px-1 py-1 rounded-md text-yellow-400">
                <x-heroicon-s-pencil class="w-6 h-8 p-1 text-yellow-500" />
            </button> --}}
            {{-- <button wire:click="$dispatch('openModal', { component: 'absen.absensi-edit' })" class="flex btn px-1 py-1 rounded-md text-yellow-400"> --}}
            <button wire:click="$dispatch('openModal', { component: {{$componentEditName}} , arguments: {id: {{$item->id}} }})" class="inline-flex items-center gap-1 rounded-md px-3 py-1 text-sm text-white bg-neutral-400 dark:bg-neutral-700 hover:bg-neutral-400 focus:relative dark:text-neutral-50 dark:hover:text-neutral-100 m-1">
                <x-heroicon-s-pencil class="w-6 h-auto p-1 text-yellow-500" />
                edit
            </button>
            @else
            <a href="{{ route($routeEdit, $item->id) }}" class="flex btn px-1 py-1 rounded-md text-yellow-400">
                <x-heroicon-s-pencil class="w-6 h-auto p-1 text-yellow-500" />
            </a>
            @endif
            {{-- <button wire:loading.attr="disabled" wire:click="deleteConfirmation({{ $item->id }})" class="inline-flex items-center gap-1 rounded-md px-2 text-sm text-gray-500 bg-gray-700 hover:bg-gray-400 focus:relative dark:text-gray-50 dark:hover:text-gray-100 m-1">
                <x-heroicon-s-trash class="w-6 h-8 p-1 text-red-500" />
                hapus
            </button> --}}
            <button wire:loading.attr="disabled" wire:click="$dispatch('delete-confirmation', {id: {{ $item->id }}})" class="inline-flex items-center gap-1 rounded-md px-3 py-1 text-sm text-white bg-neutral-400 dark:bg-neutral-700 hover:bg-neutral-400 focus:relative dark:text-neutral-50 dark:hover:text-neutral-100 m-1">
                <x-heroicon-s-trash class="w-6 h-auto p-1 text-red-500" />
                hapus
            </button>
        </div>
        @endif
    </td>
    @endforeach
</tr>