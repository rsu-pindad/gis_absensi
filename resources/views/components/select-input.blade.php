@props([
'disabled' => false,
'hidden' => false,
'items',
'nameValue' => '',
])

<select {!! $attributes->merge(['class' => 'mt-1 block w-full rounded-lg border-gray-300 text-gray-700 sm:text-sm']) !!}>
    <option hidden>Pilih {{$nameValue}}</option>
    @foreach($items as $item => $val)
    <option value="{{$val->id}}">{{$val->$nameValue}}</option>
    @endforeach
</select>
