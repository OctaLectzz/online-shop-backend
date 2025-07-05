<?php

namespace App\Http\Controllers;

use App\Models\Pay;
use App\Http\Requests\PayRequest;
use App\Http\Resources\PayResource;

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
        $data = $request->validated();

        if ($request->hasFile('transfer_proof')) {
            $pay->deleteImage();
            $data['transfer_proof'] = Pay::uploadImage($request->file('transfer_proof'));
        }

        $pay->update($data);

        return new PayResource($pay);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pay $pay)
    {
        $pay->deleteImage();
        $pay->delete();

        return response()->json(['message' => 'Pay deleted.']);
    }
}
