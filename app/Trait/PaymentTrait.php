<?php

namespace App\Trait;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\NotificationTemplate;
use App\Models\Setting;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Stripe\StripeClient;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Midtrans\Snap;
use Midtrans\Config;

trait PaymentTrait
{

    protected function StripePayment(Request $request)
    {
        $vendor_id = $request->input('vendor_id');
        $baseURL = env('APP_URL');
        $subcriptionType = $request->input('subscription_type');
        $success_url = $request->input('success_url', env('APP_URL') . '/payment/success');
        $stripe_secret_key = GetpaymentMethod('stripe_secretkey');
        $currency = GetcurrentCurrency();
        $stripe = new StripeClient($stripe_secret_key);
        $price = $request->input('price');
        $plan_id = $request->input('plan_id');
        $priceInCents = intval(round($price * 100));
        $currency = strtolower(GetcurrentCurrency());
        $checkout_session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => __('messages.subscription_plans'),
                        ],
                        'unit_amount' => $priceInCents,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => __('messages.lbl_payment'),
            'metadata' => [
                'plan_id' => $plan_id,
                'vendor_id' => $vendor_id,
                'subcriptionType' => $subcriptionType,
            ],
            'success_url' => $success_url . '?gateway=stripe&session_id={CHECKOUT_SESSION_ID}'
        ]);
        return response()->json(['redirect' => $checkout_session->url]);
    }

    protected function RazorpayPayment(Request $request, $price)
    {
        $baseURL = config('app.url');
        $razorpayKey = GetpaymentMethod('razorpay_publickey');
        $razorpaySecret = GetpaymentMethod('razorpay_secretkey');
        $subcriptionType = $request->input('subscription_type');
        $plan_id = $request->input('plan_id');
        $priceInPaise = intval(round($price * 100));
        $currency = GetcurrentCurrency();
        $formattedCurrency = strtoupper(strtolower($currency));
        try {
            $api = new \Razorpay\Api\Api($razorpayKey, $razorpaySecret);
            $orderData = [
                'receipt' => 'rcptid_' . time(),
                'amount' => $priceInPaise,
                'currency' =>   $currency,
                'payment_capture' => 1
            ];
            $razorpayOrder = $api->order->create($orderData);
            session(['razorpay_order_id' => $razorpayOrder['id']]);
            return response()->json([
                'key' => $razorpayKey,
                'amount' => $amount,
                'currency' =>   $formattedCurrency,
                'name' => config('app.name'),
                'description' => __('messages.Subscription_payment'),
                'plan_id' => $plan_id,
                'subcriptionType' => $subcriptionType,
                'order_id' => null,
                'success_url' => route('payment.success'),
                'prefill' => [
                    'name' => auth()->user()->name ?? '',
                    'email' => auth()->user()->email ?? '',
                    'contact' => auth()->user()->phone ?? ''
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    protected function PaystackPayment(Request $request)
    {
        $baseURL = env('APP_URL');
        $paystackSecretKey = GetpaymentMethod('paystack_secretkey');
        $price = $request->input('price');
        $plan_id = $request->input('plan_id');
        $subcriptionType = $request->input('subcriptionType');
        $priceInKobo = intval(round($price * 100));  // Paystack uses kobo
        $user = User::find($request->input('vendor_id'));
        $vendor_id = $request->input('vendor_id');
        $success_url = $request->input('success_url', env('APP_URL') . '/payment/success');
        $currency = strtolower(GetcurrentCurrency());
        // Create a new Paystack payment
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $paystackSecretKey,
        ])->post('https://api.paystack.co/transaction/initialize', [
            'email' => $user->email ?? '-', // Get user email from authenticated user
            'amount' => $priceInKobo,
            'currency' =>      $currency,
            'callback_url' => $success_url . '?gateway=paystack',
            'metadata' => [
                'plan_id' => $plan_id,
                'vendor_id' => $vendor_id,
                'subcriptionType' => $subcriptionType,
            ],
        ]);
        $responseBody = $response->json();
        if ($responseBody['status']) {
            return response()->json([
                'success' => true,
                'redirect' => $responseBody['data']['authorization_url'],
            ]);
        } else {
            return response()->json(['error' => __('messages.something_wrong_method')], 400);
        }
    }

    protected function PayPalPayment(Request $request)
    {
        $baseURL = env('APP_URL');
        $price = $request->input('price');
        $subcriptionType = $request->input('subcriptionType');
        $plan_id = $request->input('plan_id');
        $success_url = $request->input('success_url', env('APP_URL') . '/payment/success');
        // Validate price
        if (!is_numeric($price) || $price <= 0) {
            return redirect()->back()->withErrors('messages.invalid_price_value');
        }
        try {
            // Get Access Token
            $accessToken = $this->getAccessToken();
            // Create Payment
            $payment = $this->createPayment($accessToken, $subcriptionType, $price, $plan_id, $request->input('vendor_id'), $success_url);
            if (isset($payment['links'])) {
                foreach ($payment['links'] as $link) {
                    if ($link['rel'] === 'approval_url') {
                        return response()->json(['success' => true, 'redirect' => $link['href']]);
                    }
                }
            }
            return redirect()->back()->withErrors('messages.payment_creation_failed');
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors(__('messages.payment_process_failed') . $ex->getMessage());
        }
    }

    protected function FlutterwavePayment(Request $request)
    {
        $baseURL = env('APP_URL');
        $flutterwaveKey = GetpaymentMethod('flutterwave_secretkey');
        $price = $request->input('price');
        $subcriptionType = $request->input('subcriptionType');
        $plan_id = $request->input('plan_id');
        $priceInKobo = intval(round($price * 100));
        $success_url = $request->input('success_url', env('APP_URL') . '/payment/success');
        $currency = strtolower(GetcurrentCurrency());
        $data = [
            'tx_ref' => 'txn_' . time(),
            'email' => auth()->user()->email,
            'amount' => $priceInKobo,
            "currency" =>  $currency,
            "payment_type" => "mobilemoneyghana",
            'callback_url' => $success_url . '?gateway=flutterwave',
            'metadata' => [
                'plan_id' => $plan_id,
                'subcriptionType' => $subcriptionType,
            ],
        ];
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $flutterwaveKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.flutterwave.com/v3/charges?type=mobilemoneyghana', $data);
        $responseBody = $response->json();
        // Check if the response is valid and has expected keys
        if ($response->successful() && isset($responseBody['status'])) {
            if ($responseBody['status'] === 'success') {
                return response()->json(['success' => true, 'redirect' => $responseBody['data']['link']]);
            } else {

                return response()->json(['error' => __('messages.payment_initiation_failed') . ($responseBody['message'] ?? __('messages.unknown_error'))], 400);
            }
        }
    }

    protected function CinetPayment(Request $request)
    {
        $baseURL = env('APP_URL');
        $cinetApiKey = GetpaymentMethod('cinet_Secret_key');
        $price = $request->input('price');
        $plan_id = $request->input('plan_id');
        $subcriptionType = $request->input('subcriptionType');
        $priceInCents = intval(round($price * 100));

        $data = [
            'amount' => $priceInCents,
            'currency' =>  $currency,
            'plan_id' => $plan_id,
            'subcriptionType' => $subcriptionType,
            'callback_url' => $baseURL . '/payment/success?gateway=cinet',
            'user_email' => auth()->user()->email,
        ];
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $cinetApiKey,
        ])->post('https://api.cinet.com/payment', $data);
        $responseBody = $response->json();
        if ($response->successful() && isset($responseBody['payment_url'])) {
            return redirect($responseBody['payment_url']);
        } else {
            return redirect()->back()->withErrors(__('messages.payment_initiation_failed ') . ($responseBody['message'] ?? __('messages.unknown_error')));
        }
    }

    protected function SadadPayment(Request $request)
    {
        $baseURL = env('APP_URL');
        $price = $request->input('price');
        $plan_id = $request->input('plan_id');
        $subcriptionType = $request->input('subcriptionType');
        $response = $this->makeSadadPaymentRequest($price, $plan_id, $subcriptionType);
        if ($response->isSuccessful()) {
            return redirect($response->redirect_url);
        } else {
            return redirect()->back()->withErrors(__('messages.payment_initiation_failed ') . $response->message);
        }
    }

    protected function AirtelPayment(Request $request)
    {
        $baseURL = env('APP_URL');
        $price = $request->input('price');
        $plan_id = $request->input('plan_id');
        $subcriptionType = $request->input('subcriptionType');
        $response = $this->makeAirtelPaymentRequest($price, $plan_id, $subcriptionType);
        if ($response->isSuccessful()) {
            return redirect($response->redirect_url);
        } else {
            return redirect()->back()->withErrors(__('messages.payment_initiation_failed ') . $response->message);
        }
    }

    protected function PhonePePayment(Request $request)
    {
        $baseURL = env('APP_URL');
        $price = $request->input('price');
        $plan_id = $request->input('plan_id');
        $subcriptionType = $request->input('subcriptionType');
        $response = $this->makePhonePePaymentRequest($price, $plan_id, $subcriptionType);

        if ($response->isSuccessful()) {
            return redirect($response->payment_url);
        } else {
            return redirect()->back()->withErrors(__('messages.payment_initiation_failed ') . $response->message);
        }
    }

    protected function MidtransPayment(Request $request)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');

        $price = $request->input('price');
        $plan_id = $request->input('plan_id');
        $subcriptionType = $request->input('subcriptionType');
        $transactionDetails = [
            'order_id' => uniqid(),
            'gross_amount' => $price,
        ];

        $customerDetails = [
            'first_name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ];

        $transaction = [
            'transaction_details' => $transactionDetails,
            'customer_details' => $customerDetails,
        ];

        try {
            $snapToken = Snap::getSnapToken($transaction);
            return response()->json(['snapToken' => $snapToken]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(__('messages.payment_initiation_failed ') . $e->getMessage());
        }
    }

    private function getAccessToken()
    {
        $clientId = GetpaymentMethod('paypal_client_id');
        $clientSecret = GetpaymentMethod('paypal_secret');

        $client = new Client();
        $response = $client->post('https://api.sandbox.paypal.com/v1/oauth2/token', [
            'auth' => [$clientId, $clientSecret],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['access_token'];
    }

    private function createPayment($accessToken, $price, $planId, $vendorId, $success_url)
    {
        $baseURL = env('APP_URL');
        $client = new Client();
        $response = $client->post('https://api.sandbox.paypal.com/v1/payments/payment', [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'intent' => 'sale',
                'payer' => [
                    'payment_method' => __('messages.lbl_paypal'),
                ],
                'transactions' => [
                    [
                        'amount' => [
                            'total' => $price,
                            'currency' => 'USD',
                        ],
                        'description' => __('messages.payment_for_plan') . $planId,
                        'custom' => $vendorId,
                    ]
                ],
                'redirect_urls' => [
                    'return_url' => $success_url . '?gateway=paypal',
                    'cancel_url' => $baseURL . '/payment/cancel',
                ],
            ],
        ]);
        return json_decode($response->getBody(), true);
    }




    //handle payment

    protected function handleStripeSuccess(Request $request)
    {
        try {
            $sessionId = $request->input('session_id');
            $stripe = new StripeClient(GetpaymentMethod('stripe_secretkey'));
            $session = $stripe->checkout->sessions->retrieve($sessionId);
            $totalAmount = $session->amount_total / 100;
            // Return payment details instead of directly handling
            return [
                'status' => __('messages.success'),
                'data' => [
                    'plan_id' => $session->metadata->plan_id,
                    'amount' => $totalAmount,
                    'payment_type' => __('messages.lbl_stripe'),
                    'transaction_id' => $session->payment_intent,
                    'vendor_id' => $session->metadata->vendor_id,
                    'subcriptionType' => $session->metadata->subcriptionType,
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => __('messages.error'),
                'message' => $e->getMessage()
            ];
        }
    }


    //paystack success
    protected function handlePaystackSuccess(Request $request)
    {
        try {
            $reference = $request->input('reference');
            $paystackSecretKey = GetpaymentMethod('paystack_secretkey');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $paystackSecretKey,
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");
            $responseBody = $response->json();

            if ($responseBody['status']) {
                return [
                    'status' => __('messages.success'),
                    'data' => [
                        'plan_id' => $responseBody['data']['metadata']['plan_id'],
                        'amount' => $responseBody['data']['amount'] / 100,
                        'payment_type' => __('messages.lbl_paystack'),
                        'transaction_id' => $responseBody['data']['id'],
                        'vendor_id' => $responseBody['data']['metadata']['vendor_id']
                    ]
                ];
            }

            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_verification_failed') . $responseBody['message']
            ];
        } catch (\Exception $e) {
            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_verification_failed') . $e->getMessage()
            ];
        }
    }

    //paypal success
    protected function handlePayPalSuccess(Request $request)
    {
        try {
            $paymentId = $request->input('paymentId');
            $payerId = $request->input('PayerID');

            $apiContext = new ApiContext(
                new OAuthTokenCredential(
                    GetpaymentMethod('paypal_client_id'),
                    GetpaymentMethod('paypal_secret')
                )
            );

            $payment = Payment::get($paymentId, $apiContext);
            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);

            $result = $payment->execute($execution, $apiContext);

            if ($result->getState() == 'approved') {
                return [
                    'status' => __('messages.success'),
                    'data' => [
                        'plan_id' => $result->transactions[0]->item_list->items[0]->sku,
                        'amount' => $result->transactions[0]->amount->total,
                        'payment_type' => __('messages.lbl_paypal'),
                        'transaction_id' => $paymentId,
                        'vendor_id' => $result->transactions[0]->custom ?? null
                    ]
                ];
            }

            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_not_approve')
            ];
        } catch (\Exception $e) {
            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_verification_failed') . $e->getMessage()
            ];
        }
    }

    // flutter success
    protected function handleFlutterwaveSuccess(Request $request)
    {
        try {
            $tx_ref = $request->input('tx_ref');
            $flutterwaveKey = GetpaymentMethod('flutterwave_secret_key');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $flutterwaveKey,
            ])->get("https://api.flutterwave.com/v3/transactions/{$tx_ref}/verify");

            $responseBody = $response->json();

            if ($responseBody['status'] === 'success') {
                return [
                    'status' => __('messages.success'),
                    'data' => [
                        'plan_id' => $responseBody['data']['metadata']['plan_id'],
                        'amount' => $responseBody['data']['amount'],
                        'payment_type' => 'flutterwave',
                        'transaction_id' => $responseBody['data']['id'],
                        'vendor_id' => $responseBody['data']['metadata']['vendor_id'] ?? null
                    ]
                ];
            }

            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_verification_failed') . $responseBody['message']
            ];
        } catch (\Exception $e) {
            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_verification_failed') . $e->getMessage()
            ];
        }
    }


    //razor pay
    protected function handleRazorpaySuccess(Request $request)
    {
        $paymentId = $request->input('razorpay_payment_id');
        $razorpayOrderId = session('razorpay_order_id');
        $plan_id = $request->input('plan_id');

        $razorpayKey = 'rzp_test_CLw7tH3O3P5eQM';
        $razorpaySecret = 'rzp_test_CLw7tH3O3P5eQM';
        $api = new \Razorpay\Api\Api($razorpayKey, $razorpaySecret);
        $payment = $api->payment->fetch($paymentId);

        if ($payment['status'] == 'captured') {
            return $this->handlePaymentSuccess($plan_id, $payment['amount'] / 100, 'razorpay', $paymentId);
        } else {
            return redirect('/')->with('error', __('messages.payment_fail') . $payment['error_description']);
        }
    }


    protected function handlePhonePeSuccess(Request $request)
    {
        try {
            $transactionId = $request->input('transaction_id');
            $merchantTransactionId = $request->input('merchantTransactionId');
            $phonePeKey = GetpaymentMethod('phonepe_key');

            $response = Http::withHeaders([
                'X-VERIFY-TOKEN' => $phonePeKey
            ])->get("https://api.phonepe.com/apis/hermes/pg/v1/status/{$merchantTransactionId}");

            $responseBody = $response->json();

            if ($responseBody['success'] && $responseBody['code'] === 'PAYMENT_SUCCESS') {
                return [
                    'status' => __('messages.success'),
                    'data' => [
                        'plan_id' => $responseBody['data']['merchantMetadata']['plan_id'],
                        'amount' => $responseBody['data']['amount'] / 100,
                        'payment_type' => __('messages.lbl_phonepe'),
                        'transaction_id' => $transactionId,
                        'vendor_id' => $responseBody['data']['merchantMetadata']['vendor_id']
                    ]
                ];
            }

            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_verification_failed') . ($responseBody['message'] ?? __('messages.unknown_error'))
            ];
        } catch (\Exception $e) {
            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_verification_failed') . $e->getMessage()
            ];
        }
    }

    protected function handleCinetSuccess(Request $request)
    {
        try {
            $transactionId = $request->input('transaction_id');
            $cinetKey = GetpaymentMethod('cinet_key');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $cinetKey
            ])->get("https://api.cinetpay.com/v1/payment/check/{$transactionId}");

            $responseBody = $response->json();

            if ($responseBody['status'] == 'success') {
                return [
                    'status' => __('messages.success'),
                    'data' => [
                        'plan_id' => $responseBody['metadata']['plan_id'],
                        'amount' => $responseBody['amount'],
                        'payment_type' => __('messages.lbl_cinet'),
                        'transaction_id' => $transactionId,
                        'vendor_id' => $responseBody['metadata']['vendor_id']
                    ]
                ];
            }

            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_verification_failed') . ($responseBody['message'] ?? __('messages.unknown_error'))
            ];
        } catch (\Exception $e) {
            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_verification_failed') . $e->getMessage()
            ];
        }
    }

    protected function handleSadadSuccess(Request $request)
    {
        try {
            $transactionId = $request->input('transaction_id');
            $sadadKey = GetpaymentMethod('sadad_key');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $sadadKey
            ])->get("https://api.sadad.com/v1/transactions/{$transactionId}");

            $responseBody = $response->json();

            if ($responseBody['status'] === 'paid') {
                return [
                    'status' => __('messages.success'),
                    'data' => [
                        'plan_id' => $responseBody['metadata']['plan_id'],
                        'amount' => $responseBody['amount'],
                        'payment_type' => __('messages.sadad'),
                        'transaction_id' => $transactionId,
                        'vendor_id' => $responseBody['metadata']['vendor_id']
                    ]
                ];
            }

            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_verification_failed') . ($responseBody['message'] ?? __('messages.unknown_error'))
            ];
        } catch (\Exception $e) {
            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_verification_failed') . $e->getMessage()
            ];
        }
    }

    protected function handleAirtelSuccess(Request $request)
    {
        try {
            $transactionId = $request->input('transaction_id');
            $airtelKey = GetpaymentMethod('airtel_key');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $airtelKey
            ])->get("https://api.airtel.com/merchant/v1/payments/{$transactionId}");

            $responseBody = $response->json();

            if ($responseBody['status'] === 'SUCCESS') {
                return [
                    'status' => __('messages.success'),
                    'data' => [
                        'plan_id' => $responseBody['metadata']['plan_id'],
                        'amount' => $responseBody['transaction']['amount'],
                        'payment_type' => __('messages.lbl_airtel_money'),
                        'transaction_id' => $transactionId,
                        'vendor_id' => $responseBody['metadata']['vendor_id']
                    ]
                ];
            }

            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_verification_failed') . ($responseBody['message'] ?? __('messages.unknown_error'))
            ];
        } catch (\Exception $e) {
            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_verification_failed') . $e->getMessage()
            ];
        }
    }

    protected function handleMidtransSuccess(Request $request)
    {
        try {
            $transactionId = $request->input('order_id');
            $midtransKey = GetpaymentMethod('midtrans_server_key');

            Config::$serverKey = $midtransKey;
            $status = \Midtrans\Transaction::status($transactionId);

            if ($status->transaction_status === 'settlement') {
                return [
                    'status' => __('messages.success'),
                    'data' => [
                        'plan_id' => $status->metadata->plan_id,
                        'amount' => $status->gross_amount,
                        'payment_type' => __('messages.midtrans'),
                        'transaction_id' => $transactionId,
                        'vendor_id' => $status->metadata->vendor_id
                    ]
                ];
            }

            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_verification_failed_transaction')  . $status->transaction_status
            ];
        } catch (\Exception $e) {
            return [
                'status' => __('messages.error'),
                'message' => __('messages.payment_verification_failed') . $e->getMessage()
            ];
        }
    }
}
