<?php

namespace Modules\Prescription\Trait;

use App\Models\User;
use App\Jobs\BulkNotification;

trait PrescriptionTrait
{
    public function getTimeZone()
    {
        $timezone = \App\Models\Setting::first();

        return $timezone->default_time_zone ?? 'UTC';
    }

    public function send_mail($user_id)
    {
        try {
            $user = User::where('id', $user_id)->first();

            $subject = __('messages.pending_prescription_notify');
            $message = __('messages.book_from_prescription');

            \Mail::send('subscription.subscription_email',
                [
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                    'subject' => $subject,
                    'phone_no' => $user['phone_no'],
                    'message' => $message,
                ], function ($message) use ($user) {
                    $message->from($user->email);
                    $message->to(env('MAIL_FROM_ADDRESS'));
                });

            return $messagedata = __('messages.contact_us_greetings');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());

            return $messagedata = __('messages.something_wrong');
        }
    }
    public function sendPrescriptionsuggestion($type,$response){

        $data = mail_footer($type, $response);
        
        $data['Prescription'] = $response;

        BulkNotification::dispatch($data);
    }
}
