<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AttributeDefinition;
use Illuminate\Http\Request;

class AttributeDefinitionController extends Controller
{
    public function index()
    {
        $attributes = AttributeDefinition::withCount('values')->latest()->paginate(10);
        return view('dashboard.attribute_definitions.index', compact('attributes'));
    }

    public function list()
    {
        return response()->json(AttributeDefinition::with('values')->get());
    }

    public function create()
    {
        return view('dashboard.attribute_definitions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|unique:attribute_definitions,name_en|max:255',
            'name_ar' => 'required|string|max:255',
            'values' => 'nullable|array',
            'values.*.value_en' => 'required|string|max:255',
            'values.*.value_ar' => 'required|string|max:255',
        ]);

        $attribute = AttributeDefinition::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
        ]);

        if ($request->has('values')) {
            $attribute->values()->createMany($request->values);
        }

        return redirect()->route('attributes.index')->with('success', __('Attribute created successfully'));
    }

    public function show(string $id)
    {
        // Not implemented
    }

    public function edit(string $id)
    {
        $attribute = AttributeDefinition::with('values')->findOrFail($id);
        return view('dashboard.attribute_definitions.edit', compact('attribute'));
    }

    public function update(Request $request, string $id)
    {
        $attribute = AttributeDefinition::findOrFail($id);

        $request->validate([
            'name_en' => 'required|string|max:255|unique:attribute_definitions,name_en,' . $attribute->id,
            'name_ar' => 'required|string|max:255',
            'values' => 'nullable|array',
            'values.*.value_en' => 'required|string|max:255',
            'values.*.value_ar' => 'required|string|max:255',
        ]);

        $attribute->update([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
        ]);

        // Delete all old values and re-create to sync
        $attribute->values()->delete();

        if ($request->has('values')) {
            $attribute->values()->createMany($request->values);
        }

        return redirect()->route('attributes.index')->with('success', __('Attribute updated successfully'));
    }

    public function destroy(string $id)
    {
        $attribute = AttributeDefinition::findOrFail($id);
        $attribute->delete();
        return redirect()->route('attributes.index')->with('success', __('Attribute deleted successfully'));
    }
}
