<?php

namespace App\View\Components;

use App\Models\Transaksi;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public int $pendingOrderCount;

    public function __construct()
    {
        $this->pendingOrderCount = Transaksi::whereIn('order_status', [
            'pending', 'dibayar', 'sedang_dibuat', 'siap_diambil',
        ])->count();
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
