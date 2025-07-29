<?php

namespace Modules\Wallet\Trait;

use App\Models\User;

use Illuminate\Http\Request;
use Stripe\StripeClient;
use GuzzleHttp\Client;
use Modules\Bank\Models\Bank;
use Stripe\Exception\ApiErrorException;


trait PayoutTrait
{
    function payout_stripe($data){

        $stripe_data = new StripeClient(GetpaymentMethod('stripe_secretkey'));

        $secret_key=GetpaymentMethod('stripe_secretkey');

        $currency=GetcurrentCurrency();
        $countryData = DB::table('countries')
                   ->where('currency_code', $currency) 
                   ->select('code')
                   ->first();
        $country=$countryData->code ?? 'IN';

        $user_id=$data['user_id'];
        $payout_amount=$data['amount'];
        $bank_id=$data['bank_id'];
        $bank_details=Bank::where('id',$bank_id)->first();
        $bank_name=$bank_details['bank_name'];
        $account_number=$bank_details['account_no'];
        $ifsc=$bank_details['ifsc_no'];
        $stripe_account=$bank_details['stripe_account'];
        if($stripe_account ==''){
            $userDetails=User::where('id',$data['user_id'])->first();
            $email=$userDetails['email'];
            $first_name=$userDetails['first_name'];
            $last_name=$userDetails['last_name'];
            $contact_number=$userDetails['contact_number'];
            $user_type=$userDetails['user_type'];
            $current_datetime=time();
            $ip_address=file_get_contents('https://api.ipify.org');
            $stripe = new \Stripe\StripeClient($secret_key);

            $stripedata=$stripe->accounts->create(
            [
                'country' => $country,
                'type' => 'standard',
                'bank_account' => [
                    'account_number' =>$account_number ?? '000123456789',// $account_number,
                    'country' => $country,
                    'account_holder_name' => $first_name.$last_name,
                    'routing_number' =>$ifsc ?? 'ABCD0123456', //$ifsc
                    'currency' => $currency,
                ],

                'capabilities' => [
                    'transfers' => [
                        'requested' => true
                    ]
                ],
                'business_type' => 'individual',
                'country' => $country,
                'email' => $email,
                'individual' => [
                    'first_name' => $first_name,
                    'last_name' => $last_name
                ],
                'business_profile' => [
                    'name' => $first_name.$last_name,
                    'url' => 'demo.com'
                ],
                'tos_acceptance' => [
                    'date' =>$current_datetime,
                    'ip' => $ip_address
                ]
            ]
            );

            $stripe_account= $stripedata['id'];
            Bank::where('id',$bank_id)->update(['stripe_account'=>$stripe_account]);
   

        }
        $data=[
            'secret_key'=>$secret_key,
            'amount'=>$payout_amount,
            'currency'=>$currency,
            'stripe_account'=>$stripe_account
        ];

    $bank_transfer=$this->create_stripe_transfer($data);
    return $bank_transfer;
        }



function create_stripe_transfer($data){
        
        \Stripe\Stripe::setApiKey($data['secret_key']);
        $transfer = \Stripe\Transfer::create([
            "amount" => $data['amount']*100,
            "currency" =>  $data['currency'],
            "destination" =>$data['stripe_account'],
        ]);
        $payout=$this->create_bank_tranfer($data);
        return $payout;

}

function create_bank_tranfer($data){

    try{

        \Stripe\Stripe::setApiKey($data['secret_key']);

        $payout = \Stripe\Payout::create([
        'amount' =>$data['amount']*100,
        'currency' => $data['currency'],
        ], [
        'stripe_account' => $data['stripe_account'],

        ]);

        return $payout;

        }catch(ApiErrorException $e){


        $error= $e->getError();


            if($error ==''){

            return $response='';

            }else{

            $error['status']=400;
            return $error;

            }

}
}
protected function handleRazorpayPayout(Request $request)
{
      
    try {
        $apiKey = GetpaymentMethod('razorpayx_publickey');
        $apiSecret = GetpaymentMethod('razorpayx_secretkey');
        $accountNumber = GetpaymentMethod('razorpayx_account_number');
        $client = new Client();
        $bank_details=Bank::where('id',$request->bank_id)->first();
        $userDetails=User::where('id',$request->user_id)->first();
        $currency='INR';
        $country=$currency;
     
        // Step 1: Create a Contact
        $response = $client->post('https://api.razorpay.com/v1/contacts', [
            'auth' => [$apiKey, $apiSecret],
            'json' => [
                'name' =>$userDetails['full_name'] ?? 'Default Name',
                'email' =>  $userDetails['email']?? 'default@email.com',
                'contact' => $userDetails['contact_number'] ?? '9999999999',
                'type' => 'employee',
                'reference_id' => 'Contact_' . time()
            ]
        ]);

        $contact = json_decode($response->getBody(), true);

        // Step 2: Create Fund Account
        $response = $client->post('https://api.razorpay.com/v1/fund_accounts', [
            'auth' => [$apiKey, $apiSecret],
            'json' => [
                'contact_id' => $contact['id'],
                'account_type' => 'bank_account',
                'bank_account' => [
                    'name' => $userDetails['full_name'] ?? 'Default Name',
                    'ifsc' =>$bank_details['ifsc_code'] ?? 'HDFC0001234',
                    'account_number' => $bank_details['account_number'] ?? '112233445566',
                ]
            ]
        ]);

        $fundAccount = json_decode($response->getBody(), true);

        // Step 3: Create Payout
        $response = $client->post('https://api.razorpay.com/v1/payouts', [
          
            'auth' => [$apiKey, $apiSecret],
            'json' => [
                'account_number'=>    $accountNumber,
                'fund_account_id' => $fundAccount['id'],
                'amount' => (int)($request->amount * 100), // Convert to paisa
                'currency' =>  'INR',
                'mode' => 'IMPS',
                'purpose' => 'payout',
                'queue_if_low_balance' => true,
                'reference_id' => 'Payout_' . time(),
                'narration' => 'Payout for ' . ($request->account_holder_name ?? 'User')
            ]
        ]);

          $payout = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'payout_id' => $payout['id'],
                'status' => $payout['status'],
                'amount' => $request->amount,
                'currency' =>  $currency,
            ];

        } catch (\GuzzleHttp\Exception\ClientException $e) {
          
            $errorResponse = json_decode($e->getResponse()->getBody()->getContents(), true);
            
            // Handle specific error cases
            $errorMessage = match ($errorResponse['error']['code'] ?? '') {
                'BAD_REQUEST_ERROR' => __('messages.currency_mismatch'),
                'LOW_BALANCE' => __('messages.insufficient_balance'),
                'INVALID_ACCOUNT_NUMBER' => __('messages.invalid_bank_details'),
                default => $errorResponse['error']['description'] ?? __('messages.payment_failed'),
            };
    
            return [
                'success' => false,
                'error' => $errorMessage,
                'code' => $errorResponse['error']['code'] ?? __('messages.error')
            ];
    
        } catch (\Exception $e) {
         
            return [
                'success' => false,
                'error' => __('messages.unexpected_error'),
                'message' => $e->getMessage()
            ];
        }    
   
}

    
}
