<?php
namespace App\Http\Controllers;

use App\Http\Requests\SubscribeRequest;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function storePaymentMethod(Request $request)
    {
        $user = $request->user();
        $paymentMethodId = $request->payment_method;

        try {
            $user->createOrGetStripeCustomer();
            $user->updateDefaultPaymentMethod($paymentMethodId);

            $paymentMethod = $user->findPaymentMethod($paymentMethodId);

            return response()->json([
                'payment_method' => $paymentMethod,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    
}
