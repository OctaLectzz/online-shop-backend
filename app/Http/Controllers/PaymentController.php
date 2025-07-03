<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResource;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::latest()->get();

        return PaymentResource::collection($payments);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     */
    public function store(PaymentRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = Payment::uploadImage($request->file('image'), $data['name']);
        }

        $payment = Payment::create($data);

        return new PaymentResource($payment);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        return new PaymentResource($payment);
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     */
    public function update(PaymentRequest $request, Payment $payment)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $payment->deleteImage();
            $data['image'] = Payment::uploadImage($request->file('image'), $data['name']);
        }

        $payment->update($data);

        return new PaymentResource($payment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->deleteImage();
        $payment->delete();

        return response()->json(['message' => 'Payment deleted.']);
    }
}
