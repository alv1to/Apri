@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-kopi-dark']) }}>
    {{ $value ?? $slot }}
</label>
