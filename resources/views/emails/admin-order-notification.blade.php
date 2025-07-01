<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Notification - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background: #e74c3c;
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
            text-align: center;
        }
        .order-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .order-items {
            margin: 20px 0;
        }
        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .item:last-child {
            border-bottom: none;
        }
        .total {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            color: #27ae60;
        }
        .customer-info {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .alert {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛍️ New Order Received!</h1>
            <p>A new order has been placed on E-Kampot Shop</p>
        </div>

        <div class="alert">
            <strong>Action Required:</strong> A new order requires your attention in the admin dashboard.
        </div>

        <div class="order-info">
            <h3>📋 Order Details</h3>
            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y g:i A') }}</p>
            <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
            <p><strong>Payment Status:</strong>
                <span style="color: {{ $order->payment_status === 'paid' ? '#27ae60' : '#e74c3c' }};">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </p>
            <p><strong>Order Status:</strong> {{ ucfirst($order->status) }}</p>
        </div>

        <div class="customer-info">
            <h3>👤 Customer Information</h3>
            <p><strong>Name:</strong> {{ $order->billing_address['first_name'] ?? 'N/A' }} {{ $order->billing_address['last_name'] ?? '' }}</p>
            <p><strong>Email:</strong> {{ $order->billing_address['email'] ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $order->billing_address['phone'] ?? 'N/A' }}</p>
            <p><strong>Address:</strong> {{ $order->billing_address['address'] ?? 'N/A' }}, {{ $order->billing_address['city'] ?? 'N/A' }}, {{ $order->billing_address['postal_code'] ?? 'N/A' }}, {{ $order->billing_address['country'] ?? 'N/A' }}</p>
        </div>

        <div class="order-items">
            <h3>🛒 Order Items</h3>
            @foreach($order->items as $item)
            <div class="item">
                <div>
                    <strong>{{ $item->product_name }}</strong><br>
                    <small>SKU: {{ $item->product_sku }}</small><br>
                    <small>Quantity: {{ $item->quantity }}</small>
                </div>
                <div style="text-align: right;">
                    <strong>${{ number_format($item->total, 2) }}</strong><br>
                    <small>${{ number_format($item->price, 2) }} each</small>
                </div>
            </div>
            @endforeach
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span>Subtotal:</span>
                <span>${{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span>Shipping:</span>
                <span>${{ number_format($order->shipping_amount, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span>Tax:</span>
                <span>${{ number_format($order->tax_amount, 2) }}</span>
            </div>
            <div class="total">
                Total: ${{ number_format($order->total_amount, 2) }}
            </div>
        </div>

        @if($order->notes)
        <div style="background: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h4>📝 Order Notes</h4>
            <p>{{ $order->notes }}</p>
        </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/admin/orders/' . $order->id) }}" class="button">
                View Order in Admin Dashboard
            </a>
        </div>

        <div style="border-top: 1px solid #eee; padding-top: 20px; margin-top: 30px; text-align: center; color: #666; font-size: 14px;">
            <p>This is an automated notification from E-Kampot Shop.</p>
            <p>© {{ date('Y') }} E-Kampot Shop. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
