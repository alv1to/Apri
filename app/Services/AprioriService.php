<?php

namespace App\Services;

class AprioriService
{
    private array $transaksi;
    private int   $totalTransaksi;
    private float $minSupport;
    private float $minConfidence;

    /**
     * Jalankan Apriori dan kembalikan association rules.
     *
     * @param  array $transaksi      [ ['1','3','5'], ['2','3'], ... ]
     * @param  float $minSupport     0.0–1.0 (misal 0.05 = 5%)
     * @param  float $minConfidence  0.0–1.0 (misal 0.5  = 50%)
     */
    public function run(array $transaksi, float $minSupport = 0.05, float $minConfidence = 0.5): array
    {
        $this->transaksi      = $transaksi;
        $this->totalTransaksi = count($transaksi);
        $this->minSupport     = $minSupport;
        $this->minConfidence  = $minConfidence;

        if ($this->totalTransaksi === 0) {
            return [];
        }

        $frequentItemsets = $this->findFrequentItemsets();
        $rules            = $this->generateRules($frequentItemsets);

        usort($rules, fn($a, $b) => $b['lift'] <=> $a['lift']);

        return $rules;
    }

    // -----------------------------------------------------------------
    // LANGKAH 1: Temukan semua frequent itemsets (iteratif Apriori)
    // -----------------------------------------------------------------

    private function findFrequentItemsets(): array
    {
        $allFrequent = [];
        $kandidat    = $this->generateC1();
        $k           = 1;

        while (!empty($kandidat)) {
            $frequent = $this->filterBySupport($kandidat);

            if (empty($frequent)) break;

            foreach ($frequent as $itemset => $support) {
                $allFrequent[$itemset] = $support;
            }

            $kandidat = $this->generateCandidates(array_keys($frequent), $k);
            $k++;

            if ($k > 4) break; // Safety: max 4-itemset
        }

        return $allFrequent;
    }

    private function generateC1(): array
    {
        $items = [];
        foreach ($this->transaksi as $trx) {
            foreach ($trx as $item) {
                $items[(string) $item] = true;
            }
        }
        return array_keys($items);
    }

    private function filterBySupport(array $kandidat): array
    {
        $hitungan = [];

        foreach ($this->transaksi as $trx) {
            $trxSet = array_flip($trx);

            foreach ($kandidat as $kandidatItem) {
                $items    = is_array($kandidatItem) ? $kandidatItem : explode('|', $kandidatItem);
                $semuaAda = true;

                foreach ($items as $item) {
                    if (!isset($trxSet[$item])) {
                        $semuaAda = false;
                        break;
                    }
                }

                if ($semuaAda) {
                    $key = implode('|', $items);
                    $hitungan[$key] = ($hitungan[$key] ?? 0) + 1;
                }
            }
        }

        $frequent = [];
        foreach ($hitungan as $key => $count) {
            $support = $count / $this->totalTransaksi;
            if ($support >= $this->minSupport) {
                $frequent[$key] = $support;
            }
        }

        return $frequent;
    }

    private function generateCandidates(array $frequentKeys, int $k): array
    {
        $kandidat = [];
        $itemsets = array_map(fn($key) => explode('|', $key), $frequentKeys);
        $count    = count($itemsets);

        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $a = $itemsets[$i];
                $b = $itemsets[$j];

                if ($k === 1) {
                    $gabungan = array_unique(array_merge($a, $b));
                    sort($gabungan);
                    $key = implode('|', $gabungan);
                    if (!in_array($key, $kandidat)) {
                        $kandidat[] = $key;
                    }
                } else {
                    $prefixA = array_slice($a, 0, $k - 1);
                    $prefixB = array_slice($b, 0, $k - 1);

                    if ($prefixA === $prefixB) {
                        $gabungan = array_unique(array_merge($a, $b));
                        sort($gabungan);
                        if (count($gabungan) === $k + 1) {
                            $key = implode('|', $gabungan);
                            if (!in_array($key, $kandidat)) {
                                $kandidat[] = $key;
                            }
                        }
                    }
                }
            }
        }

        return $kandidat;
    }

    // -----------------------------------------------------------------
    // LANGKAH 2: Generate association rules dari frequent itemsets
    // -----------------------------------------------------------------

    private function generateRules(array $frequentItemsets): array
    {
        $rules = [];

        foreach ($frequentItemsets as $itemsetKey => $supportAB) {
            $items = explode('|', $itemsetKey);

            if (count($items) < 2) continue;

            $subsets = $this->generateSubsets($items);

            foreach ($subsets as $antecedent) {
                $consequent = array_values(array_diff($items, $antecedent));

                if (empty($consequent)) continue;

                $antKey = implode('|', $antecedent);
                $conKey = implode('|', $consequent);

                if (!isset($frequentItemsets[$antKey])) continue;

                $supportA   = $frequentItemsets[$antKey];
                $confidence = $supportAB / $supportA;

                if ($confidence < $this->minConfidence) continue;

                $supportB = $frequentItemsets[$conKey]
                    ?? $this->hitungSupportSingle($consequent);

                $lift = $supportB > 0 ? $confidence / $supportB : 0;

                $rules[] = [
                    'antecedent' => $antecedent,
                    'consequent' => $consequent,
                    'support'    => round($supportAB,  4),
                    'confidence' => round($confidence, 4),
                    'lift'       => round($lift,        4),
                    'frekuensi'  => (int) round($supportAB * $this->totalTransaksi),
                ];
            }
        }

        return $rules;
    }

    private function generateSubsets(array $items): array
    {
        $subsets = [];
        $total   = count($items);

        for ($mask = 1; $mask < (1 << $total) - 1; $mask++) {
            $subset = [];
            for ($bit = 0; $bit < $total; $bit++) {
                if ($mask & (1 << $bit)) {
                    $subset[] = $items[$bit];
                }
            }
            sort($subset);
            $subsets[] = $subset;
        }

        return $subsets;
    }

    private function hitungSupportSingle(array $items): float
    {
        $count = 0;
        foreach ($this->transaksi as $trx) {
            $trxSet   = array_flip($trx);
            $semuaAda = true;
            foreach ($items as $item) {
                if (!isset($trxSet[$item])) {
                    $semuaAda = false;
                    break;
                }
            }
            if ($semuaAda) $count++;
        }
        return $this->totalTransaksi > 0 ? $count / $this->totalTransaksi : 0;
    }

    // -----------------------------------------------------------------
    // UTILITAS PUBLIK
    // -----------------------------------------------------------------

    public function statistik(array $transaksi): array
    {
        $totalItem = 0;
        $unikItem  = [];

        foreach ($transaksi as $trx) {
            $totalItem += count($trx);
            foreach ($trx as $item) {
                $unikItem[$item] = true;
            }
        }

        return [
            'total_transaksi'        => count($transaksi),
            'total_item_unik'        => count($unikItem),
            'rata_item_per_transaksi' => count($transaksi) > 0
                ? round($totalItem / count($transaksi), 2)
                : 0,
        ];
    }
}
