<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\OrdertStatusEvent;
use App\Events\PaymentStatusEvent;
use App\Models\{Order, Payment};
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function createPayment(Request $request, $orderId)
    {
        $user = $request->user();
        $order = Order::where('id', $orderId)->where('user_id', $user->id)->firstOrFail();

        if (!in_array($order->status, ['pending','processing'])) {
            return response()->json(['message' => 'Order not payable','data'=>[]] , 200);
        }

        $data = $request->validate(['method' => 'required|in:vodafone_cash,fawry,credit_card,cash']);

        $payment = Payment::create([
            'order_id' => $order->id,
            'method' => $data['method'],
            'amount' => $order->total_price,
            'status' => 'pending'
        ]);

        // Example — call provider API here and save response
        if ($data['method'] === 'fawry') {
            // call Fawry API -> save invoice_id/payment_url
            $providerResponse = [
                'invoice_id' => 'FW-' . uniqid(),
                'payment_url' => url('/pay/fawry/' . $payment->id),
            ];
            $payment->update(['response' => json_encode($providerResponse)]);
            return response()->json(['payment' => $payment, 'provider' => $providerResponse]);
        }

        if ($data['method'] === 'vodafone_cash') {
            $providerResponse = [
                'reference' => 'VC-' . uniqid(),
                'instruction' => 'Send payment via Vodafone Cash with reference ...'
            ];
            $payment->update(['response' => json_encode($providerResponse)]);
            // event(new PaymentStatusEvent($payment));
            return response()->json(['payment' => $payment, 'provider' => $providerResponse]);
        }
        // بعد شرط فوري وفودافون
if ($data['method'] === 'cash') {
    $order->update(["status"=>"paid"]);
    $providerResponse = [
        'instruction' => 'Pay with cash upon delivery.',
        'note' => 'No online payment needed. Your order will be processed.'
    ];
    $payment->update(['response' => json_encode($providerResponse),"status"=>"completed"]);
       event(new PaymentStatusEvent($payment));

    return response()->json(['payment' => $payment, 'provider' => $providerResponse]);
}


        return response()->json(['payment' => $payment]);
    }

    // public callback endpoints — Fawry
    public function fawryCallback(Request $request)
    {
        $payload = $request->all();
        // TODO: verify signature HMAC as per Fawry docs (implement real check)
        $invoiceId = $payload['invoice_id'] ?? null;
        $status = $payload['status'] ?? null;

        // find payment by invoice id in response
        $payment = Payment::where('response', 'like', "%{$invoiceId}%")->first();
        if (!$payment) return response()->json(['message'=>'Payment not found'], 404);

        DB::transaction(function() use ($payment, $status, $payload) {
            $payment->response = json_encode($payload);
            if (in_array(strtolower($status), ['paid','success','completed'])) {
                $payment->status = 'completed';
                $payment->transaction_id = $payload['transaction_id'] ?? null;
                $payment->save();

                $order = $payment->order;
                $order->status = 'paid';
                $order->save();
            } else {
                $payment->status = 'failed';
                $payment->save();

                $payment->order->update(['status' => 'cancelled']);
            }
        });

        return response()->json(['message' => 'ok']);
    }

    // public callback endpoint — Vodafone Cash
    public function vodafoneCallback(Request $request)
    {
        $payload = $request->all();
        // TODO: verify signature per Vodafone docs
        $reference = $payload['reference'] ?? null;
        $status = $payload['status'] ?? null;

        $payment = Payment::where('response', 'like', "%{$reference}%")->first();
        if (!$payment) return response()->json(['message'=>'Payment not found'], 404);

        DB::transaction(function() use ($payment, $status, $payload) {
            $payment->response = json_encode($payload);
            if (in_array(strtolower($status), ['paid','success','completed'])) {
                $payment->status = 'completed';
                $payment->transaction_id = $payload['transaction_id'] ?? null;
                $payment->save();

                $order = $payment->order;
                $order->status = 'paid';
                $order->save();
            } else {
                $payment->status = 'failed';
                $payment->save();

                $payment->order->update(['status' => 'cancelled']);
            }
        });

        return response()->json(['message' => 'ok']);
    }
}