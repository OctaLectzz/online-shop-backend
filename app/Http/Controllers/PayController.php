<?php

namespace App\Http\Controllers;

use App\Models\Pay;
use App\Http\Requests\PayRequest;
use App\Http\Resources\PayResource;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

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
        if (in_array($pay->validation_status, ['accepted', 'rejected'])) {
            return response()->json([
                'message' => 'This payment has already been validated and cannot be changed.'
            ], 422);
        }

        $data = $request->validated();

        if ($request->hasFile('transfer_proof')) {
            $pay->deleteImage();
            $data['transfer_proof'] = Pay::uploadImage($request->file('transfer_proof'));
        }

        if (in_array($data['validation_status'] ?? '', ['accepted', 'rejected'])) {
            $data['validated_by'] = Auth::id();
        }

        $pay->update($data);

        $userName = $pay->order?->user?->name ?? 'Unknown User';

        // Activity
        if ($data['validation_status'] === 'accepted') {
            ActivityLogger::log('validation_status', 'pay', $pay->id, Auth::user()->name . ' accepted payment from ' . $userName);
        } elseif ($data['validation_status'] === 'rejected') {
            ActivityLogger::log('validation_status', 'pay', $pay->id, Auth::user()->name . ' rejected payment from ' . $userName);
        } else {
            ActivityLogger::log('validation_status', 'pay', $pay->id, Auth::user()->name . ' updated payment info for ' . $userName);
        }

        return new PayResource($pay);
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
