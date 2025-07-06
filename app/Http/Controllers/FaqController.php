<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Http\Requests\FaqRequest;
use App\Http\Resources\FaqResource;

class FaqController extends Controller
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
        $faqs = Faq::latest()->get();

        return FaqResource::collection($faqs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FaqRequest $request)
    {
        $data = $request->validated();

        $faq = Faq::create($data);

        return new FaqResource($faq);
    }

    /**
     * Display the specified resource.
     */
    public function show(Faq $faq)
    {
        return new FaqResource($faq);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FaqRequest $request, Faq $faq)
    {
        $data = $request->validated();

        $faq->update($data);

        return new FaqResource($faq);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        return response()->json(['message' => 'Faq deleted.']);
    }
}
