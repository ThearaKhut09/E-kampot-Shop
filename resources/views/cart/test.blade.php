<!DOCTYPE html>
<html>
<head>
    <title>Cart Test</title>
</head>
<body>
    <h1>Cart Test Page</h1>
    <p>Cart Items Count: {{ $cartItems->count() }}</p>
    <p>Total: ${{ number_format($total, 2) }}</p>

    @if($cartItems->count() > 0)
        <ul>
        @foreach($cartItems as $item)
            <li>{{ $item->product->name ?? 'No Name' }} - Qty: {{ $item->quantity }}</li>
        @endforeach
        </ul>
    @else
        <p>Cart is empty</p>
    @endif
</body>
</html>
