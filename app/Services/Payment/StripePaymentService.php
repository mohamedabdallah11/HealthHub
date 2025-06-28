<?php

namespace App\Services\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use App\Interfaces\PaymentGatewayInterface;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\Payment\BasePaymentService;
class StripePaymentService extends BasePaymentService implements PaymentGatewayInterface

{

    protected mixed $api_key;
    public function __construct()
    {
    $this->base_url = config('services.stripe.base_url');
    $this->api_key = config('services.stripe.secret_key');
        $this->header = [
            'Accept' => 'application/json',
            'Content-Type' =>'application/x-www-form-urlencoded',
            'Authorization' => 'Bearer ' . $this->api_key,
        ];

    }

public function sendPayment(Request $request): array
{
    $data = $this->formatData($request);

    $response = Http::withOptions([
            'verify' => false, 
        ])
        ->withToken($this->api_key)
        ->asForm()
        ->post($this->base_url . '/v1/checkout/sessions', $data);

    if ($response->successful() && !empty($response->json()['url'])) {
        return [
            'success' => true,
            'url' => $response->json()['url'],
        ];
    }

    return [
        'success' => false,
        'url' => route('payment.failed'),
    ];
}

    public function callBack(Request $request): bool
    {
          $session_id = $request->get('session_id');
          $response=$this->buildRequest('GET','/v1/checkout/sessions/'.$session_id);
        Storage::put('stripe.json',json_encode([
            'callback_response'=>$request->all(),
            'response'=>$response,
        ]));
         if($response->getData(true)['success']&& $response->getData(true)['data']['payment_status']==='paid') {

             return true;
         }
        return false;

    }

    public function formatData($request): array
    {
        return [
            "success_url" =>$request->getSchemeAndHttpHost().'/api/payment/callback?session_id={CHECKOUT_SESSION_ID}',
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
        ];
    }

}