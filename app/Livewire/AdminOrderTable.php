<?php

namespace App\Livewire;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\RefundService;
use Livewire\Component;
use Livewire\WithPagination;

class AdminOrderTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public ?int $refundingOrderId = null;
    public string $refundReason = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openRefundModal(int $orderId): void
    {
        $this->refundingOrderId = $orderId;
        $this->refundReason = '';
    }

    public function closeRefundModal(): void
    {
        $this->refundingOrderId = null;
        $this->refundReason = '';
    }

    public function processRefund(): void
    {
        if (! $this->refundingOrderId) {
            return;
        }

        $order = Order::findOrFail($this->refundingOrderId);
        $service = app(RefundService::class);

        try {
            $service->refund($order, $this->refundReason ?: null);
            session()->flash('success', 'Refund processed for order #' . $order->id);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }

        $this->closeRefundModal();
    }

    public function render()
    {
        $orders = Order::query()
            ->with(['user', 'course'])
            ->when($this->search, function ($q) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
                    ->orWhereHas('course', fn ($c) => $c->where('title', 'like', "%{$this->search}%"));
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.admin-order-table', [
            'orders' => $orders,
        ]);
    }
}
