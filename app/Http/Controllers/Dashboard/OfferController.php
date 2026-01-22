<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\offers\{createe, update};
use App\Events\ProductFavourite;
use Illuminate\Support\Facades\{Cache, DB};
use App\Models\{Offer, Property};

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offers = Offer::with(['variant.product', 'variant.variantAttributes'])->paginate(10);

        return view('dashboard.offers.index', compact('offers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // We need properties that are linked to products
        $properties = Property::with(['product', 'variantAttributes'])->get();
        
        return view('dashboard.offers.create', compact('properties'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(createe $request)
    {
        $data = $request->validated();
        $offer = Offer::create($data);

        // Notify users who have this item in their cart
        Cache::flush();
        \App\Events\OfferCreatedForCartItems::dispatch($offer);
         \App\Events\OfferCreatedForFavorites::dispatch($offer);
    
        return response()->json([
            'success' => true,
            'message' => __('Offer created successfully!'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $offer = Offer::findOrFail($id);
        $properties = Property::with(['product', 'variantAttributes'])->get();
        return view("dashboard.offers.edit",compact("offer","properties"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(update $request, string $id)
    {
        $offer = Offer::findOrFail($id);
        $data = $request->validated();
        $offer->update($data);

        // Notify users who have this item in their cart
        \App\Events\OfferCreatedForCartItems::dispatch($offer);
        \App\Events\OfferCreatedForFavorites::dispatch($offer);

        return response()->json([
            "success" => true,
            "message" => __("Offer updated successfully!")
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $offer = Offer::findOrFail($id);
        $offer->delete();
        return redirect()->back()->with('success', __('Offer deleted successfully!'));
    }
}