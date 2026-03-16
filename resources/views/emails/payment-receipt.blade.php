<x-mail::message>
# Payment received for {{ $order->course->title }}

Hi {{ $order->user->name }},

We have received your payment for **{{ $order->course->title }}**.

**Amount paid:** {{ strtoupper($order->currency) }} {{ number_format($order->amount, 2) }}

<x-mail::button :url="route('learn.show', $order->course->slug)">
Start Learning
</x-mail::button>

You can download your invoice anytime from the **My Orders** page in your dashboard.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

