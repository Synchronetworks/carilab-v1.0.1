<?php

namespace Modules\Wallet\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Trait\NotificationTrait;
use Illuminate\Http\Request;
use Modules\Wallet\Models\WalletHistory;
use Modules\Wallet\Models\Wallet;
use Modules\Wallet\Models\WithdrawMoney;
use App\Models\User;
use Carbon\Carbon;

use Modules\Wallet\Trait\PayoutTrait;
use Modules\Wallet\Transformers\WalletHistoryResource;


class WalletsController extends Controller
{
    use NotificationTrait;
    use PayoutTrait;
  public function walletTopup(Request $request)
    {
        $request->validate([
            'amount' => 'required',
        ]);

        $user_id = $request->user_id ?? auth()->id();
        
        $wallet = Wallet::where('user_id', $user_id)->first();
    
        if (!$wallet) {
            $user = User::where('id', $user_id)->first();

            if ($user && $user->user_type == 'user' || $user->user_type = "collector") {
                $wallet = Wallet::create([
                    'title' => $user->full_name,
                    'user_id' => $user->id,
                    'amount' => 0,
                ]);
            } else {
                return comman_custom_response(['error' => __('messages.invalid_user_type')]);
            }
        }
        
        $wallet->amount += $request->amount;
        
        $wallet->save();

        $activity_data = [
            'activity_type' => 'wallet_top_up',
            'message' =>  __('messages.wallet_topup_successfully'),
            'notification_type' => 'wallet_top_up',
            'wallet' => $wallet,
            'credit_debit_amount' => $request->amount,
            'amount' => $wallet->amount,
            'transaction_type' => $request->transaction_type,
            'transaction_id' => $request->transaction_id,
        ];
        
        WalletHistory::create([
            'user_id' => $user_id,
            'activity_type' => 'wallet_top_up',
            'activity_message' =>  __('messages.wallet_topup_successfully'),
            'activity_data' => json_encode($activity_data),
            'datetime' => now()
        ]);
        $this->sendNotification($activity_data);
        
        $response = [
            'message' => trans('messages.wallet_top_up'),
            'data' => $wallet,
        ];

        return comman_custom_response($response);
    }

    public function getHistory(Request $request)
    {
        $user_id = $request->user_id ?? auth()->id();

        $wallet_history = WalletHistory::with('users')->where('user_id', $user_id)->orderBy('updated_at','desc');
        $per_page = config('constant.PER_PAGE_LIMIT');

        $orderBy = $request->orderby ? $request->orderby : 'asc';

        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page === 'all') {
                $per_page = $wallet_history->count();
            }
        }
        
        $wallet_history = $wallet_history->orderBy('id', $orderBy)->paginate($per_page);
        $items = WalletHistoryResource::collection($wallet_history);
        $wallet_balance = Wallet::where('user_id', $user_id)->value('amount');
        $response = [
            'pagination' => [
                'total_items' => $items->total(),
                'per_page' => $items->perPage(),
                'currentPage' => $items->currentPage(),
                'totalPages' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
                'next_page' => $items->nextPageUrl(),
                'previous_page' => $items->previousPageUrl(),
            ],
            'data' => $items,
            'available_balance' => $wallet_balance,
        ];

        return comman_custom_response($response);
    }

    public function withdrawMoney(Request $request)
    {
        $data = $request->except('_token');

        $payment_gateway = $data['payment_gateway'];
        $user_id = $data['user_id'];

        // Fetch user's wallet details
        $wallet = Wallet::where('user_id', $user_id)->first();

        if (!$wallet) {
            return response()->json([
                'status' => false,
                'message' => __('messages.wallet_not_found')
            ], 404);
        }

        if ($wallet->amount < $request->amount) {
            return response()->json([
                'status' => false,
                'message' => __('messages.insufficient_balance_withdraw')
            ], 400);
        }

        $payout_status = '';
        $status = '';

        // Check the payment method (bank)
        if ($data['payment_method'] === 'bank') {

            switch ($payment_gateway) {

                    // RazorpayX case
                case 'razorpayx':

                    // Call the  payout function from helpers.php
                    $response = $this->handleRazorpayPayout(new  Request($data));
                    if ($response == '') {
                        // Handle failure if response is empty
                        $data['bank_id'] = $data['bank_id'];
                        $data['payment_type'] = $payment_gateway;
                        $data['datetime'] = Carbon::now();
                        $data['status'] = 'failed';
                        WithdrawMoney::create($data);

                        return response()->json([
                            'status' => false,
                            'message' => __('messages.error_razorpayX_detail')
                        ], 500);
                    }

                    $payout_details = $response;

                    // Check if payout response is valid
                    if (isset($payout_details['status']) && $payout_details['status'] == 'processing') {
                        $data['status'] = 'paid';
                    }

                    // Check for Razorpay error and handle accordingly
                    if (isset($payout_details['error'])) {
                        // Safely access 'description' if it exists
                        $razorpay_message = isset($payout_details['error']['description']) ? $payout_details['error']['description'] : 'Unknown error occurred';

                        if (isset($payout_details['error']['code']) && $payout_details['error']['code'] == 'BAD_REQUEST_ERROR') {
                            return response()->json([
                                'status' => false,
                                'message' => __('messages.razorpay_error')
                            ], 406);
                        }

                        // If error is not related to bad request, mark the transaction as failed
                        $data['bank_id'] = $data['bank_id'];
                        $data['payment_type'] = $payment_gateway;
                        $data['datetime'] = Carbon::now();
                        $data['status'] = 'failed';
                        WithdrawMoney::create($data);

                        return response()->json([
                            'status' => false,
                            'message' => __('messages.razorpay_error') . $razorpay_message
                        ], 500);
                    }
                    break;
                case 'stripe':

                        $response= $this->payout_stripe($data);
    
                        if($response==''){
    
                            return comman_message_response(trans('messages.stripe_details'));
    
                        }
                        else{
    
                            $status = $response->status;
    
                            if($status==400){
    
                                $error_message = $response->code;
    
                                $data['bank_id'] = $data['bank'];
                                $data['payment_type'] = $payment_gateway;
                                $data['datetime'] = Carbon::now();
                                $data['status'] = 'failed';
                                $result = WithdrawMoney::create($data);
    
                                return comman_message_response(trans('messages.stripe_message',['stripe_message' => $error_message]));
    
                            }
                            else{
                                $payout_id=$response['id'];
    
                                $status='';
    
                                if($payout_id!=''){
    
                                    $status="paid";
                                }
    
                                $data['bank_id']=$data['bank'];
                                $data['status']=$status;
                                $data['paid_date']=Carbon::now();
    
                            }
                        }
                    break;
            }
        }

        // Save withdrawal request in the database
        $data['bank_id'] = $data['bank_id'];
        $data['payment_type'] = $payment_gateway;
        $data['datetime'] = Carbon::now();
        $result =WithdrawMoney::create($data);

        // Deduct the withdrawal amount from the wallet balance
        $wallet->amount -= $data['amount'];
        $wallet->save();

        $activity_message = __('messages.withdraw') . getCurrencySymbol() . $data['amount'];


        $activity_data = [
            'id' => $result->id,
            'type' => 'wallet',
            'wallet' => $wallet,
            'activity_type' => 'withdraw_money',
            'notification_type' => 'withdraw_money',
            'user_id' => $data['user_id'],
            'amount' => $data['amount'],
        ];
        // Prepare data for the WalletHistory entry
        $history_data = [
            'user_id' => $wallet->user_id,
            'datetime' => now(),
            'activity_type' => 'wallet_debit', // Debit for withdrawal
            'activity_message' => $activity_message,
            'activity_data' => json_encode($activity_data),
        ];
        // Create an entry in the WalletHistory table for the withdrawal
        WalletHistory::create($history_data);
        $this->sendNotification($activity_data);
        // Return response to the user (API or Web)
        return response()->json([
            'status' => true,
            'message' => __('messages.money_transfer')
        ], 200);
    }
   
}
