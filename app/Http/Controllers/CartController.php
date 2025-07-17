<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index()
    {
        try {
            $cartItems = $this->getCartItems();
            $total = 0;

            foreach ($cartItems as $item) {
                if ($item->product) {
                    $total += $item->quantity * $item->product->current_price;
                }
            }

            return view('cart.index', compact('cartItems', 'total'));
        } catch (\Exception $e) {
            Log::error('Cart index error: ' . $e->getMessage());
            return view('cart.index', ['cartItems' => collect(), 'total' => 0]);
        }
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check stock
        if ($product->manage_stock && $product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available'
            ]);
        }

        $cartData = [
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'product_options' => $request->options ?? null,
        ];

        if (Auth::check()) {
            $cartData['user_id'] = Auth::id();
        } else {
            $cartData['session_id'] = session()->getId();
        }

        // Check if item already exists in cart
        $existingItem = Cart::where('product_id', $product->id)
            ->where(function($query) {
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                } else {
                    $query->where('session_id', session()->getId());
                }
            })
            ->first();

        if ($existingItem) {
            $existingItem->quantity += $request->quantity;
            $existingItem->save();
        } else {
            Cart::create($cartData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'cart_count' => $this->getCartCount()
        ]);
    }

    public function update(Request $request, Cart $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Verify ownership
        if (!$this->verifyCartItemOwnership($cartItem)) {
            abort(403);
        }

        $product = $cartItem->product;

        // Check stock
        if ($product->manage_stock && $product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available'
            ]);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'total' => $cartItem->total,
            'cart_total' => $this->getCartTotal()
        ]);
    }

    public function remove(Cart $cartItem)
    {
        // Verify ownership
        if (!$this->verifyCartItemOwnership($cartItem)) {
            abort(403);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => $this->getCartCount(),
            'cart_total' => $this->getCartTotal()
        ]);
    }

    public function clear()
    {
        $query = Cart::query();

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', session()->getId());
        }

        $query->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }

    public function count()
    {
        try {
            return response()->json([
                'count' => $this->getCartCount()
            ]);
        } catch (\Exception $e) {
            Log::error('Cart count error: ' . $e->getMessage());
            return response()->json([
                'count' => 0
            ]);
        }
    }

    private function getCartItems()
    {
        $query = Cart::with('product');

        if (Auth::check()) {
            $query->forUser(Auth::id());
        } else {
            $query->forSession(session()->getId());
        }

        return $query->get();
    }

    private function getCartCount()
    {
        $query = Cart::query();

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', session()->getId());
        }

        return $query->sum('quantity');
    }

    private function getCartTotal()
    {
        return $this->getCartItems()->sum('total');
    }

    private function verifyCartItemOwnership(Cart $cartItem)
    {
        if (Auth::check()) {
            return $cartItem->user_id === Auth::id();
        } else {
            return $cartItem->session_id === session()->getId();
        }
    }
}
