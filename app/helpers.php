<?php

use App\Models\Setting;

if (! function_exists('currency_symbol')) {
    function currency_symbol(): string
    {
        $symbols = [
            'USD' => '$', 'EUR' => '€', 'GBP' => '£',
            'NGN' => '₦', 'GHS' => '₵', 'KES' => 'KSh',
            'ZAR' => 'R',  'INR' => '₹', 'CAD' => 'CA$',
            'AUD' => 'A$',
        ];
        $currency = Setting::get('currency', Setting::get('payment_currency', 'USD'));
        return $symbols[$currency] ?? $currency;
    }
}

if (! function_exists('format_price')) {
    function format_price(float $amount): string
    {
        if ($amount <= 0) {
            return 'Free';
        }
        return currency_symbol() . number_format($amount, 2);
    }
}
