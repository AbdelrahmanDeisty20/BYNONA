<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments =Payment::with("order")->paginate(10);
        return view("dashboard.payments.index",compact("payments"));
        
    }
    public function destroy($id)
    {
        $payments =Payment::findOrFail($id);
        $payments->delete();
        return redirect()->back();
        
    }
    
}