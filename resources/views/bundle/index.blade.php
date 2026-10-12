<x-app-layout>
    <x-slot name="title">Generate Bundle Rekomendasi</x-slot>

    <div class="space-y-6">

        {{-- Form Generate --}}
        <div class="card-kopi p-6">
            <h3 class="font-semibold text-[#1F2937] mb-1">Parameter Analisis Apriori</h3>
            <p class="text-sm text-gray-500 mb-5">
                Sistem menganalisa pola pembelian dari transaksi untuk menemukan kombinasi produk yang sering dibeli bersamaan.
            </p>

            <form method="POST" action="{{ route('bundle.generate') }}" id="form-generate">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="dari"
                               value="{{ old('dari', now()->subDays(30)->toDateString()) }}"
                               class="input-kopi">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                        <input type="date" name="sampai"
                               value="{{ old('sampai', now()->toDateString()) }}"
                               class="input-kopi">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Min. Support (%)
                            <span class="text-gray-400 font-normal" title="% transaksi yang memuat kombinasi ini">ⓘ</span>
                        </label>
                        <input type="number" name="min_support" value="{{ old('min_support', 5) }}"
                               min="1" max="100"
                               class="input-kopi">
                        <p class="text-xs text-gray-400 mt-1">Rekomendasi: 5–15%</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Min. Confidence (%)
                            <span class="text-gray-400 font-normal" title="Seberapa sering B dibeli jika A dibeli">ⓘ</span>
                        </label>
                        <input type="number" name="min_confidence" value="{{ old('min_confidence', 30) }}"
                               min="1" max="100"
                               class="input-kopi">
                        <p class="text-xs text-gray-400 mt-1">Rekomendasi: 30–60%</p>
                    </div>
                </div>

                <button type="submit" id="btn-generate"
                        class="btn-kopi flex items-center gap-2 justify-center">
                    <span id="btn-label">Jalankan Analisis Apriori</span>
                    <svg id="spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                </button>
            </form>
        </div>

# Deskripsi Halaman Aplikasi Association Rule – Algoritma Apriori

Halaman ini adalah aplikasi web berbasis PHP dan digunakan untuk menjalankan serta menampilkan hasil **proses data mining dengan algoritma Apriori**. Data yang dianalisis adalah transaksi penjualan tanaman hias (varietas *Alocasia*, *Philodendron*, *Monstera*, *Anthurium*). Hasil yang diperoleh berupa pola pembelian yang sering muncul bersamaan.

---

## 1. Pembentukan Itemset 1

Tabel **Itemset 1** menampilkan 12 item beserta jumlah kemunculan dan nilai *support*. Item yang nilai supportnya memenuhi batas minimum diberi label **Lolos**, sedangkan sisanya **Tidak Lolos**.

| No | Item | Jumlah | Support (%) | Keterangan |
|----|------|--------|-------------|------------|
| 1 | Alocasia Dragon Silver | 57 | 50,00 | Lolos |
| 2 | Alocasia Black Velvet | 39 | 34,21 | Lolos |
| 3 | Alocasia Amazonica | 65 | 57,02 | Lolos |
| 4 | Alocasia Golden Bone | 3 | 2,63 | Tidak Lolos |
| 5 | Alocasia Reginae Miri | 30 | 26,32 | Lolos |
| 6 | Philodendron Giganteum Marble Variage | 2 | 1,75 | Tidak Lolos |
| 7 | Alocasia Melo | 2 | 1,75 | Tidak Lolos |
| 8 | Alocasia Watsoniana | 2 | 1,75 | Tidak Lolos |
| 9 | Alocasia Black Maharani | 1 | 0,88 | Tidak Lolos |
| 10 | Monstera Adansonii Variegata (Lokal) | 1 | 0,88 | Tidak Lolos |
| 11 | Anthurium Jemani | 1 | 0,88 | Tidak Lolos |
| 12 | Philodendron White Princess | 1 | 0,88 | Tidak Lolos |

Dari tabel tersebut, ada **4 item yang lolos** dan masuk ke tabel **Itemset 1 yang lolos**: Dragon Silver, Black Velvet, Amazonica, dan Reginae Miri.

---

## 2. Pembentukan Itemset 2

Item yang lolos dikombinasikan menjadi pasangan (2 item).

| No | Item 1 | Item 2 | Jumlah | Support (%) | Keterangan |
|----|--------|--------|--------|-------------|------------|
| 1 | Alocasia Dragon Silver | Alocasia Black Velvet | 26 | 22,81 | Lolos |
| 2 | Alocasia Dragon Silver | Alocasia Amazonica | 25 | 21,93 | Lolos |
| 3 | Alocasia Dragon Silver | Alocasia Reginae Miri | 3 | 2,63 | Tidak Lolos |
| 4 | Alocasia Black Velvet | Alocasia Amazonica | 20 | 17,54 | Lolos |
| 5 | Alocasia Black Velvet | Alocasia Reginae Miri | 0 | 0,00 | Tidak Lolos |
| 6 | Alocasia Amazonica | Alocasia Reginae Miri | 23 | 20,18 | Lolos |

Terdapat **4 pasangan** yang lolos dan ditampilkan pada tabel **Itemset 2 yang lolos**.

---

## 3. Pembentukan Itemset 3

Pasangan yang lolos dikembangkan menjadi kombinasi 3 item.

| No | Item 1 | Item 2 | Item 3 | Jumlah | Support (%) | Keterangan |
|----|--------|--------|--------|--------|-------------|------------|
| 1 | Alocasia Dragon Silver | Alocasia Black Velvet | Alocasia Amazonica | 7 | 6,14 | Tidak Lolos |
| 2 | Alocasia Dragon Silver | Alocasia Amazonica | Alocasia Reginae Miri | 1 | 0,88 | Tidak Lolos |
| 3 | Alocasia Dragon Silver | Alocasia Black Velvet | Alocasia Reginae Miri | 0 | 0,00 | Tidak Lolos |
| 4 | Alocasia Amazonica | Alocasia Black Velvet | Alocasia Reginae Miri | 0 | 0,00 | Tidak Lolos |

Semua kombinasi **tidak lolos**, sehingga tabel **Itemset 3 yang lolos** kosong. Aplikasi lalu menampilkan notifikasi **"Proses mining selesai"**, dan proses berhenti pada itemset 3.

---

## 4. Perhitungan Confidence

### Confidence dari Itemset 3

Tabel kosong, karena tidak ada itemset 3 yang lolos.

### Confidence dari Itemset 2

| No | X ⇒ Y | Support X ∪ Y | Support X | Confidence (%) | Keterangan |
|----|-------|---------------|-----------|----------------|------------|
| 1 | Dragon Silver ⇒ Black Velvet | 22,81 | 50,00 | 45,61 | Lolos |
| 2 | Black Velvet ⇒ Dragon Silver | 22,81 | 34,21 | 66,67 | Lolos |
| 3 | Dragon Silver ⇒ Amazonica | 21,93 | 50,00 | 43,86 | Lolos |
| 4 | Amazonica ⇒ Dragon Silver | 21,93 | 57,02 | 38,46 | Tidak Lolos |
| 5 | Black Velvet ⇒ Amazonica | 17,54 | 34,21 | 51,28 | Lolos |
| 6 | Amazonica ⇒ Black Velvet | 17,54 | 57,02 | 30,77 | Tidak Lolos |
| … | … | … | … | … | … |
| 8 | Reginae Miri ⇒ Amazonica | 20,18 | 26,32 | 76,67 | Lolos |

Confidence dihitung dengan rumus:

$$\text{Confidence}(X \Rightarrow Y) = \frac{\text{Support}(X \cup Y)}{\text{Support}(X)}$$

---

## 5. Rule Asosiasi yang Terbentuk

Rule yang lolos kemudian diuji dengan **nilai uji lift** untuk menentukan jenis korelasinya.

| No | X ⇒ Y | Confidence (%) | Nilai Uji Lift | Korelasi Rule |
|----|-------|----------------|----------------|---------------|
| 1 | Dragon Silver ⇒ Black Velvet | 45,61 | 1,33 | Korelasi positif |
| 2 | Black Velvet ⇒ Dragon Silver | 66,67 | 1,33 | Korelasi positif |
| 3 | Dragon Silver ⇒ Amazonica | 43,86 | 0,77 | Korelasi negatif |
| 4 | Black Velvet ⇒ Amazonica | 51,28 | 0,90 | Korelasi negatif |
| 5 | Reginae Miri ⇒ Amazonica | 76,67 | 1,34 | Korelasi positif |

Interpretasi nilai lift:

- **Lift > 1** → korelasi positif (item saling mendorong pembelian).
- **Lift < 1** → korelasi negatif (item cenderung tidak dibeli bersamaan).

Di bawah tabel ini terdapat juga **Grafik Rule Asosiasi yang terbentuk** (*Multi Line*).

---

## 6. Hasil Analisis

Pada bagian ini, setiap rule diterjemahkan ke dalam kalimat yang mudah dipahami, dilengkapi tombol **Print** untuk mencetak hasil:

1. Jika konsumen membeli **Alocasia Dragon Silver**, maka konsumen juga akan membeli **Alocasia Black Velvet**.
2. Jika konsumen membeli **Alocasia Black Velvet**, maka konsumen juga akan membeli **Alocasia Dragon Silver**.
3. Jika konsumen membeli **Alocasia Dragon Silver**, maka konsumen juga akan membeli **Alocasia Amazonica**.
4. Jika konsumen membeli **Alocasia Black Velvet**, maka konsumen juga akan membeli **Alocasia Amazonica**.
5. Jika konsumen membeli **Alocasia Reginae Miri**, maka konsumen juga akan membeli **Alocasia Amazonica**.

Halaman ini juga memuat bagian **Perhitungan Itemset 1** (URL `view_rule&id_process=6`) yang menampilkan hasil perhitungan dengan data berbeda (misalnya *Alocasia Dragon Silver (Bibit)* dengan jumlah 3 dan support 60,00).

---

## Ringkasan Alur Proses

```
Itemset 1 → Itemset 1 lolos → Itemset 2 → Itemset 2 lolos
   → Itemset 3 (tidak ada yang lolos) → Proses mining selesai
   → Hitung Confidence → Uji Lift → Rule Asosiasi → Hasil Analisis
```

## Catatan Penting

- **Rule terkuat**: *Alocasia Reginae Miri ⇒ Alocasia Amazonica* (confidence 76,67% dan lift 1,34).
- **Rule yang kurang layak** untuk dijadikan strategi penjualan: yang melibatkan Amazonica sebagai konsekuen dari Dragon Silver dan Black Velvet, karena lift < 1 (korelasi negatif), meskipun confidence-nya cukup tinggi.
- Di halaman "Hasil Analisis", kelima rule ditampilkan bersama, termasuk dua rule berkorelasi negatif. Sebaiknya hal ini dijelaskan atau difilter dalam deskripsi Anda.
</x-app-layout>