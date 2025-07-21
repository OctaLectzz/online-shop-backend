<?php

namespace App\Http\Controllers;

use App\Models\Pay;
use App\Models\Order;
use App\Helpers\ActivityLogger;
use App\Http\Requests\PayRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PayResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PayController extends Controller
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
        $pays = Pay::latest()->get();

        return PayResource::collection($pays);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     */
    public function store(PayRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('transfer_proof')) {
            $data['transfer_proof'] = Pay::uploadImage($request->file('transfer_proof'));
        }

        $pay = Pay::create($data);

        $order = Order::findOrFail($data['order_id']);
        if ($order->payment_status === 'paid') {
            throw new \Exception('Order sudah dibayar sebelumnya');
        }

        $order->update([
            'payment_status' => 'paid',
            'order_status' => 'processing'
        ]);

        ActivityLogger::log('payment', 'pay', $pay->id, (Auth::check() ? Auth::user()->name : 'Someone') . ' make a payment');

        return new PayResource($pay);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pay $pay)
    {
        return new PayResource($pay);
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     */
    public function update(PayRequest $request, Pay $pay)
    {
        // Prevent updates to already validated payments
        if (in_array($pay->validation_status, ['accepted', 'rejected'])) {
            return response()->json([
                'message' => 'This payment has already been validated and cannot be changed.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Handle file upload
            if ($request->hasFile('transfer_proof')) {
                $pay->deleteImage();
                $data['transfer_proof'] = Pay::uploadImage($request->file('transfer_proof'));
            }

            // Set validator if status is being changed
            if (in_array($data['validation_status'] ?? '', ['accepted', 'rejected'])) {
                $data['validated_by'] = Auth::id();
                $data['validated_at'] = now();
            }

            $pay->update($data);

            // Update related order status if payment validation status changed
            if (isset($data['validation_status'])) {
                $pay->updateOrderStatusBasedOnPayment($pay, $data['validation_status']);
            }

            $userName = $pay->order?->user?->name ?? 'Unknown User';
            $pay->logPaymentActivity($pay, $data['validation_status'] ?? null, $userName);

            DB::commit();

            return new PayResource($pay->fresh());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment update failed', [
                'pay_id' => $pay->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Payment update failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pay $pay)
    {
        $pay->deleteImage();
        $pay->delete();

        ActivityLogger::log('delete', 'pay', $pay->id, (Auth::check() ? Auth::user()->name : 'Someone') . ' deleted payment confirmation');

        return response()->json(['message' => 'Pay deleted.']);
    }
}
