@props([
'disabled' => false,
'hidden' => false,
'items' => null,
'nameValue' => 'pilih',
'custom' => false,
])

<select {{ $attributes->merge(['class' => 'w-full bg-neutral-100 border-gray-300 dark:border-gray-700 dark:bg-neutral-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm sm:text-sm']) }}>
    
    @if($custom === false)
    <option hidden>Pilih {{$nameValue}}</option>
    @else
    {{$customOption ?? ''}}
    @endif
    @foreach($items as $item => $val)
    <option value="{{$val->id}}">{{$val->$nameValue}}</option>
    @endforeach
</select>
