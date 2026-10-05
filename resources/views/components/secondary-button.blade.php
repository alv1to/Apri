<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2 rounded-2xl border border-[#D3D3D3] bg-white text-kopi-dark font-semibold text-sm hover:bg-[#F8F5EF] focus:outline-none focus:ring-2 focus:ring-kopi/30 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
