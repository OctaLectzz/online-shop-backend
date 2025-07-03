<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Http\Requests\PromoRequest;
use App\Http\Resources\PromoResource;
use Illuminate\Support\Facades\Auth;

class PromoController extends Controller
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
        $promos = Promo::latest()->get();

        return PromoResource::collection($promos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PromoRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();

        $promo = Promo::create($data);

        return new PromoResource($promo);
    }

    /**
     * Display the specified resource.
     */
    public function show(Promo $promo)
    {
        return new PromoResource($promo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PromoRequest $request, Promo $promo)
    {
        $data = $request->validated();

        $promo->update($data);

        return new PromoResource($promo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promo $promo)
    {
        $promo->delete();

        return response()->json(['message' => 'Promo deleted.']);
    }
}
