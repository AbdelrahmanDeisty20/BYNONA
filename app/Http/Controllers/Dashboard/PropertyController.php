<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{Product, Property};
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index($id)
    {
        $product = Product::findOrFail($id);
        $properties = $product->properties()->paginate(10);
        return view('dashboard.properties.index', compact('product', 'properties'));
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return redirect()->back()->with('success', __('Variant deleted successfully'));
    }
}
