@props(['value'])

<span {{$attributes->merge(['class' => 'block mb-2 text-sm text-gray-500 dark:text-neutral-500'])}}>
    {{$value}}
</span>