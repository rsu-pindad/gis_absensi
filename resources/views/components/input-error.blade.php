@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'space-y-3 text-sm']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex gap-x-3">
                <x-ionicon-warning-outline class="w-5 h-auto" style="color: #ff0000" />
                <span class="text-red-400">
                    {{ $message }}
                </span>
            </li>
        @endforeach
    </ul>
@endif
