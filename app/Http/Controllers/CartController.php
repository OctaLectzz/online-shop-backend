<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Http\Requests\CartRequest;
use App\Http\Resources\CartResource;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carts = Cart::latest()->get();

        return CartResource::collection($carts);
    }

    /**
     * Display a listing by user of the resource.
     */
    public function getByUser()
    {
        $carts = Cart::where('user_id', Auth::id())->latest()->get();

        return CartResource::collection($carts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CartRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();


        // Stock Chexk
        $product = Product::findOrFail($data['product_id']);
        if ($data['quantity'] > $product->stock) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Order quantity cannot exceed stock',
            ], 403);
        } else {
            $cart = Cart::where('user_id', $data['user_id'])->where('product_id', $data['product_id'])->first();

            if ($cart) {
                $data['quantity'] = $cart->quantity + $data['quantity'];
                $cart->update($data);
            } else {
                $cart = Cart::create($data);
            }
        }

        return new CartResource($cart);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        return new CartResource($cart);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CartRequest $request, Cart $cart)
    {
        $data = $request->validated();

        // Stock Check
        $product = Product::findOrFail($data['product_id']);
        if ($data['quantity'] > $product->stock) {
            $cart->update([
                'quantity' => $product->stock
            ]);

            return response()->json([
                'status' => 'failed',
                'message' => 'Order quantity cannot exceed stock',
            ], 403);
        } else {
            $cart->update($data);
        }

        return new CartResource($cart);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();

        return response()->json(['message' => 'Cart deleted.']);
    }
}
