<x-mail::message>
# Order Confirmation

Hi {{ $order->billing_address['first_name'] }},

Thank you for your order! We're excited to confirm that we've received your order and we're preparing it for shipment.

## Order Details
**Order Number:** {{ $order->order_number }}
**Order Date:** {{ $order->created_at->format('M j, Y g:i A') }}
**Total Amount:** ${{ number_format($order->total_amount, 2) }}

## Order Items
@foreach($order->items as $item)
- {{ $item->product_name }} × {{ $item->quantity }} - ${{ number_format($item->total, 2) }}
@endforeach

## Shipping Address
{{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] }}
{{ $order->shipping_address['address'] }}
{{ $order->shipping_address['city'] }}, {{ $order->shipping_address['postal_code'] }}
{{ $order->shipping_address['country'] }}

## Payment Information
**Payment Method:** {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
**Payment Status:** {{ ucfirst($order->payment_status ?? 'pending') }}

@if($order->payment_method === 'bank_transfer' && $order->payment_status === 'pending')
## Payment Instructions
Please transfer ${{ number_format($order->total_amount, 2) }} to:
**Bank:** E-Kampot Bank
**Account:** 1234567890
**Reference:** {{ $order->order_number }}
@endif

<x-mail::button :url="route('orders.show', $order)">
View Order Details
</x-mail::button>

We'll send you another email with tracking information once your order ships.

If you have any questions, please don't hesitate to contact us.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
