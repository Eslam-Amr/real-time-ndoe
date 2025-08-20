<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Interfaces\PaymentGatewayInterface;
use App\Services\StripePaymentServiceDocs;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    //   protected PaymentGatewayInterface $paymentGateway;

    public function __construct(protected  PaymentGatewayInterface $paymentGateway , private StripePaymentServiceDocs $payV2)
    {

        // $this->paymentGateway = $paymentGateway;
    }


    public function callBackV2(Request $request){
$response= $this->payV2->getSession($request);
        if ($response) {

            return redirect()->route('payment.success.v2');
        }
        return redirect()->route('payment.failed.v2');
    }
    public function paymentProcessV2(Request $request){
return $this->payV2->pay($request);
    }
    public function paymentProcess(Request $request)
    {

        return $this->paymentGateway->sendPayment($request);
    }

    public function callBack(Request $request): \Illuminate\Http\RedirectResponse
    {
        $response = $this->paymentGateway->callBack($request);
        if ($response) {

            return redirect()->route('payment.success');
        }
        return redirect()->route('payment.failed');
    }



    public function success()
    {

        return view('payment-success');
    }
    public function failed()
    {

        return view('payment-failed');
    }

    public function successV2()
    {
        return view('payment.v2.success');
    }

    public function failedV2()
    {
        return view('payment.v2.failed');
    }
}
