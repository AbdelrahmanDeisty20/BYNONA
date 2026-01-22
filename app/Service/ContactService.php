<?php
namespace App\Service;

use App\Models\Contact;

class ContactService
{
    /**
     * Get all contact information.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getContacts()
    {
        return Contact::select('email', 'address_ar', 'address_en', 'phone', 'whatsapp', 'facebook')->get();
    }
}
