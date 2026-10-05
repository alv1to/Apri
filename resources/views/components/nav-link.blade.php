@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-[#08783F] text-sm font-medium leading-5 text-[#064C32] focus:outline-none focus:border-[#064C32] transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-[#5F6D63] hover:text-[#064C32] hover:border-[#A9D68B] focus:outline-none focus:text-[#064C32] focus:border-[#08783F] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
