<?php

namespace Modules\Vendor\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Modules\Subscriptions\Models\Plan;
use Modules\Subscriptions\Models\SubscriptionTransactions;
use Modules\Subscriptions\Models\Subscription;
use Modules\Subscriptions\Trait\SubscriptionTrait;
use Modules\Tax\Models\Tax;
use GuzzleHttp\Client;
use PayPal\Api\Payment;
use Stripe\StripeClient;
use PayPal\Api\PaymentExecution;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\User;
use App\Trait\NotificationTrait;
use App\Trait\PaymentTrait;
class VendorPaymentController extends Controller
{
    use NotificationTrait;
    use SubscriptionTrait;
    use PaymentTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('vendor::index');
    }




    public function processPayment(Request $request)
    {     
        $paymentMethod = $request->input('payment_method');
        $price = $request->input('price');
        $price = intval(round($price * 100)); 
        $vendor_id=$request->input('vendor_id');
        $subscriptionType=$request->input('subscription_type');
        $request->merge([
            'success_url' => route('payment.success')
        ]);
     
        $paymentHandlers = [
            'stripe' => 'StripePayment',
            'razorpay' => 'RazorpayPayment',
            'paystack' => 'PaystackPayment',
            'paypal' => 'PayPalPayment',
            'flutterwave' => 'FlutterwavePayment',
            'cinet' => 'CinetPayment',
            'sadad' => 'SadadPayment',
            'airtel' => 'AirtelPayment',
            'phonepe' => 'PhonePePayment',
            'midtrans' => 'MidtransPayment',
        ];

        if (array_key_exists($paymentMethod, $paymentHandlers)) {
        
            return $this->{$paymentHandlers[$paymentMethod]}($request, $price);
        }

        return redirect()->back()->withErrors('messages.invalid_payment_method');
    }



    public function paymentSuccess(Request $request)
    {
        $gateway = $request->input('gateway');

        switch ($gateway) {
            case 'stripe':
                $result = $this->handleStripeSuccess($request);              
            if ($result['status'] === 'success') {
                return $this->handlePaymentSuccess(
                    $result['data']['plan_id'],
                    $result['data']['amount'],
                    $result['data']['payment_type'],
                    $result['data']['transaction_id'],
                    $result['data']['vendor_id'],
                    $result['data']['subcriptionType']??'new',
                );
            }
            case 'razorpay':
                return $this->handleRazorpaySuccess($request);
            case 'paystack':
                $result = $this->handlePaystackSuccess($request);
                if ($result['status'] === 'success') {
                    return $this->handlePaymentSuccess(
                        $result['data']['plan_id'],
                        $result['data']['amount'],
                        $result['data']['payment_type'],
                        $result['data']['transaction_id'],
                        $result['data']['vendor_id']
                    );
                }
                return redirect()->back()->withErrors($result['message']);
            case 'paypal':
                $result = $this->handlePayPalSuccess($request);
                if ($result['status'] === 'success') {
                    return $this->handlePaymentSuccess(
                        $result['data']['plan_id'],
                        $result['data']['amount'],
                        $result['data']['payment_type'],
                        $result['data']['transaction_id'],
                        $result['data']['vendor_id']
                    );
                }
                return redirect()->back()->withErrors($result['message']);
    
            case 'flutterwave':
                $result = $this->handleFlutterwaveSuccess($request);
                if ($result['status'] === 'success') {
                    return $this->handlePaymentSuccess(
                        $result['data']['plan_id'],
                        $result['data']['amount'],
                        $result['data']['payment_type'],
                        $result['data']['transaction_id'],
                        $result['data']['vendor_id']
                    );
                }
                return redirect()->back()->withErrors($result['message']);
    
            case 'phonepe':
                $result = $this->handlePhonePeSuccess($request);
                if ($result['status'] === 'success') {
                    return $this->handlePaymentSuccess(
                        $result['data']['plan_id'],
                        $result['data']['amount'],
                        $result['data']['payment_type'],
                        $result['data']['transaction_id'],
                        $result['data']['vendor_id']
                    );
                }
                return redirect()->back()->withErrors($result['message']);
    
            case 'cinet':
                $result = $this->handleCinetSuccess($request);
                if ($result['status'] === 'success') {
                    return $this->handlePaymentSuccess(
                        $result['data']['plan_id'],
                        $result['data']['amount'],
                        $result['data']['payment_type'],
                        $result['data']['transaction_id'],
                        $result['data']['vendor_id']
                    );
                }
                return redirect()->back()->withErrors($result['message']);
    
            case 'sadad':
                $result = $this->handleSadadSuccess($request);
                if ($result['status'] === 'success') {
                    return $this->handlePaymentSuccess(
                        $result['data']['plan_id'],
                        $result['data']['amount'],
                        $result['data']['payment_type'],
                        $result['data']['transaction_id'],
                        $result['data']['vendor_id']
                    );
                }
                return redirect()->back()->withErrors($result['message']);
    
            case 'airtel':
                $result = $this->handleAirtelSuccess($request);
                if ($result['status'] === 'success') {
                    return $this->handlePaymentSuccess(
                        $result['data']['plan_id'],
                        $result['data']['amount'],
                        $result['data']['payment_type'],
                        $result['data']['transaction_id'],
                        $result['data']['vendor_id']
                    );
                }
                return redirect()->back()->withErrors($result['message']);
    
            case 'midtrans':
                $result = $this->handleMidtransSuccess($request);
                if ($result['status'] === 'success') {
                    return $this->handlePaymentSuccess(
                        $result['data']['plan_id'],
                        $result['data']['amount'],
                        $result['data']['payment_type'],
                        $result['data']['transaction_id'],
                        $result['data']['vendor_id']
                    );
                }
                return redirect()->back()->withErrors($result['message']);
    
            default:
                return redirect('/')->with('error', __('messages.invalid_payment_gateway'));        }
    }

    protected function handlePaymentSuccess($plan_id, $amount, $payment_type, $transaction_id,$vendor_id,$subscription_type='new')
    {
       
        $plan = Plan::findOrFail($plan_id);
        $start_date = now();
        $end_date = $this->get_plan_expiration_date($start_date, $plan->duration, $plan->duration_value);
        $taxes = Tax::active()->get();
        $totalTax = 0;
        foreach ($taxes as $tax) {
            if (strtolower($tax->type) == 'fixed') {
                $totalTax += $tax->value;
            } elseif (strtolower($tax->type) == 'percentage') {
                $totalTax += ($plan->price * $tax->value) / 100;
            }
        }
        if ($subscription_type == 'upgrade-plan') {
            Subscription::where('user_id', $vendor_id)
                ->where('status', ['active'])
                ->update(['status' => 'inactive']);
        }
        
        // Create the subscription
        $subscription = Subscription::create([
            'plan_id' => $plan_id,
            'user_id' => $vendor_id,
            'device_id' => 1,
            'start_date' => now(),
            'end_date' => $end_date,
            'status' => 'active',
            'amount' => $plan->price,
            'tax_amount' => $totalTax,
            'total_amount' => $amount,
            'name' => $plan->name,
            'identifier' => $plan->identifier,
            'type' => $plan->duration,
            'duration' => $plan->duration_value,
            'level' => $plan->level,
            'plan_type' => '',
            'discount_percentage' => $plan->discount_percentage,
          
        ]);
 
        // Create a subscription transaction
       $SubscriptionTransactions= SubscriptionTransactions::create([
            'user_id' =>$vendor_id,
            'amount' => $amount,
            'payment_type' => $payment_type,
            'payment_status' => 'paid',
            'tax_data' => $taxes->isEmpty() ? null : json_encode($taxes),
            'transaction_id' => $transaction_id,
            'subscriptions_id' => $subscription->id,
        ]);
       

       
        $user = User::find($vendor_id);
        if ($user) {
            $user->update(['is_subscribe' => 1]);
            Auth::login($user);
        }

        $activity_data = [
            'activity_type' => 'subscription_added',
            'notification_type'=>'subscription_added',
            'appointment_id' => $subscription->id,
            'subscription' => $subscription,
        ];
  
    if ($subscription_type == 'upgrade-plan') {
        return redirect()->route('backend.vendors.subscription-history')->with('success', __('messages.upgrade_plan_successfully'));
    } 
    return redirect()->route('vendor-registration', ['step' => 4,'vendor_id' => $vendor_id])
        ->with('success', __('messages.payment_complete'));
    
}

  

  

    protected function handleCinetSuccess(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $paymentStatus = $request->input('status');
        $planId = $request->input('plan_id');

        if ($paymentStatus !== 'success') {
            return redirect('/')->with('error', __('messages.payment_failed'));
        }

        return $this->handlePaymentSuccess($planId, $request->input('amount'), 'cinet', $transactionId);
    }

    protected function handleSadadSuccess(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $paymentStatus = $request->input('status');
        $plan_id = $request->input('plan_id');

        if ($paymentStatus !== 'success') {
            return redirect('/')->with('error', __('messages.payment_failed'));
        }

        return $this->handlePaymentSuccess($plan_id, $request->input('amount'), 'sadad', $transactionId);
    }

    public function midtransNotification(Request $request)
    {
        $payload = json_decode($request->getContent(), true);

        if ($payload['transaction_status'] === 'settlement') {
            $transactionId = $payload['order_id'];
            $plan_id = $payload['item_details'][0]['id'];
            $amount = $payload['gross_amount'];

            return $this->handlePaymentSuccess($plan_id, $amount, 'midtrans', $transactionId);
        }

        return response()->json(['status' => 'success']);
    }

    protected function makeSadadPaymentRequest($price, $plan_id)
    {
        $url = 'https://api.sadad.com/payment';
        $data = [
            'amount' => $price,
            'plan_id' => $plan_id,
            'callback_url' => env('APP_URL') . '/payment/success?gateway=sadad',
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->post($url, [
            'json' => $data,
            'headers' => [
                'Authorization' => 'Bearer ' . env('SADAD_API_KEY'),
            ]
        ]);

        return json_decode($response->getBody());
    }

    protected function handleAirtelSuccess(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $paymentStatus = $request->input('status');
        $planId = $request->input('plan_id');

        if ($paymentStatus !== 'success') {
            return redirect('/')->with('error', __('messages.payment_failed'));
        }

        return $this->handlePaymentSuccess($planId, $request->input('amount'), 'airtel', $transactionId);
    }

    protected function handlePhonePeSuccess(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $paymentStatus = $request->input('status');
        $planId = $request->input('plan_id');

        if ($paymentStatus !== 'success') {
            return redirect('/')->with('error', __('messages.payment_failed'));
        }

        return $this->handlePaymentSuccess($planId, $request->input('amount'), 'phonepe', $transactionId);
    }

    protected function makePhonePePaymentRequest($price, $plan_id)
    {
        $url = 'https://api.phonepe.com/apis/hermes/pg/v1/pay';
        $data = [
            'amount' => $price,
            'plan_id' => $plan_id,
            'callbackUrl' => env('APP_URL') . '/payment/success?gateway=phonepe',
            'currency' => 'INR',
        ];
        $client = new Client();
        $response = $client->post($url, [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-VERIFY-TOKEN' => env('PHONEPE_VERIFY_TOKEN'),
            ]
        ]);

        return json_decode($response->getBody());
    }
    protected function makeAirtelPaymentRequest($price, $plan_id)
    {
        $url = 'https://api.airtel.com/payment';
        $data = [
            'amount' => $price,
            'plan_id' => $plan_id,
            'callback_url' => env('APP_URL') . '/payment/success?gateway=airtel',
        ];

        $client = new Client();
        $response = $client->post($url, [
            'json' => $data,
            'headers' => [
                'Authorization' => 'Bearer ' . env('AIRTEL_API_KEY'),
            ]
        ]);

        return json_decode($response->getBody());
    }

    public function subscriptionPlan()
    {
        $vendor_id = auth()->id() ?? null;
        $plans = Plan::with('planLimitation')->where('status',1)->get();
        $activeSubscriptions = Subscription::where('user_id', auth()->id())->where('status', 'active')->where('end_date', '>', now())->orderBy('id','desc')->first();
        $currentPlanId = $activeSubscriptions ? $activeSubscriptions->plan_id : null;
        $subscriptions = Subscription::where('user_id', auth()->id())
        ->with('subscription_transaction')
        ->where('end_date', '<', now())
        ->get();
        $module_title=__('messages.subscription_plans');
        return view('vendor::backend.subscriptionplan', compact('plans','module_title','currentPlanId','vendor_id','activeSubscriptions'));
    }

    public function selectPlan(Request $request)
    {
       
        $planId = $request->input('plan_id');
        $planName = $request->input('plan_name');
        $vendor_id= $request->input('vendor_id');
        $subscriptionType= $request->input('subscription_type')??null;
        $plans = Plan::where('status',1)->get();
        $module_title = 'Payment Details';
      if($subscriptionType == 'upgrade-plan'){
      
        $redirectUrl = route('backend.subscriptionUpgradePlan', [
            'planId' => $planId,
            'vendor_id' => $vendor_id,
            'subscriptionType' => $subscriptionType,
            'module_title' => $module_title,
            'plans' =>$plans,

        ]);
        return response()->json(['success' => true, 'redirect_url' => $redirectUrl]);
      }
        $view = view('vendor::backend.VendorRegistration.subscriptionPayment', compact('plans', 'planId','vendor_id','subscriptionType'))->render();
        return response()->json(['success' => true, 'view' => $view]);
    }


    public function subscriptionUpgradePlan(Request $request){
        $planId = $request->input('planId');
        $vendor_id= $request->input('vendor_id');
        $subscriptionType= $request->input('subscriptionType')??null;
        $module_title = 'Payment Details';
        $plans = Plan::where('status',1)->get();
        return view('vendor::backend.subscriptionupgradePayment', compact('module_title','plans', 'planId','vendor_id','subscriptionType'))->render();
    }






    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vendor::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('vendor::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('vendor::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
