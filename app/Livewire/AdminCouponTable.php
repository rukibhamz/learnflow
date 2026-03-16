<?php

namespace App\Livewire;

use App\Enums\DiscountType;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Livewire\Component;
use Livewire\WithPagination;

class AdminCouponTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public array $selected = [];
    public bool $selectAllOnPage = false;

    // Form modal
    public bool $showFormModal = false;
    public ?int $editingId = null;
    public string $code = '';
    public string $name = '';
    public string $discount_type = 'fixed';
    public string $amount = '';
    public string $minimum_amount = '';
    public string $max_uses = '';
    public string $expires_at = '';
    public bool $is_active = true;
    public string $stripe_coupon_id = '';

    // Usage modal
    public bool $showUsageModal = false;
    public ?Coupon $usageCoupon = null;
    public $usage = [];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'code', 'name', 'discount_type', 'amount', 'minimum_amount', 'max_uses', 'expires_at', 'stripe_coupon_id']);
        $this->is_active = true;
        $this->discount_type = 'fixed';
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $coupon = Coupon::findOrFail($id);
        $this->editingId = $id;
        $this->code = $coupon->code;
        $this->name = $coupon->name ?? '';
        $this->discount_type = $coupon->discount_type->value;
        $this->amount = (string) $coupon->amount;
        $this->minimum_amount = (string) ($coupon->minimum_amount ?? '');
        $this->max_uses = (string) ($coupon->max_uses ?? '');
        $this->expires_at = $coupon->expires_at?->format('Y-m-d') ?? '';
        $this->is_active = (bool) $coupon->is_active;
        $this->stripe_coupon_id = $coupon->stripe_coupon_id ?? '';
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'code'          => 'required|string|max:50|unique:coupons,code' . ($this->editingId ? ",{$this->editingId}" : ''),
            'name'          => 'required|string|max:255',
            'discount_type' => 'required|in:fixed,percentage',
            'amount'        => 'required|numeric|min:0',
            'minimum_amount'=> 'nullable|numeric|min:0',
            'max_uses'      => 'nullable|integer|min:1',
            'expires_at'    => 'nullable|date',
        ]);

        $data = [
            'code'           => strtoupper($this->code),
            'name'           => $this->name,
            'discount_type'  => DiscountType::from($this->discount_type),
            'amount'         => $this->amount,
            'minimum_amount' => $this->minimum_amount ?: null,
            'max_uses'       => $this->max_uses ?: null,
            'expires_at'     => $this->expires_at ?: null,
            'is_active'      => $this->is_active,
            'stripe_coupon_id' => $this->stripe_coupon_id ?: null,
        ];

        if ($this->editingId) {
            Coupon::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Coupon updated.');
        } else {
            Coupon::create($data);
            session()->flash('success', 'Coupon created.');
        }

        $this->showFormModal = false;
    }

    public function toggleActive(int $id): void
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update(['is_active' => !$coupon->is_active]);
    }

    public function bulkDeactivate(): void
    {
        Coupon::whereIn('id', $this->selected)->update(['is_active' => false]);
        $this->selected = [];
        session()->flash('success', 'Selected coupons deactivated.');
    }

    public function openUsage(int $id): void
    {
        $this->usageCoupon = Coupon::findOrFail($id);
        $this->usage = CouponUsage::where('coupon_id', $id)
            ->with(['user', 'order'])
            ->latest()
            ->get();
        $this->showUsageModal = true;
    }

    public function closeUsage(): void
    {
        $this->showUsageModal = false;
        $this->usageCoupon = null;
        $this->usage = [];
    }

    public function createStripeCoupon(int $id): void
    {
        // Placeholder — Stripe integration not yet implemented
        session()->flash('info', 'Stripe coupon creation not yet implemented.');
    }

    public function render()
    {
        $coupons = Coupon::query()
            ->when($this->search, fn ($q) => $q->where('code', 'like', "%{$this->search}%")
                ->orWhere('name', 'like', "%{$this->search}%"))
            ->when($this->statusFilter === 'active', fn ($q) => $q->where('is_active', true))
            ->when($this->statusFilter === 'inactive', fn ($q) => $q->where('is_active', false))
            ->when($this->statusFilter === 'expired', fn ($q) => $q->where('expires_at', '<', now()))
            ->latest()
            ->paginate(20);

        return view('livewire.admin-coupon-table', ['coupons' => $coupons]);
    }
}
