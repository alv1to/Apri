<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', Rule::in(Produk::KATEGORI)],
            'status' => ['nullable', Rule::in(['tersedia', 'tidak_tersedia', 'aktif', 'nonaktif'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = Produk::query();

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('kode', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (! empty($filters['kategori'])) {
            $query->where('kategori', $filters['kategori']);
        }

        if (! empty($filters['status'])) {
            $query->where('is_active', in_array($filters['status'], ['tersedia', 'aktif'], true));
        }

        $produk = $query->orderBy('nama')->paginate(10)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('produk._results', compact('produk'))->render(),
            ]);
        }

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
            'kategori'  => ['required', Rule::in(config('produk.kategori'))],
            'harga'     => 'required|numeric|min:0',
            'stok'      => 'required|integer|min:0',
            'satuan'    => 'required|string|max:20',
            'is_active' => 'required|boolean',
            'deskripsi' => 'nullable|string',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'promo_percent' => 'nullable|integer|min:0|max:100',
            'promo_active'  => 'nullable|boolean',
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        $validated['promo_percent'] = (int) $request->input('promo_percent', 0);
        $validated['promo_active']  = $request->boolean('promo_active');

        DB::transaction(function () use ($validated) {
            DB::table('produk')->orderByDesc('id')->lockForUpdate()->first();
            Produk::create($validated);
        });

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
            'kategori'  => ['required', Rule::in(config('produk.kategori'))],
            'harga'     => 'required|numeric|min:0',
            'stok'      => 'required|integer|min:0',
            'satuan'    => 'required|string|max:20',
            'is_active' => 'required|boolean',
            'deskripsi' => 'nullable|string',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'promo_percent' => 'nullable|integer|min:0|max:100',
            'promo_active'  => 'nullable|boolean',
        ]);

        $gambarLama = $produk->gambar;
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        $validated['promo_percent'] = (int) $request->input('promo_percent', 0);
        $validated['promo_active']  = $request->boolean('promo_active');

        $produk->update($validated);

        if (isset($validated['gambar']) && $gambarLama) {
            Storage::disk('public')->delete($gambarLama);
        }

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        // Nonaktifkan saja — data historis transaksi tetap terjaga
        $produk->update(['is_active' => false]);

        return redirect()->route('produk.index')
            ->with('success', 'Produk dinonaktifkan dan tidak tersedia di POS.');
    }

    public function updateAvailability(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $produk->update(['is_active' => $request->boolean('is_active')]);

        return back()->with('success', $produk->status_ketersediaan . '.');
    }
}
