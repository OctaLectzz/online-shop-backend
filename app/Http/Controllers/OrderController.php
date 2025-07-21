<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
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
        $orders = Order::latest()->get();

        return OrderResource::collection($orders);
    }

    /**
     * Display a listing by user of the resource.
     */
    public function getByUser()
    {
        $carts = Order::where('user_id', Auth::id())->latest()->get();

        return OrderResource::collection($carts);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     */
    public function store(OrderRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $data['invoice'] = Order::generateUniqueInvoice();
            $data['order_date'] = now();
            $data['order_status'] = $data['order_status'] ?? 'pending';
            $data['payment_status'] = $data['payment_status'] ?? 'unpaid';

            $order = DB::transaction(function () use ($data) {
                $items = $data['items'];
                unset($data['items']);

                $order = Order::create($data);

                foreach ($items as $item) {
                    $order->items()->create($item);

                    $product = Product::findOrFail($item['product_id']);
                    $product->decrement('stock', $item['quantity']);
                    $product->increment('sold', $item['quantity']);

                    $cart = Cart::where('user_id', $order->user_id)->where('product_id', $item['product_id'])->first();
                    $cart->delete();
                }


                return $order;
            });

            return new OrderResource($order);
        } catch (\Throwable $e) {
            Log::error('Failed to store order', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Order failed to create',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     */
    public function update(OrderRequest $request, Order $order)
    {
        try {
            $data = $request->validated();

            DB::transaction(function () use ($order, $data) {
                if (isset($data['order_status']) && $data['order_status'] === 'cancelled') {
                    if ($order->order_status !== 'cancelled') {
                        foreach ($order->items as $item) {
                            $product = Product::findOrFail($item->product_id);
                            $product->increment('stock', $item->quantity);
                            $product->decrement('sold', $item->quantity);
                        }
                    }

                    $order->update($data);
                } else {
                    $items = $data['items'];
                    unset($data['items']);

                    foreach ($order->items as $oldItem) {
                        $product = Product::findOrFail($oldItem->product_id);
                        $product->increment('stock', $oldItem->quantity);
                        $product->decrement('sold', $oldItem->quantity);
                    }

                    $order->items()->delete();
                    $order->update($data);

                    foreach ($items as $item) {
                        $order->items()->create($item);

                        $product = Product::findOrFail($item['product_id']);
                        $product->decrement('stock', $item['quantity']);
                        $product->increment('sold', $item['quantity']);
                    }
                }
            });

            return new OrderResource($order);
        } catch (\Throwable $e) {
            Log::error('Failed to update order', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Order failed to update',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->items()->delete();
        $order->delete();

        return response()->json(['message' => 'Order deleted.']);
    }
}
