<?php

namespace App\Trait;

use App\Models\MailTemplates;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\NotificationTemplate;
use App\Models\Setting;
use App\Models\Country;
use Modules\Appointment\Models\Appointment;
trait NotificationTrait
{
    function sendNotification($data)
    {
        
        date_default_timezone_set($app_setting->time_zone ?? 'UTC');
        $admin = User::where('user_type', 'admin')->first();
        $adminId=$admin->id;
        $data['platform_name']=setting('app_name') ?? 'kivilab';
        if(!empty($data['user_id']) && $data['user_id']){
            $userId = [$data['user_id']];
            $data['user_name'] = $data['user']->full_name ?? '';
        }
        if(!empty($data['collector_id']) && $data['collector_id']){
            $collectorId = [$data['collector_id']];
            $data['collector_name'] = $data['collector']->full_name ?? '';
        }
        if(!empty($data['vendor_id']) && $data['vendor_id']){
            $vendorId = [$data['vendor_id']];
            $data['vendor_name'] = $data['vendor']->full_name ?? '';
        }

        // Get appointment data
        if (isset($data['appointment_id'])  && $data['appointment_id'] ) {
            $appointment = Appointment::findorfail($data['appointment_id']);
        
            $id = $appointment->id;
            $vendorId= [$appointment->vendor_id] ?? '';
            if(!empty($data['collector_id']) && $data['collector_id']){
                $collectorId = [$data['collector_id']];
                $data['collector_name'] = $data['collector'] ?? '';
            }
  
            $userId = [$appointment->customer_id];
            $data['user_id'] = $appointment->customer_id;
            $data['vendor_id'] = $appointment->vendor_id ?? '';
            $data['vendor_name']=  $appointment->vendor->full_name ?? '';
            $data['test_name'] = $appointment->getTestAttribute()->name ?? '';
            $data['lab_name'] = $appointment->lab->name ?? '';
            $data['user_name'] = $appointment->user->full_name ?? '';
            $data['date_time'] = $appointment->appointment_date . ' ' . $appointment->appointment_time;
            $data['platform_name'] = $app_setting->app_name ?? '';
            $data['user_contact']=$appointment->user->mobile??'-'; 
            $data['notification_group'] = 'appointment';
            // Add collector info if assigned
            if ($appointment->appointmentCollectorMapping) {
                $data['collector_id'] = $appointment->appointmentCollectorMapping->collector_id;
                $data['user_address'] = $appointment->address;
            }

            // Add OTP if generated
            if (isset($data['otp'])) {
                $data['otp_code'] = $data['otp'];
            }
        }

        //subscription data 
        $data['type'] = $data['notification_type'];
        $subscription = isset($data['subscription']) ? $data['subscription'] : null;
        if (isset($subscription) && $subscription != null) {
            $data['id'] = $subscription['id'];
            $data['user_id'] = $subscription['user_id'];
            $data['plan_id'] = $subscription['plan_id'];
            $data['name'] = $subscription['name'];
            $data['vendor_name']=$subscription->user->full_name;
            $data['plan_name']=$subscription->plan->name;
            $data['identifier'] = $subscription['identifier'];
            $data['type'] = $subscription['type'];
            $data['status'] = $subscription['status'];
            $data['amount'] = $subscription['amount'];
            $data['plan_type'] = $subscription['plan_type'];
            $data['username'] = $subscription['user']->full_name;
            $data['notification_group'] = 'subscription';
            $data['site_url'] = env('APP_URL');
            $vendorId = [$subscription['user_id']];
            unset($data['subscription']);
            $data['user_type']='vendor' ;
        }
        
        //presceription
        if (isset( $data['prescription'])) {
            $prescription = $data['prescription'];
            $userId = [$prescription->user_id];
            $data['user_id'] = $prescription->user_id;
            $data['user_name'] = $prescription->user->full_name ?? '';
            $data['test_name'] = $prescription->test_names ?? '';
            $data['notification_group'] = 'prescription';
        }

        if (isset($data['helpdesk_id'])  && $data['helpdesk_id'] ) {
            $data['sender_name'] =  $data['sender_name'] ?? '';
            $data['receiver_name'] =  $data['receiver_name'] ?? '';
            $data['receiver_type'] =  $data['receiver_type'] ?? '';
            $data['helpdesk_id'] =  $data['helpdesk_id'] ?? '';
            $data['subject'] =  $data['subject'] ?? '';
            $userId = [$data['receiver_id']];
            $vendorId = [$data['receiver_id']];
            $collectorId = [$data['receiver_id']];
            $data['notification_group'] = 'helpdesk'; 
        }

        if (isset($data['wallet'])  && $data['wallet'] ) {
            $wallet = $data['wallet'];
            $userId = [$wallet->user_id];
            $data['user_type']='user';
        }


        
        $mailable = \Modules\NotificationTemplate\Models\NotificationTemplate::where('type', $data['notification_type'])->with('defaultNotificationTemplateMap')->first();
        
        if ($mailable != null && $mailable->to != null) {
            $mails = json_decode($mailable->to);
         
            if (in_array($mailable->type, ['closed_helpdesk', 'reply_helpdesk'])) {
                $mails = is_array($data['receiver_type']) ? $data['receiver_type'] : explode(',', $data['receiver_type']);
            }

            
            
    foreach ($mails as $key => $mailTo) {
        
        switch ($mailTo) {
             
            case 'admin':
                $admin = \App\Models\User::role('admin')->first();
                $data['person_id'] = $admin->id;
                if (isset($admin->email)) {
                    try {
                        $data['user_type'] = $mailTo;
                        $admin->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                        
                    } catch (\Exception $e) {
                        Log::error($e);
                    }
                }
                break;
            case 'vendor':
                if (isset($vendorId)) {
                    foreach ($vendorId as $id) {
                        $employee = \App\Models\User::find($id);
                        $data['person_id'] = $employee->id;
                        
                        if (isset($employee->email)) {
                            try {
                                $data['user_type'] = $mailTo;
                  
                                $employee->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                            } catch (\Exception $e) {
                                Log::error($e);
                            }
                        }
                    }
                }
                break;
            case 'collector':
                if (isset($collectorId)) {
                  
                    foreach ($collectorId as $id) {
                  
                        $employee = \App\Models\User::find($id);
                        $data['person_id'] = $employee->id;
                        if (isset($employee->email) && $employee->user_type == 'collector') {
                            try {
                                $data['user_type'] = $mailTo;
                                $employee->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                            } catch (\Exception $e) {
                                Log::error($e);
                            }
                        }
                    }
                }
                break;
            case 'user':

                if (isset($userId)) {
                    foreach ($userId as $id) {
                    $user = \App\Models\User::find($id);
                      
                    if($user){
                        $data['person_id'] = $user->id;
                        try {
                            $data['user_type'] = $mailTo;
                            $user->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                            
                        } catch (\Exception $e) {
                            Log::error($e);
                        }
                  }
                }
                }
                break;
        }
    }
}

    }
}
