<?php

namespace App\Services;

use Illuminate\Http\Request;

class StripePaymentServiceDocs
{
    public function pay(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
        $session = $stripe->checkout->sessions->create([
            'success_url' => $request->getSchemeAndHttpHost().'/api/payment/callback/V2?session_id={CHECKOUT_SESSION_ID}',
                  'cancel_url'  => $request->getSchemeAndHttpHost() . '/api/payment/cancel',
              "line_items" => [
                [
                    "price_data"=>[
                        "unit_amount" => $request->input('amount')*100,
                        "currency" => $request->input("currency"),
                        "product_data" => [
                            "name" => "product name",
                            "description" => "description of product"
                        ],
                    ],
                    "quantity" => 1,
                ],
            ],
            "mode" => "payment",

        ]);
         return response()->json([
        'id'  => $session->id,
        'url' => $session->url,
    ]);
    }


    public function getSession(Request $request)
{
    $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

    $sessionId = $request->input('session_id');

    if (!$sessionId) {
        return response()->json(['error' => 'Missing session_id'], 400);
    }

    $session = $stripe->checkout->sessions->retrieve($sessionId, []);

    return response()->json($session);
}

}
