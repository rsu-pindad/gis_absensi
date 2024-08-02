<thead class="ltr:text-left rtl:text-right">
    <tr>
        <th class="w-1">No.
        </th>
        @foreach($columns as $key => $value)
        @if($value['isData'])
        <th wire:click="doSort('{{ $value['column'] }}')"
        class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
            <x-table.datatable-column :sortColumn="$sortColumn" :sortDirection="$sortDirection" columnName="{{ $value['label'] }}" />
        </th>
        @else
        <th>{{ $value['label'] }}</th>
        @endif
        @endforeach
    </tr>
</thead>
