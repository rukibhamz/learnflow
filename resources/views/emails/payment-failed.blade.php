<x-mail::message>
# Payment failed for {{ $order->course->title }}

Hi {{ $order->user->name }},

Unfortunately, your recent payment attempt for **{{ $order->course->title }}** was not successful.

Please try again from the course page or update your payment method.

If you believe this is a mistake, contact support and include your order reference: **Order #{{ $order->id }}**.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

