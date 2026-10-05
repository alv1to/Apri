<form method="POST" action="{{ route('pos.orders.status', $transaksi) }}" class="order-status-swipe-form">
    @csrf
    <input type="hidden" name="order_status" value="{{ $nextStatus }}">
    <div class="order-status-swipe" data-order-status-swipe>
        <span class="order-status-swipe-label">Geser untuk {{ $nextLabel }}</span>
        <input type="range" min="0" max="100" step="1" value="0"
               aria-label="Geser untuk mengubah status ke {{ $nextLabel }}"
               data-order-status-range>
    </div>
</form>