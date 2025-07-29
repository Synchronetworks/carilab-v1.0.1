<?php

namespace App\Http\Controllers\Auth\Trait;

use App\Events\Auth\UserLoginSuccess;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Modules\Collector\Models\CollectorVendorMapping;
use Modules\Wallet\Models\Wallet;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

trait AuthTrait
{
    protected function loginTrait($request)
    {
        $email = $request->email;
        $password = $request->password;
        $remember =$request->remember == '1';

        if (Auth::attempt(['email' => $email, 'password' => $password, 'status' => 1], $remember)) {
            $user = auth()->user();
          
            if($user->hasRole('user') || $user->hasRole('collector')){
                Auth::logout();
               return ['status' => 406, 'message' => __('messages.unauthorized_role')];
            }

            event(new UserLoginSuccess($request, auth()->user()));
            return ['status' => 200, 'message' => __('messages.login_successfully')];
        }
        return false;
    }

    protected function registerTrait($request, $model = null)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => [
                'nullable', // Email is optional
                'email',
                'max:191',
                Rule::unique('users')->ignore($request->id), // Ignore current user if updating
            ],
            'mobile' => [
                'nullable', // Mobile is optional
                'string',
                'min:10',
                'max:15',
                Rule::unique('users')->ignore($request->id),
            ],
            'password' => ['required', Password::defaults()],
        ],[
            'first_name.required' => __('messages.first_name_required'),
            'first_name.string' => __('messages.first_name_string'),
            'first_name.max' => __('messages.first_name_max'),
    
            'last_name.required' => __('messages.last_name_required'),
            'last_name.string' => __('messages.last_name_string'),
            'last_name.max' => __('messages.last_name_max'),
    
            'email.email' => __('messages.email_invalid'),
            'email.max' => __('messages.email_max'),
            'email.unique' => __('messages.email_unique'),
    
            'mobile.string' => __('messages.mobile_invalid'),
            'mobile.min' => __('messages.mobile_min'),
            'mobile.max' => __('messages.mobile_max'),
            'mobile.unique' => __('messages.mobile_unique'),
    
            'password.required' => __('messages.password_required'),
        ]);
        
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422));
        }
        
        // Ensure at least one of email or mobile is provided
        if (!$request->has('email') && !$request->has('mobile')) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => __('messages.email_mobile_provide'),
                'errors' => [
                    'email' => ['Either email or mobile is required.'],
                    'mobile' => ['Either email or mobile is required.']
                ]
            ], 422));
        }
        
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => __('messages.validation_failed'),
                'errors' => $validator->errors()
            ], 422));
        }
             

   
        $arr = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'login_type' => $request->login_type,
            'status' => 0,
        ];
        if($request->user_type != null && $request->user_type == 'user')
        {
            $arr['status'] = (!empty($request->status)) ? $request->status: 0;
        }
        
        if (isset($model)) {
            $user = $model::create($arr);
        } else {
            $user = User::create($arr);
        }
      
        $user->assignRole($user->user_type);
        $user->save();
        if(multivendor() == 1 &&  isset($request->vendor_id) && $request->vendor_id != null)
        {
            $collectorvendormapping = [
                'collector_id' => $user->id,    
                'vendor_id' => $request->vendor_id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ];
            $data = CollectorVendorMapping::create($collectorvendormapping);
        }
        if($user->user_type == 'user' || $user->user_type == 'collector'){
            $wallet = [
                'title' => $user->first_name.' '.$user->last_name,
                'user_id' => $user->id,
                'amount' => 0
            ];
            Wallet::create($wallet);
        }
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('config:cache');

        return $user;
    }
}
