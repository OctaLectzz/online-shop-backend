<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Http\Requests\ShipmentRequest;
use App\Http\Resources\ShipmentResource;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
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
        $shipments = Shipment::latest()->get();

        return ShipmentResource::collection($shipments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShipmentRequest $request)
    {
        $data = $request->validated();
        $data['shipping_date'] = now();
        $data['processed_by'] = Auth::id();

        $shipment = Shipment::create($data);

        return new ShipmentResource($shipment);
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipment $shipment)
    {
        return new ShipmentResource($shipment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShipmentRequest $request, Shipment $shipment)
    {
        $data = $request->validated();

        $shipment->update($data);

        return new ShipmentResource($shipment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipment $shipment)
    {
        $shipment->delete();

        return response()->json(['message' => 'Shipment deleted.']);
    }
}
