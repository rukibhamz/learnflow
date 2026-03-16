<?php

namespace App\Livewire;

use App\Models\Coupon;
use App\Services\CouponService;
use Livewire\Component;

class CouponField extends Component
{
    public float $orderAmount;

    public string $code = '';
    public ?array $result = null;

    public function mount(float $orderAmount): void
    {
        $this->orderAmount = $orderAmount;
        $this->code = session('coupon_code', '');

        if ($this->code !== '' && auth()->check()) {
            $this->refreshResult();
        }
    }

    public function apply(CouponService $service): void
    {
        $this->resetErrorBag();

        if (! auth()->check()) {
            $this->addError('code', 'Please log in to apply a coupon.');
            return;
        }

        $validation = $service->validate($this->code, auth()->user(), $this->orderAmount);

        if (! $validation->valid) {
            $this->result = [
                'valid' => false,
                'message' => $validation->message,
                'discount' => 0,
                'coupon_id' => $validation->coupon_id,
                'code' => $validation->code ?? $this->code,
            ];
            session()->forget('coupon_code');
            return;
        }

        session(['coupon_code' => $this->code]);

        $this->result = [
            'valid' => true,
            'message' => $validation->message,
            'discount' => $validation->discount_amount,
            'coupon_id' => $validation->coupon_id,
            'code' => $validation->code ?? $this->code,
        ];
    }

    public function remove(): void
    {
        session()->forget('coupon_code');
        $this->code = '';
        $this->result = null;
    }

    public function refreshResult(): void
    {
        if (! auth()->check()) {
            return;
        }

        $service = app(CouponService::class);
        $validation = $service->validate($this->code, auth()->user(), $this->orderAmount);

        $this->result = [
            'valid' => $validation->valid,
            'message' => $validation->message,
            'discount' => $validation->discount_amount,
            'coupon_id' => $validation->coupon_id,
            'code' => $validation->code ?? $this->code,
        ];
    }

    public function render()
    {
        $appliedCoupon = null;
        if (($this->result['valid'] ?? false) && ($this->result['coupon_id'] ?? null)) {
            $appliedCoupon = Coupon::find($this->result['coupon_id']);
        }

        return view('livewire.coupon-field', [
            'appliedCoupon' => $appliedCoupon,
        ]);
    }
}

