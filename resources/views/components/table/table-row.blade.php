@props(['item', 'key', 'page', 'perPage', 'columns', 'isModalEdit' => false,  'routeEdit'=> null, 'routeView'=> null])

<tr wire:key="{{ $item->id . $page }}">
    <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">{{ ++$key + $perPage * ($page - 1) }}.</td>
    @foreach ($columns as $column)
    <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
        @if ($column['isData'])
        {!! $this->customFormat($column['column'], $column['hasRelation'] ? $item->{$column['column']}->{$column['columnRelation']} : $item->{$column['column']}) !!}
        @elseif ($column['column'] === 'action')
        <div class="flex gap-1 items-center justify-center">
            @if(($routeView))
            <a href="{{ route($routeView, $item->id) }}" class="flex btn px-1 py-1 rounded-md text-blue-400">
                <x-heroicon-s-eye class="w-6 h-8 p-1 text-blue-500" />
            </a>
            @endif
            @if($isModalEdit)
            <button wire:loading.attr="disabled" wire:click="edit({{ $item->id }})" class="flex btn px-1 py-1 rounded-md text-yellow-400">
                <x-heroicon-s-pencil class="w-6 h-8 p-1 text-yellow-500" />
            </button>
            @else
            <a href="{{ route($routeEdit, $item->id) }}" class="flex btn px-1 py-1 rounded-md text-yellow-400">
                <x-heroicon-s-pencil class="w-6 h-8 p-1 text-yellow-500" />
            </a>
            @endif
            <button wire:loading.attr="disabled" wire:click="deleteConfirmation({{ $item->id }})" class="flex btn px-1 py-1 text-red-400">
                <x-heroicon-s-trash class="w-6 h-8 p-1 text-red-500" />
            </button>
        </div>
        @endif
    </td>
    @endforeach
</tr>