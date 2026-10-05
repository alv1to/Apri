{{-- Nama --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-kopi-dark mb-1">Nama Produk <span class="text-red-500">*</span></label>
    <input type="text" name="nama" value="{{ old('nama', $produk->nama ?? '') }}"
           class="input-kopi @error('nama') border-red-400 @enderror"
           >
    @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

{{-- Kode --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-kopi-dark mb-1">Kode Produk <span class="text-red-500">*</span></label>
    <input type="text" name="kode" value="{{ old('kode', $produk->kode ?? '') }}"
           placeholder="MNM001"
           class="input-kopi @error('kode') border-red-400 @enderror">
    @error('kode')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

{{-- Kategori & Satuan --}}
<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-kopi-dark mb-1">Kategori <span class="text-red-500">*</span></label>
        <select name="kategori"
                class="select-kopi @error('kategori') border-red-400 @enderror">
            @foreach(config('produk.kategori') as $kat)
            <option value="{{ $kat }}" {{ old('kategori', $produk->kategori ?? '') == $kat ? 'selected' : '' }}>
                {{ ucfirst($kat) }}
            </option>
            @endforeach
        </select>
        @error('kategori')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-kopi-dark mb-1">Satuan</label>
        <input type="text" name="satuan" value="{{ old('satuan', $produk->satuan ?? 'pcs') }}"
               class="input-kopi">
    </div>
</div>

{{-- Harga & Stok --}}
<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-kopi-dark mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
        <input type="number" name="harga" value="{{ old('harga', $produk->harga ?? '') }}"
               min="0" step="500"
               class="input-kopi @error('harga') border-red-400 @enderror">
        @error('harga')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-kopi-dark mb-1">Stok <span class="text-red-500">*</span></label>
        <input type="number" name="stok" value="{{ old('stok', $produk->stok ?? 0) }}"
               min="0"
               class="input-kopi @error('stok') border-red-400 @enderror">
        @error('stok')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>

{{-- Deskripsi --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-kopi-dark mb-1">Deskripsi (opsional)</label>
    <textarea name="deskripsi" rows="3"
              class="textarea-kopi">{{ old('deskripsi', $produk->deskripsi ?? '') }}</textarea>
</div>

{{-- Gambar --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar (opsional)</label>
    @if(!empty($produk->gambar))
    <img src="{{ asset('storage/' . $produk->gambar) }}" class="w-20 h-20 object-cover rounded-lg mb-2">
    @endif
    <input type="file" name="gambar" accept="image/*" class="w-full text-sm text-gray-500">
    @error('gambar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

{{-- Status aktif --}}
<div class="mb-4 flex items-center gap-2">
    <input type="checkbox" name="is_active" id="is_active" value="1"
           {{ old('is_active', $produk->is_active ?? true) ? 'checked' : '' }}
           class="rounded border-gray-300 text-[#08783F]">
    <label for="is_active" class="text-sm text-gray-700">Produk aktif (tampil di kasir POS)</label>
</div>

{{-- Promo: persentase diskon dan aktif --}}
<div class="grid grid-cols-2 gap-4 mb-4 items-center">
    <div>
        <label class="block text-sm font-medium text-kopi-dark mb-1">Promo (persen)</label>
        <input type="number" name="promo_percent" min="0" max="100"
               value="{{ old('promo_percent', $produk->promo_percent ?? 0) }}"
               class="input-kopi">
        <p class="text-xs text-gray-400 mt-1">Isi 0 jika tidak ada promosi.</p>
    </div>
    <div class="flex items-center gap-2">
        <input type="checkbox" name="promo_active" id="promo_active" value="1"
               {{ old('promo_active', $produk->promo_active ?? false) ? 'checked' : '' }}
               class="rounded border-gray-300 text-[#08783F]">
        <label for="promo_active" class="text-sm text-gray-700">Aktifkan promo</label>
    </div>
</div>
