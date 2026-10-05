<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 rounded-2xl bg-kopi text-white font-semibold text-sm hover:bg-kopi-dark focus:outline-none focus:ring-2 focus:ring-kopi focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
