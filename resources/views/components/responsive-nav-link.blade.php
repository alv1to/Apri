@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[#08783F] text-start text-base font-medium text-[#064C32] bg-[#EEF8ED] focus:outline-none focus:text-[#064C32] focus:bg-[#E5F6E1] focus:border-[#08783F] transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-[#5F6D63] hover:text-[#064C32] hover:bg-[#F5F7F1] hover:border-[#B9E95C] focus:outline-none focus:text-[#064C32] focus:bg-[#F5F7F1] focus:border-[#08783F] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
