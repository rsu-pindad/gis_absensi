@props(['items', 'columns', 'page', 'perPage', 'isModalEdit' => false, 'routeEdit'=> null, 'routeView' => null, 'componentEditName' => null])

<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
    @if ($items->isEmpty())
        <tr>
            <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white" :colspan="{{ count($columns) + 1 }}">Tidak ada data.</td>
        </tr>
    @endif
    
    @foreach($items as $key => $item)
    <x-table.table-row :routeView="$routeView" :routeEdit="$routeEdit" :isModalEdit="$isModalEdit" :item="$item" :columns="$columns" :key="$key" :page="$page" :perPage="$perPage" :componentEditName="$componentEditName" />
    @endforeach

</tbody>