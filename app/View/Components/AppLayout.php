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
        $this->pendingOrderCount = Transaksi::whereIn('order_status', ['pending', 'dibayar'])->count();
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
