<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Service\ContactService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    protected ContactService $service;

    public function __construct(ContactService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the contact information.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $contacts = $this->service->getContacts();

        return response()->json([
            'success' => true,
            'message' => __('Contact information retrieved successfully'),
            'data' => $contacts,
        ]);
    }
}
