<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contact = Contact::first();
        return view('dashboard.contacts.index', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        return view('dashboard.contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'email' => 'required|email|unique:contacts,email,' . $contact->id,
            'address_ar' => 'required|string',
            'address_en' => 'required|string',
            'phone' => 'required|string',
            'whatsapp' => 'nullable|string',
            'facebook' => 'nullable|string',
        ]);

        $contact->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => __('Contact information updated successfully')
        ]);
    }
}
