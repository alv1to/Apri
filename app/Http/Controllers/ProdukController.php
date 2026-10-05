<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('kode', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $produk = $query->orderBy('nama')->paginate(10)->withQueryString();

        return view('produk.index', compact('produk'));
    }

    public function create()
    {
        return view('produk.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100',
            'kode'      => 'required|string|max:20|unique:produk,kode',
            'kategori'  => ['required', Rule::in(config('produk.kategori'))],
            'harga'     => 'required|numeric|min:0',
            'stok'      => 'required|integer|min:0',
            'satuan'    => 'required|string|max:20',
            'deskripsi' => 'nullable|string',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'promo_percent' => 'nullable|integer|min:0|max:100',
            'promo_active'  => 'nullable|boolean',
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $validated['promo_percent'] = (int) $request->input('promo_percent', 0);
        $validated['promo_active']  = $request->boolean('promo_active');

        Produk::create($validated);

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Produk $produk)
    {
        return view('produk.show', compact('produk'));
    }

    public function edit(Produk $produk)
    {
        return view('produk.edit', compact('produk'));
    }

    public function update(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100',
            'kode'      => 'required|string|max:20|unique:produk,kode,' . $produk->id,
            'kategori'  => ['required', Rule::in(config('produk.kategori'))],
            'harga'     => 'required|numeric|min:0',
            'stok'      => 'required|integer|min:0',
            'satuan'    => 'required|string|max:20',
            'deskripsi' => 'nullable|string',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'promo_percent' => 'nullable|integer|min:0|max:100',
            'promo_active'  => 'nullable|boolean',
        ]);

        if ($request->hasFile('gambar')) {
            if ($produk->gambar) {
                \Storage::disk('public')->delete($produk->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $validated['promo_percent'] = (int) $request->input('promo_percent', 0);
        $validated['promo_active']  = $request->boolean('promo_active');

        $produk->update($validated);

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        // Nonaktifkan saja — data historis transaksi tetap terjaga
        $produk->update(['is_active' => false]);

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil dinonaktifkan.');
    }
}
