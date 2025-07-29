<?php

namespace Modules\NotificationTemplate\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Constant\Models\Constant;
use Modules\NotificationTemplate\Models\NotificationTemplate;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
        $types = [
            // Registration notifications
            [
                'type' => 'notification_type',
                'value' => 'user_registration',
                'name' => 'New User Registration',
            ],
            [
                'type' => 'notification_type', 
                'value' => 'vendor_registration',
                'name' => 'New Vendor Registration',
            ],
            [
                'type' => 'notification_type',
                'value' => 'collector_registration', 
                'name' => 'New Collector Registration',
            ],

               // Appointment notifications
            [
                'type' => 'notification_type',
                'value' => 'new_appointment',
                'name' => 'New Appointment',
            ],
            [
                'type' => 'notification_type',
                'value' => 'accept_appointment',
                'name' => 'Appointment Accepted',
            ],
            [
                'type' => 'notification_type',
                'value' => 'cancel_appointment',
                'name' => 'Appointment Cancelled',
            ],
            [
                'type' => 'notification_type',
                'value' => 'reject_appointment',
                'name' => 'Appointment Rejected',
            ],
            [
                'type' => 'notification_type',
                'value' => 'otp_generated',
                'name' => 'Otp Generated',
            ],
            [
                'type' => 'notification_type',
                'value' => 'in_progress_appointment',
                'name' => 'Appointment In Progress',
            ],
            [
                'type' => 'notification_type',
                'value' => 'on_going_appointment',
                'name' => 'Appointment On Going',
            ],
            [
                'type' => 'notification_type',
                'value' => 'completed',
                'name' => 'Appointment Completed',
            ],
            [
                'type' => 'notification_type',
                'value' => 'collector_assigned',
                'name' => 'Collector Assigned',
            ],
            [
                'type' => 'notification_type',
                'value' => 'otp_verified',
                'name' => 'Otp Verified',
            ],
            // [
            //     'type' => 'notification_type',
            //     'value' => 'appointment_declined',
            //     'name' => 'Appointment Declined',
            // ],
            // [
            //     'type' => 'notification_type',
            //     'value' => 'appointment_completed',
            //     'name' => 'Appointment Completed',
            // ],

            // Payment notifications
            [
                'type' => 'notification_type',
                'value' => 'payment_pending',
                'name' => 'Payment Pending',
            ],
            [
                'type' => 'notification_type',
                'value' => 'payment_paid',
                'name' => 'Payment Paid',
            ],
           // Subscription notifications
            [
                'type' => 'notification_type',
                'value' => 'subscription_added',
                'name' => 'Subscription Added',
            ],
            [
                'type' => 'notification_type',
                'value' => 'subscription_expired',
                'name' => 'Subscription Expired',
            ],
            [
                'type' => 'notification_type',
                'value' => 'subscription_expiring',
                'name' => 'Subscription Near Expiry',
            ],
            [
                'type' => 'notification_type',
                'value' => 'subscription_renewed',
                'name' => 'Subscription Renewed',
            ],
            // Other notifications
            // [
            //     'type' => 'notification_type',
            //     'value' => 'send_otp',
            //     'name' => 'Send OTP',
            // ],

            //prescription
            [
                'type' => 'notification_type',
                'value' => 'prescription_pending',
                'name' => 'Prescription Pending',
            ],
            [
                'type' => 'notification_type',
                'value' => 'prescription_suggestion',
                'name' => 'Prescription Suggestion',
            ],
            //
            [
                'type' => 'notification_type',
                'value' => 'test_case_received',
                'name' => 'Test Case Received at Lab',
            ],

            [
                'type' => 'notification_type',
                'value' => 'test_case_not_received',
                'name' => 'Not Received at Lab',
            ],
            [
                'type' => 'notification_type',
                'value' => 'appointment_rescheduled',
                'name' => 'Reschedule Appointment',
            ],
            [
                'type' => 'notification_type',
                'value' => 'test_in_progress',
                'name' => 'Test Case in Progress',
            ],
            [
                'type' => 'notification_type',
                'value' => 'test_awaiting_validation',
                'name' => 'Test Case Awaiting Validation',
            ],
            [
                'type' => 'notification_type',
                'value' => 'test_validated',
                'name' => 'Test Case Validated',
            ],

            [
                'type' => 'notification_type',
                'value' => 'report_generated',
                'name' => 'Report Generated',
            ],
            [
                'type' => 'notification_type',
                'value' => 'report_sent',
                'name' => 'Send Report to User',
            ],
            //wallet
            [
                'type' => 'notification_type', 
                'value' => 'wallet_top_up',
                'name' => 'Wallet Top Up',
            ],
            [
                'type' => 'notification_type', 
                'value' => 'payment_completed',
                'name' => 'Payment Status',
            ],
            [
                'type' => 'notification_type',
                'value' => 'add_helpdesk',
                'name' => 'New Query Received!',
            ],
            [
                'type' => 'notification_type',
                'value' => 'closed_helpdesk',
                'name' => 'Query Closed Received!',
            ],
            [
                'type' => 'notification_type',
                'value' => 'reply_helpdesk',
                'name' => 'Query Replied!',
            ],
            [
                'type' => 'notification_type',
                'value' => 'vendor_payout',
                'name' => 'Vendor Payout!',
            ],    
            [
                'type' => 'notification_type',
                'value' => 'collector_payout',
                'name' => 'Collector Payout!',
            ],
            [
                'type' => 'notification_type',
                'value' => 'cancel_subscription',
                'name' => 'Cancel Subscription !',
            ],
            

  // Notification parameters

            // User Information
            [
                'type' => 'notification_param_button',
                'value' => 'user_name',
                'name' => 'User Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'vendor_name',
                'name' => 'Vendor Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'collector_name',
                'name' => 'Collector Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'user_contact',
                'name' => 'User Contact',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'user_address',
                'name' => 'User Address',
            ],

            // Test/Lab Information
            [
                'type' => 'notification_param_button',
                'value' => 'test_name',
                'name' => 'Test Name/Package',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'lab_name',
                'name' => 'Lab Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'date_time',
                'name' => 'Date/Time',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'new_date_time',
                'name' => 'New Date/Time',
            ],

            // Platform Information
            [
                'type' => 'notification_param_button',
                'value' => 'platform_name',
                'name' => 'Platform Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'otp_code',
                'name' => 'OTP Code',
            ],

            // Payment Information
            [
                'type' => 'notification_param_button',
                'value' => 'plan_name',
                'name' => 'Plan Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'amount',
                'name' => 'Amount',
            ],

          



            ////////////////////////////////////////////////////////////////////////////////////////////////////////////





            [
                'type' => 'notification_to',
                'value' => 'user',
                'name' => 'User',
            ],
            [
                'type' => 'notification_to',
                'value' => 'vendor',
                'name' => 'Vendor',
            ],
            [
                'type' => 'notification_to',
                'value' => 'collector',
                'name' => 'Collector',
            ],
            [
                'type' => 'notification_to',
                'value' => 'admin',
                'name' => 'Admin',
            ],
            [
                'type' => 'notification_to',
                'value' => 'admin',
                'name' => 'Admin',
            ],
        ];

        foreach ($types as $value) {
            Constant::updateOrCreate(['type' => $value['type'], 'value' => $value['value']], $value);
        }

        echo " Insert: notificationtempletes \n\n";

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('notification_templates')->delete();
        DB::table('notification_template_content_mapping')->delete();


   

    // User Registration Template
    $template = NotificationTemplate::create([
        'type' => 'user_registration',
        'name' => 'user_registration', 
        'label' => 'New User Registration',
        'status' => 1,
        'to' => '["user"]',
        'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
    ]);

    $template->defaultNotificationTemplateMap()->create([
        'language' => 'en',
        'notification_link' => '',
        'notification_message' => '',
        'user_type' => 'user',
        'status' => 1,
        'subject' => 'Welcome to [[ platform_name ]]!',
        'mail_subject' => 'Welcome to [[ platform_name ]]!',
        'whatsapp_subject' => 'Welcome to [[ platform_name ]]!', 
        'sms_subject' => 'Welcome to [[ platform_name ]]!',
        'template_detail' => '<p>Hi [[ user_name ]], welcome to [[ platform_name ]]! Your account has been successfully registered. Start appointment lab tests or packages today. We\'re here for your health needs.</p>',
        'whatsapp_template_detail' => 'Hi [[ user_name ]], welcome to [[ platform_name ]]! Your account has been successfully registered. Start appointment lab tests or packages today. We\'re here for your health needs.',
        'sms_template_detail' => 'Welcome to [[ platform_name ]], [[ user_name ]]! Your account is registered. Start appointment lab tests now.',
        'mail_template_detail' => '<p>Hi [[ user_name ]], welcome to [[ platform_name ]]! Your account has been successfully registered. Start appointment lab tests or packages today. We\'re here for your health needs.</p>'
    ]);

    // Vendor Registration Template 
    $template = NotificationTemplate::create([
        'type' => 'vendor_registration',
        'name' => 'vendor_registration',
        'label' => 'New Vendor Registration', 
        'status' => 1,
        'to' => '["vendor"]',
        'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
    ]);

    $template->defaultNotificationTemplateMap()->create([
        'language' => 'en',
        'notification_link' => '',
        'notification_message' => '',
        'user_type' => 'vendor',
        'status' => 1,
        'subject' => 'Vendor Account Registered',
        'mail_subject' => 'Vendor Account Registered',
        'whatsapp_subject' => 'Vendor Account Registered',
        'sms_subject' => 'Vendor Account Registered',
        'template_detail' => '<p>Hi [[ vendor_name ]], welcome to [[ platform_name ]]! Your account is successfully registered. Manage your labs, appointments, and earnings efficiently with our platform.</p>',
        'whatsapp_template_detail' => 'Hi [[ vendor_name ]], welcome to [[ platform_name ]]! Your account is successfully registered. Manage your labs, appointments, and earnings efficiently with our platform.',
        'sms_template_detail' => 'Hi [[ vendor_name ]], your vendor account on [[ platform_name ]] is active. Manage labs and appointments now.',
        'mail_template_detail' => '<p>Hi [[ vendor_name ]], welcome to [[ platform_name ]]! Your account is successfully registered. Manage your labs, appointments, and earnings efficiently with our platform.</p>'
    ]);

    // Collector Registration Template
    $template = NotificationTemplate::create([
        'type' => 'collector_registration',
        'name' => 'collector_registration',
        'label' => 'New Collector Registration',
        'status' => 1, 
        'to' => '["collector"]',
        'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
    ]);

    $template->defaultNotificationTemplateMap()->create([
        'language' => 'en',
        'notification_link' => '',
        'notification_message' => '',
        'user_type' => 'collector',
        'status' => 1,
        'subject' => 'Welcome to [[ platform_name ]]!',
        'mail_subject' => 'Welcome to [[ platform_name ]]!',
        'whatsapp_subject' => 'Welcome to [[ platform_name ]]!',
        'sms_subject' => 'Welcome to [[ platform_name ]]!',
        'template_detail' => '<p>Hi [[ collector_name ]], your collector account is registered. Start managing and completing assigned tasks seamlessly on our platform.</p>',
        'whatsapp_template_detail' => 'Hi [[ collector_name ]], your collector account is registered. Start managing and completing assigned tasks seamlessly on our platform.',
        'sms_template_detail' => 'Hi [[ collector_name ]], your account on [[ platform_name ]] is active. Start managing assigned tasks.',
        'mail_template_detail' => '<p>Hi [[ collector_name ]], your collector account is registered. Start managing and completing assigned tasks seamlessly on our platform.</p>'
    ]);


        // New Appointment Template
    $template = NotificationTemplate::create([
        'type' => 'new_appointment',
        'name' => 'new_appointment',
        'label' => 'New Appointment',
        'status' => 1,
        'to' => '["user","vendor","admin"]',
        'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
    ]);

    // For User
    $template->defaultNotificationTemplateMap()->create([
        'language' => 'en',
        'notification_link' => '',
        'notification_message' => '',
        'user_type' => 'user',
        'status' => 1,
        'subject' => 'New Appointment Confirmation',
        'mail_subject' => 'New Appointment Confirmation',
        'whatsapp_subject' => 'New Appointment Confirmation',
        'sms_subject' => 'Appointment Confirmed',
        'template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] at [[ lab_name ]] on [[ date_time ]] is confirmed. For queries, contact us.</p>',
        'whatsapp_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] at [[ lab_name ]] on [[ date_time ]] is confirmed. For queries, contact us.',
        'sms_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] at [[ lab_name ]] on [[ date_time ]] is confirmed. Check details in your account.',
        'mail_template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] at [[ lab_name ]] on [[ date_time ]] is confirmed. For queries, contact us.</p>'
    ]);

    // For Vendor
    $template->defaultNotificationTemplateMap()->create([
        'language' => 'en',
        'notification_link' => '',
        'notification_message' => '',
        'user_type' => 'vendor',
        'status' => 1, 
        'subject' => 'New Appointment Appointment',
        'mail_subject' => 'New Appointment Appointment',
        'whatsapp_subject' => 'New Appointment Appointment',
        'sms_subject' => 'New Appointment',
        'template_detail' => '<p>Hi [[ vendor_name ]], a new appointment has been made for [[ test_name ]] at your laboratory [[ lab_name ]].</p><p>Details:<br>Customer Name: [[ user_name ]]<br>Date/Time: [[ date_time ]]<br>Contact: [[ user_contact ]]</p><p>Please ensure the required resources are available.</p>',
        'whatsapp_template_detail' => 'Hi [[ vendor_name ]], a new appointment has been made for [[ test_name ]] at [[ lab_name ]]. Customer: [[ user_name ]], Date/Time: [[ date_time ]], Contact: [[ user_contact ]]',
        'sms_template_detail' => 'Hi [[ vendor_name ]], new appointment for [[ test_name ]] at [[ lab_name ]] for [[ date_time ]]. Please check your account for details.',
        'mail_template_detail' => '<p>Hi [[ vendor_name ]], a new appointment has been made for [[ test_name ]] at your laboratory [[ lab_name ]].</p><p>Details:<br>Customer Name: [[ user_name ]]<br>Date/Time: [[ date_time ]]<br>Contact: [[ user_contact ]]</p><p>Please ensure the required resources are available.</p>'
    ]);

    // For Admin
    $template->defaultNotificationTemplateMap()->create([
        'language' => 'en',
        'notification_link' => '',
        'notification_message' => '',
        'user_type' => 'admin',
        'status' => 1,
        'subject' => 'New Appointment Alert',
        'mail_subject' => 'New Appointment Alert', 
        'whatsapp_subject' => 'New Appointment Alert',
        'sms_subject' => 'New Appointment Alert',
        'template_detail' => '<p>Hi Admin, a new appointment has been confirmed in the system.</p><p>Details:<br>Customer Name: [[ user_name ]]<br>Test/Package: [[ test_name ]]<br>Lab: [[ lab_name ]]<br>Vendor: [[ vendor_name ]]<br>Date/Time: [[ date_time ]]</p><p>Please monitor the appointment status for updates.</p>',
        'whatsapp_template_detail' => 'Hi Admin, new appointment confirmed.\nDetails:\nCustomer: [[ user_name ]]\nTest: [[ test_name ]]\nLab: [[ lab_name ]]\nVendor: [[ vendor_name ]]\nDate/Time: [[ date_time ]]',
        'sms_template_detail' => 'Hi Admin, a new appointment for [[ test_name ]] at [[ lab_name ]] is confirmed. Check your admin panel for details.',
        'mail_template_detail' => '<p>Hi Admin, a new appointment has been confirmed in the system.</p><p>Details:<br>Customer Name: [[ user_name ]]<br>Test/Package: [[ test_name ]]<br>Lab: [[ lab_name ]]<br>Vendor: [[ vendor_name ]]<br>Date/Time: [[ date_time ]]</p><p>Please monitor the appointment status for updates.</p>'
    ]);

        // Appointment Accept Template
    $template = NotificationTemplate::create([
        'type' => 'accept_appointment',
        'name' => 'accept_appointment',
        'label' => 'Appointment Accepted',
        'status' => 1,
        'to' => '["user"]',
        'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
    ]);

    $template->defaultNotificationTemplateMap()->create([
        'language' => 'en',
        'notification_link' => '',
        'notification_message' => '',
        'user_type' => 'user',
        'status' => 1,
        'subject' => 'Your Appointment is Accepted!',
        'mail_subject' => 'Your Appointment is Accepted!',
        'whatsapp_subject' => 'Your Appointment is Accepted!',
        'sms_subject' => 'Appointment Accepted',
        'template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] has been accepted by [[ lab_name ]]. See you on [[ date_time ]].</p>',
        'whatsapp_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] has been accepted by [[ lab_name ]]. See you on [[ date_time ]].',
        'sms_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] has been accepted. Visit [[ lab_name ]] on [[ date_time ]].',
        'mail_template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] has been accepted by [[ lab_name ]]. See you on [[ date_time ]].</p>'
    ]);

            // Appointment Cancel Template
        $template = NotificationTemplate::create([
            'type' => 'cancel_appointment',
            'name' => 'cancel_appointment',
            'label' => 'Appointment Cancelled',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Appointment Cancelled',
            'mail_subject' => 'Appointment Cancelled',
            'whatsapp_subject' => 'Appointment Cancelled',
            'sms_subject' => 'Appointment Cancelled',
            'template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] at [[ lab_name ]] has been cancelled. Contact support for assistance.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] at [[ lab_name ]] has been cancelled. Contact support for assistance.',
            'sms_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] at [[ lab_name ]] is cancelled. Contact support for help.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] at [[ lab_name ]] has been cancelled. Contact support for assistance.</p>'
        ]);

                // Appointment Assign Template
        $template = NotificationTemplate::create([
            'type' => 'collector_assigned',
            'name' => 'collector_assigned',
            'label' => 'Appointment Assigned',
            'status' => 1,
            'to' => '["collector","user","vendor"]',  // Collector only
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'collector',
            'status' => 1,
            'subject' => 'Collector Assigned',
            'mail_subject' => 'Collector Assigned',
            'whatsapp_subject' => 'Collector Assigned',
            'sms_subject' => 'Collector Assigned',
            'template_detail' => '<p>Hi [[ collector_name ]], you\'ve been assigned a new appointment. Visit [[ user_address ]] on [[ date_time ]]. Details are in your dashboard.</p>',
            'whatsapp_template_detail' => 'Hi [[ collector_name ]], you\'ve been assigned a new appointment. Visit [[ user_address ]] on [[ date_time ]]. Details are in your dashboard.',
            'sms_template_detail' => 'Hi [[ collector_name ]], Collector assigned! Visit [[ user_address ]] on [[ date_time ]]. Check your dashboard for details.',
            'mail_template_detail' => '<p>Hi [[ collector_name ]], you\'ve been assigned a new appointment. Visit [[ user_address ]] on [[ date_time ]]. Details are in your dashboard.</p>'
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Collector Assigned!',
            'whatsapp_subject' => 'Collector Assigned!',
            'sms_subject' => 'Collector Assigned!',
            'mail_subject' => 'Collector Assigned!',
            'template_detail' => '<p>#[[ collector_name ]] -  has been assigned to new Appointment.</p>',
            'whatsapp_template_detail' => '<p>#[[ collector_name ]] -  has been assigned to new Appointment.</p>',
            'sms_template_detail' => '<p>#[[ collector_name ]] -  has been assigned to new Appointment.</p>',
            'mail_template_detail' => '<p>#[[ collector_name ]] -  has been assigned to new Appointment.</p>',

        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'vendor',
            'status' => 1,
            'subject' => 'Collector Assigned!',
            'mail_subject' => 'Collector Assigned!',
            'whatsapp_subject' => 'Collector Assigned!',
            'sms_subject' => 'Collector Assigned!',
            'template_detail' => '<p>#[[ collector_name ]] - has been assigned to new Appointment.</p>',
            'whatsapp_template_detail' => '<p>#[[ collector_name ]] - has been assigned to new Appointment.</p>',
            'sms_template_detail' => '<p>#[[ collector_name ]] - has been assigned to new Appointment.</p>',
            'mail_template_detail' => '<p>#[[ collector_name ]] - has been assigned to new Appointment.</p>',
        ]);

        // Appointment Decline Template
        $template = NotificationTemplate::create([
            'type' => 'reject_appointment',
            'name' => 'reject_appointment',
            'label' => 'Appointment Rejected',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Appointment Rejected',
            'mail_subject' => 'Appointment Rejected',
            'whatsapp_subject' => 'Appointment Rejected',
            'sms_subject' => 'Appointment Rejected',
            'template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] at [[ lab_name ]] has been Rejected. Please reschedule or contact support.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] at [[ lab_name ]] has been Rejected. Please reschedule or contact support.',
            'sms_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] at [[ lab_name ]] is Rejected. Please reschedule.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] at [[ lab_name ]] has been Rejected. Please reschedule or contact support.</p>'
        ]);

        // Appointment Complete Template
        $template = NotificationTemplate::create([
            'type' => 'completed',
            'name' => 'appointment_completed',
            'label' => 'Appointment Complete',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Appointment Complete',
            'mail_subject' => 'Appointment Complete',
            'whatsapp_subject' => 'Appointment Complete',
            'sms_subject' => 'Appointment Complete',
            'template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] is complete. We hope you had a great experience. Check your account for reports.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] is complete. We hope you had a great experience. Check your account for reports.',
            'sms_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] is complete. Check your account for reports.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] is complete. We hope you had a great experience. Check your account for reports.</p>'
        ]);

        // Payment Pending Template
        $template = NotificationTemplate::create([
            'type' => 'payment_pending',
            'name' => 'payment_pending',
            'label' => 'Payment Pending',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Payment Pending for Your Appointment',
            'mail_subject' => 'Payment Pending for Your Appointment',
            'whatsapp_subject' => 'Payment Pending for Your Appointment',
            'sms_subject' => 'Payment Pending',
            'template_detail' => '<p>Hi [[ user_name ]], payment for your appointment of [[ test_name ]] is pending. Complete the payment to confirm the appointment.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], payment for your appointment of [[ test_name ]] is pending. Complete the payment to confirm the appointment.',
            'sms_template_detail' => 'Hi [[ user_name ]], your payment for [[ test_name ]] is pending. Complete it to confirm the appointment.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], payment for your appointment of [[ test_name ]] is pending. Complete the payment to confirm the appointment.</p>'
        ]);

        // Payment Paid Template
        $template = NotificationTemplate::create([
            'type' => 'payment_paid',
            'name' => 'payment_paid',
            'label' => 'Payment Paid',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Payment Received Successfully',
            'mail_subject' => 'Payment Received Successfully',
            'whatsapp_subject' => 'Payment Received Successfully',
            'sms_subject' => 'Payment Received',
            'template_detail' => '<p>Hi [[ user_name ]], we\'ve received your payment for [[ test_name ]]. Thank you for choosing [[ platform_name ]].</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], we\'ve received your payment for [[ test_name ]]. Thank you for choosing [[ platform_name ]].',
            'sms_template_detail' => 'Hi [[ user_name ]], we\'ve received your payment for [[ test_name ]]. Thank you!',
            'mail_template_detail' => '<p>Hi [[ user_name ]], we\'ve received your payment for [[ test_name ]]. Thank you for choosing [[ platform_name ]].</p>'
        ]);


        // Subscription Added Template
        $template = NotificationTemplate::create([
            'type' => 'subscription_added',
            'name' => 'subscription_added',
            'label' => 'Subscription Added',
            'status' => 1,
            'to' => '["vendor"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'vendor',
            'status' => 1,
            'subject' => 'Subscription Activated Successfully',
            'mail_subject' => 'Subscription Activated Successfully',
            'whatsapp_subject' => 'Subscription Activated Successfully',
            'sms_subject' => 'Subscription Activated',
            'template_detail' => '<p>Hi [[ vendor_name ]], your subscription plan [[ plan_name ]] has been successfully activated. You can now manage laboratories, appointments, and more seamlessly. Thank you for choosing [[ platform_name ]].</p>',
            'whatsapp_template_detail' => 'Hi [[ vendor_name ]], your subscription plan [[ plan_name ]] has been successfully activated. You can now manage laboratories, appointments, and more seamlessly. Thank you for choosing [[ platform_name ]].',
            'sms_template_detail' => 'Hi [[ vendor_name ]], your subscription plan [[ plan_name ]] is now active. Manage your labs efficiently on [[ platform_name ]].',
            'mail_template_detail' => '<p>Hi [[ vendor_name ]], your subscription plan [[ plan_name ]] has been successfully activated. You can now manage laboratories, appointments, and more seamlessly. Thank you for choosing [[ platform_name ]].</p>'
        ]);

        // Subscription Expired Template
        $template = NotificationTemplate::create([
            'type' => 'subscription_expired',
            'name' => 'subscription_expired',
            'label' => 'Subscription Expired',
            'status' => 1,
            'to' => '["vendor"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'vendor',
            'status' => 1,
            'subject' => 'Subscription Expired',
            'mail_subject' => 'Subscription Expired',
            'whatsapp_subject' => 'Subscription Expired',
            'sms_subject' => 'Subscription Expired',
            'template_detail' => '<p>Hi [[ vendor_name ]], your subscription plan [[ plan_name ]] has expired. To continue managing your laboratories and appointments, please renew your subscription.</p>',
            'whatsapp_template_detail' => 'Hi [[ vendor_name ]], your subscription plan [[ plan_name ]] has expired. To continue managing your laboratories and appointments, please renew your subscription.',
            'sms_template_detail' => 'Hi [[ vendor_name ]], your subscription plan [[ plan_name ]] has expired. Renew now to avoid interruptions.',
            'mail_template_detail' => '<p>Hi [[ vendor_name ]], your subscription plan [[ plan_name ]] has expired. To continue managing your laboratories and appointments, please renew your subscription.</p>'
        ]);

        // Subscription Near Expiry Template
        $template = NotificationTemplate::create([
            'type' => 'subscription_expiring',
            'name' => 'subscription_expiring',
            'label' => 'Subscription Near Expiry',
            'status' => 1,
            'to' => '["vendor"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'vendor',
            'status' => 1,
            'subject' => 'Your Subscription is About to Expire!',
            'mail_subject' => 'Your Subscription is About to Expire!',
            'whatsapp_subject' => 'Your Subscription is About to Expire!',
            'sms_subject' => 'Subscription Expiring Soon',
            'template_detail' => '<p>Hi [[ vendor_name ]], your subscription plan [[ plan_name ]] is set to expire on [[ date_time ]]. Renew now to avoid interruptions in managing your laboratory services.</p>',
            'whatsapp_template_detail' => 'Hi [[ vendor_name ]], your subscription plan [[ plan_name ]] is set to expire on [[ date_time ]]. Renew now to avoid interruptions in managing your laboratory services.',
            'sms_template_detail' => 'Hi [[ vendor_name ]], your subscription plan [[ plan_name ]] expires on [[ date_time ]]. Renew now to continue uninterrupted services.',
            'mail_template_detail' => '<p>Hi [[ vendor_name ]], your subscription plan [[ plan_name ]] is set to expire on [[ date_time ]]. Renew now to avoid interruptions in managing your laboratory services.</p>'
        ]);


        // Subscription Renewal Template
        $template = NotificationTemplate::create([
            'type' => 'subscription_renewed',
            'name' => 'subscription_renewed',
            'label' => 'Subscription Renewed',
            'status' => 1,
            'to' => '["vendor"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'vendor',
            'status' => 1,
            'subject' => 'Subscription Renewed Successfully',
            'mail_subject' => 'Subscription Renewed Successfully',
            'whatsapp_subject' => 'Subscription Renewed Successfully',
            'sms_subject' => 'Subscription Renewed',
            'template_detail' => '<p>Hi [[ vendor_name ]], thank you for renewing your subscription plan [[ plan_name ]]. Your account is now updated, and services will continue seamlessly.</p>',
            'whatsapp_template_detail' => 'Hi [[ vendor_name ]], thank you for renewing your subscription plan [[ plan_name ]]. Your account is now updated, and services will continue seamlessly.',
            'sms_template_detail' => 'Hi [[ vendor_name ]], your subscription plan [[ plan_name ]] has been renewed successfully. Enjoy uninterrupted services!',
            'mail_template_detail' => '<p>Hi [[ vendor_name ]], thank you for renewing your subscription plan [[ plan_name ]]. Your account is now updated, and services will continue seamlessly.</p>'
        ]);

        // OTP Verification Template
        $template = NotificationTemplate::create([
            'type' => 'otp_generated',
            'name' => 'otp_generated',
            'label' => 'Send OTP',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Your OTP for Verification',
            'mail_subject' => 'Your OTP for Verification',
            'whatsapp_subject' => 'Your OTP for Verification',
            'sms_subject' => 'OTP Verification',
            'template_detail' => '<p>Hi [[ user_name ]], your OTP for verifying your account is [[ otp_code ]]. This OTP is valid for 10 minutes. Do not share it with anyone.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], your OTP for verifying your account is [[ otp_code ]]. This OTP is valid for 10 minutes. Do not share it with anyone.',
            'sms_template_detail' => 'Your OTP for [[ platform_name ]] is [[ otp_code ]]. It\'s valid for 10 minutes. Do not share this with anyone.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], your OTP for verifying your account is [[ otp_code ]]. This OTP is valid for 10 minutes. Do not share it with anyone.</p>'
        ]);

        // Prescription Pending Template
        $template = NotificationTemplate::create([
            'type' => 'prescription_pending',
            'name' => 'prescription_pending',
            'label' => 'Prescription Pending',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Prescription Pending',
            'mail_subject' => 'Prescription Pending',
            'whatsapp_subject' => 'Prescription Pending',
            'sms_subject' => 'Prescription Pending',
            'template_detail' => '<p>Hi [[ user_name ]], we noticed that you uploaded a prescription for guidance, but it\'s yet to be reviewed. Our team is working on it and will provide recommendations shortly. Thank you for your patience.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], we noticed that you uploaded a prescription for guidance, but it\'s yet to be reviewed. Our team is working on it and will provide recommendations shortly. Thank you for your patience.',
            'sms_template_detail' => 'Hi [[ user_name ]], your prescription is pending review. Recommendations will be shared shortly. Thank you for your patience.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], we noticed that you uploaded a prescription for guidance, but it\'s yet to be reviewed. Our team is working on it and will provide recommendations shortly. Thank you for your patience.</p>'
        ]);

        // Prescription Suggestion Template
        $template = NotificationTemplate::create([
            'type' => 'prescription_suggestion',
            'name' => 'prescription_suggestion',
            'label' => 'Prescription Suggestion',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Prescription Reviewed: Test Suggestions',
            'mail_subject' => 'Prescription Reviewed: Test Suggestions',
            'whatsapp_subject' => 'Prescription Reviewed: Test Suggestions',
            'sms_subject' => 'Prescription Review Complete',
            'template_detail' => '<p>Hi [[ user_name ]], based on your uploaded prescription, we recommend the following tests/packages: [[ test_name ]]. You can book them directly via your dashboard.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], based on your uploaded prescription, we recommend the following tests/packages: [[ test_name ]]. You can book them directly via your dashboard.',
            'sms_template_detail' => 'Hi [[ user_name ]], based on your prescription, we recommend [[ test_name ]]. Book now via your account.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], based on your uploaded prescription, we recommend the following tests/packages: [[ test_name ]]. You can book them directly via your dashboard.</p>'
        ]);

        // Sample Received at Lab Template
        $template = NotificationTemplate::create([
            'type' => 'test_case_received',
            'name' => 'test_case_received',
            'label' => 'Received at Lab',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Sample Received at the Lab',
            'mail_subject' => 'Sample Received at the Lab',
            'whatsapp_subject' => 'Sample Received at the Lab',
            'sms_subject' => 'Sample Received',
            'template_detail' => '<p>Hi [[ user_name ]], your sample for the test [[ test_name ]] has been successfully received at the lab on [[ date_time ]]. Processing will begin shortly.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], your sample for the test [[ test_name ]] has been successfully received at the lab on [[ date_time ]]. Processing will begin shortly.',
            'sms_template_detail' => 'Hi [[ user_name ]], your sample for [[ test_name ]] has been received at the lab. Processing will begin soon.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], your sample for the test [[ test_name ]] has been successfully received at the lab on [[ date_time ]]. Processing will begin shortly.</p>'
        ]);

        // Sample Not Received Template
        $template = NotificationTemplate::create([
            'type' => 'test_case_not_received',
            'name' => 'test_case_not_received',
            'label' => 'Not Received at Lab',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Sample Not Received at Lab',
            'mail_subject' => 'Sample Not Received at Lab',
            'whatsapp_subject' => 'Sample Not Received at Lab',
            'sms_subject' => 'Sample Not Received',
            'template_detail' => '<p>Hi [[ user_name ]], we have not received your sample for the test [[ test_name ]]. Please contact our support team or your collector to resolve this issue.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], we have not received your sample for the test [[ test_name ]]. Please contact our support team or your collector to resolve this issue.',
            'sms_template_detail' => 'Hi [[ user_name ]], your sample for [[ test_name ]] has not been received at the lab. Please contact support for assistance.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], we have not received your sample for the test [[ test_name ]]. Please contact our support team or your collector to resolve this issue.</p>'
        ]);

        // Appointment Reschedule Template
        $template = NotificationTemplate::create([
            'type' => 'appointment_rescheduled',
            'name' => 'appointment_rescheduled',
            'label' => 'Appointment Rescheduled',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Appointment Rescheduled Successfully',
            'mail_subject' => 'Appointment Rescheduled Successfully',
            'whatsapp_subject' => 'Appointment Rescheduled Successfully',
            'sms_subject' => 'Appointment Rescheduled',
            'template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] has been successfully rescheduled to [[ date_time ]]. Please be available for the collector.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] has been successfully rescheduled to [[ date_time ]]. Please be available for the collector.',
            'sms_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] has been rescheduled to [[ date_time ]]. Thank you!',
            'mail_template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] has been successfully rescheduled to [[ date_time ]]. Please be available for the collector.</p>'
        ]);

        // Test Case In Progress Template
        $template = NotificationTemplate::create([
            'type' => 'test_in_progress',
            'name' => 'test_in_progress',
            'label' => 'Test In Progress',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Your Test is in Progress',
            'mail_subject' => 'Your Test is in Progress',
            'whatsapp_subject' => 'Your Test is in Progress',
            'sms_subject' => 'Test in Progress',
            'template_detail' => '<p>Hi [[ user_name ]], the processing of your sample for [[ test_name ]] has begun. We will notify you once the results are ready.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], the processing of your sample for [[ test_name ]] has begun. We will notify you once the results are ready.',
            'sms_template_detail' => 'Hi [[ user_name ]], your sample for [[ test_name ]] is being processed. We\'ll notify you when the results are ready.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], the processing of your sample for [[ test_name ]] has begun. We will notify you once the results are ready.</p>'
        ]);

        // Test Case Awaiting Validation Template
        $template = NotificationTemplate::create([
            'type' => 'test_awaiting_validation',
            'name' => 'test_awaiting_validation',
            'label' => 'Test Awaiting Validation',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Test Results Awaiting Validation',
            'mail_subject' => 'Test Results Awaiting Validation',
            'whatsapp_subject' => 'Test Results Awaiting Validation',
            'sms_subject' => 'Results Awaiting Validation',
            'template_detail' => '<p>Hi [[ user_name ]], your test results for [[ test_name ]] are ready and are currently awaiting validation by our experts. Thank you for your patience.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], your test results for [[ test_name ]] are ready and are currently awaiting validation by our experts. Thank you for your patience.',
            'sms_template_detail' => 'Hi [[ user_name ]], your test results for [[ test_name ]] are awaiting validation. Thank you for your patience.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], your test results for [[ test_name ]] are ready and are currently awaiting validation by our experts. Thank you for your patience.</p>'
        ]);

        // Test Case Validated Template
        $template = NotificationTemplate::create([
            'type' => 'test_validated',
            'name' => 'test_validated',
            'label' => 'Test Validated',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Test Results Validated',
            'mail_subject' => 'Test Results Validated',
            'whatsapp_subject' => 'Test Results Validated',
            'sms_subject' => 'Results Validated',
            'template_detail' => '<p>Hi [[ user_name ]], your test results for [[ test_name ]] have been validated and will be available shortly. Thank you for choosing [[ platform_name ]].</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], your test results for [[ test_name ]] have been validated and will be available shortly. Thank you for choosing [[ platform_name ]].',
            'sms_template_detail' => 'Hi [[ user_name ]], your test results for [[ test_name ]] have been validated and will be available shortly.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], your test results for [[ test_name ]] have been validated and will be available shortly. Thank you for choosing [[ platform_name ]].</p>'
        ]);


        // Report Generated Template
        $template = NotificationTemplate::create([
            'type' => 'report_generated',
            'name' => 'report_generated', 
            'label' => 'Report Generated',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Your Test Report is Ready',
            'mail_subject' => 'Your Test Report is Ready',
            'whatsapp_subject' => 'Your Test Report is Ready',
            'sms_subject' => 'Report Ready',
            'template_detail' => '<p>Hi [[ user_name ]], the report for your test [[ test_name ]] has been successfully generated. You can download it from your account.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], the report for your test [[ test_name ]] has been successfully generated. You can download it from your account.',
            'sms_template_detail' => 'Hi [[ user_name ]], your report for [[ test_name ]] is ready. Download it from your account.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], the report for your test [[ test_name ]] has been successfully generated. You can download it from your account.</p>'
        ]);

        // Send Report Template
        $template = NotificationTemplate::create([
            'type' => 'report_sent',
            'name' => 'report_sent',
            'label' => 'Report Sent',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1','IS_MAIL' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user', 
            'status' => 1,
            'subject' => 'Your Test Report',
            'mail_subject' => 'Your Test Report',
            'whatsapp_subject' => 'Your Test Report',
            'sms_subject' => 'Test Report Sent',
            'template_detail' => '<p>Hi [[ user_name ]], your report for [[ test_name ]] has been shared successfully. Please check your email or account to view/download the report.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], your report for [[ test_name ]] has been shared successfully. Please check your email or account to view/download the report.',
            'sms_template_detail' => 'Hi [[ user_name ]], your report for [[ test_name ]] has been sent. Check your email or account to view/download it.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], your report for [[ test_name ]] has been shared successfully. Please check your email or account to view/download the report.</p>'
        ]);


        //wallet

        $template = NotificationTemplate::create([
            'type' => 'wallet_top_up',
            'name' => 'wallet_top_up',
            'label' => 'Wallet Top Up',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Wallet Top-Up',
            'mail_subject' => 'Wallet Top-Up',
            'whatsapp_subject' => 'Wallet Top-Up',
            'sms_subject' => 'Wallet Top-Up',
            'template_detail' => '<p>[[ customer_name ]] has topped up wallet with [[ credit_debit_amount ]].</p>',
            'whatsapp_template_detail' => '<p>[[ customer_name ]] has topped up wallet with [[ credit_debit_amount ]].</p>',
            'sms_template_detail' => '<p>[[ customer_name ]] has topped up wallet with [[ credit_debit_amount ]].</p>',
            'mail_template_detail' => '<p>[[ customer_name ]] has topped up wallet with [[ credit_debit_amount ]].</p>',

        ]);

        $template = NotificationTemplate::create([
            'type' => 'in_progress_appointment',
            'name' => 'in_progress_appointment',
            'label' => 'Appointment In Progress',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1', 'IS_MAIL' => '1'],
        ]);
        
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Your Appointment is Now In Progress!',
            'mail_subject' => 'Your Appointment is Now In Progress!',
            'whatsapp_subject' => 'Your Appointment is Now In Progress!',
            'sms_subject' => 'Appointment In Progress',
            'template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] with [[ lab_name ]] is now in progress. Please be ready.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] with [[ lab_name ]] is now in progress. Please be ready.',
            'sms_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] is now in progress. Please be ready.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] with [[ lab_name ]] is now in progress. Please be ready.</p>'
        ]);

        $template = NotificationTemplate::create([
            'type' => 'otp_verified',
            'name' => 'otp_verified',
            'label' => 'OTP Verified',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1', 'IS_MAIL' => '1'],
        ]);
        
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Your OTP Has Been Verified!',
            'mail_subject' => 'Your OTP Has Been Verified!',
            'whatsapp_subject' => 'Your OTP Has Been Verified!',
            'sms_subject' => 'OTP Verified',
            'template_detail' => '<p>Hi [[ user_name ]], your OTP for appointment [[ appointment_id ]] has been successfully verified. Your appointment is now confirmed.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], your OTP for appointment [[ appointment_id ]] has been successfully verified. Your appointment is now confirmed.',
            'sms_template_detail' => 'Hi [[ user_name ]], your OTP for appointment [[ appointment_id ]] has been verified.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], your OTP for appointment [[ appointment_id ]] has been successfully verified. Your appointment is now confirmed.</p>'
        ]);

        $template = NotificationTemplate::create([
            'type' => 'on_going_appointment',
            'name' => 'on_going_appointment',
            'label' => 'Appointment Ongoing',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1', 'IS_MAIL' => '1'],
        ]);
        
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Your Appointment is Ongoing!',
            'mail_subject' => 'Your Appointment is Ongoing!',
            'whatsapp_subject' => 'Your Appointment is Ongoing!',
            'sms_subject' => 'Appointment Ongoing',
            'template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] with [[ lab_name ]] is now ongoing. Please proceed with the necessary steps.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] with [[ lab_name ]] is now ongoing. Please proceed with the necessary steps.',
            'sms_template_detail' => 'Hi [[ user_name ]], your appointment for [[ test_name ]] is now ongoing. Please proceed with the necessary steps.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], your appointment for [[ test_name ]] with [[ lab_name ]] is now ongoing. Please proceed with the necessary steps.</p>'
        ]);

        $template = NotificationTemplate::create([
            'type' => 'payment_completed',
            'name' => 'payment_completed',
            'label' => 'Payment Completed',
            'status' => 1,
            'to' => '["admin","user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1', 'IS_MAIL' => '1'],
        ]);
        
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Payment Successful!',
            'mail_subject' => 'Payment Successful!',
            'whatsapp_subject' => 'Payment Successful!',
            'sms_subject' => 'Payment Completed',
            'template_detail' => '<p>Hi [[ user_name ]], your payment for [[ test_name ]] has been successfully completed.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], your payment for [[ test_name ]] has been successfully completed.',
            'sms_template_detail' => 'Hi [[ user_name ]], your payment for [[ test_name ]] is completed.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], your payment for [[ test_name ]] has been successfully completed.</p>'
        ]);
        
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Payment Received',
            'mail_subject' => 'Payment Received',
            'whatsapp_subject' => 'Payment Received',
            'sms_subject' => 'Payment Received',
            'template_detail' => '<p>Payment received for [[ test_name ]] of [[ appointment_amount ]].</p>',
            'whatsapp_template_detail' => 'Payment received for [[ test_name ]] of [[ appointment_amount ]].',
            'sms_template_detail' => 'Payment received for [[ test_name ]] of [[ appointment_amount ]].',
            'mail_template_detail' => '<p>Payment received for [[ test_name ]] of [[ appointment_amount ]].</p>'
        ]);

        $template = NotificationTemplate::create([
            'type' => 'add_helpdesk',
            'name' => 'add_helpdesk',
            'label' => 'Query confirmation',
            'status' => 1,
            'to' => '["admin"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'New Query Received',
            'mail_subject' => 'New Query Received',
            'whatsapp_subject' => 'New Query Received',
            'sms_subject' => 'New Query Received',
            'template_detail' => '<p>New Query [[ sender_name ]] - [[ subject ]].</p>',
            'whatsapp_template_detail' => '<p>New Query [[ sender_name ]] - [[ subject ]].</p>',
            'sms_template_detail' => '<p>New Query [[ sender_name ]] - [[ subject ]].</p>',
            'mail_template_detail' => '<p>New Query [[ sender_name ]] - [[ subject ]].</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'closed_helpdesk',
            'name' => 'closed_helpdesk',
            'label' => 'Closed',
            'status' => 1,
            'to' => '["admin","vendor","collector","user"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Query Closed',
            'mail_subject' => 'Query Closed',
            'whatsapp_subject' => 'Query Closed',
            'sms_subject' => 'Query Closed',
            'template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'vendor',
            'status' => 1,
            'subject' => 'Query Closed',
            'mail_subject' => 'Query Closed',
            'whatsapp_subject' => 'Query Closed',
            'sms_subject' => 'Query Closed',
            'template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'collector',
            'status' => 1,
            'subject' => 'Query Closed',
            'mail_subject' => 'Query Closed',
            'whatsapp_subject' => 'Query Closed',
            'sms_subject' => 'Query Closed',
            'template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Query Closed',
            'mail_subject' => 'Query Closed',
            'whatsapp_subject' => 'Query Closed',
            'sms_subject' => 'Query Closed',
            'template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'reply_helpdesk',
            'name' => 'reply_helpdesk',
            'label' => 'Replied Query',
            'status' => 1,
            'to' => '["admin","vendor","collector","user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Query Replied',
            'mail_subject' => 'Query Replied',
            'whatsapp_subject' => 'Query Replied',
            'sms_subject' => 'Query Replied',
            'template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'vendor',
            'status' => 1,
            'subject' => 'Query Replied',
            'mail_subject' => 'Query Replied',
            'whatsapp_subject' => 'Query Replied',
            'sms_subject' => 'Query Replied',
            'template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'collector',
            'status' => 1,
            'subject' => 'Query Replied',
            'mail_subject' => 'Query Replied',
            'whatsapp_subject' => 'Query Replied',
            'sms_subject' => 'Query Replied',
            'template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Query Replied',
            'mail_subject' => 'Query Replied',
            'whatsapp_subject' => 'Query Replied',
            'sms_subject' => 'Query Replied',
            'template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'vendor_payout',
            'name' => 'vendor_payout',
            'label' => 'Vendor Payout Processed',
            'status' => 1,
            'to' => '["vendor"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1', 'IS_MAIL' => '1'],
        ]);
        
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'vendor',
            'status' => 1,
            'subject' => 'Payout Processed Successfully!',
            'mail_subject' => 'Your Payout Has Been Processed',
            'whatsapp_subject' => 'Your Payout Has Been Processed',
            'sms_subject' => 'Payout Processed',
            'template_detail' => '<p>Hi [[ vendor_name ]], your payout of [[ amount ]] has been processed on [[ pay_date ]].</p>',
            'whatsapp_template_detail' => 'Hi [[ vendor_name ]], your payout of [[ amount ]] has been processed on [[ pay_date ]].',
            'sms_template_detail' => 'Hi [[ vendor_name ]], your payout of [[ amount ]] has been processed on [[ pay_date ]].',
            'mail_template_detail' => '<p>Hi [[ vendor_name ]], your payout of [[ amount ]] has been processed on [[ pay_date ]].</p>'
        ]);

        $template = NotificationTemplate::create([
            'type' => 'collector_payout',
            'name' => 'collector_payout',
            'label' => 'Collector Payout Processed',
            'status' => 1,
            'to' => '["collector"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1', 'IS_MAIL' => '1'],
        ]);
        
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'collector',
            'status' => 1,
            'subject' => 'Payout Processed Successfully!',
            'mail_subject' => 'Your Payout Has Been Processed',
            'whatsapp_subject' => 'Your Payout Has Been Processed',
            'sms_subject' => 'Payout Processed',
            'template_detail' => '<p>Hi [[ collector_name ]], your payout of [[ amount ]] has been processed on [[ pay_date ]].</p>',
            'whatsapp_template_detail' => 'Hi [[ collector_name ]], your payout of [[ amount ]] has been processed on [[ pay_date ]].',
            'sms_template_detail' => 'Hi [[ collector_name ]], your payout of [[ amount ]] has been processed on [[ pay_date ]].',
            'mail_template_detail' => '<p>Hi [[ collector_name ]], your payout of [[ amount ]] has been processed on [[ pay_date ]].</p>'
        ]);

        $template = NotificationTemplate::create([
            'type' => 'cancel_subscription',
            'name' => 'cancel_subscription',
            'label' => 'Subscription Canceled',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1', 'IS_MAIL' => '1'],
        ]);
        
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Your Subscription Has Been Canceled',
            'mail_subject' => 'Your Subscription Has Been Canceled',
            'whatsapp_subject' => 'Your Subscription Has Been Canceled',
            'sms_subject' => 'Subscription Canceled',
            'template_detail' => '<p>Hi [[ user_name ]], your subscription to [[ subscription_name ]] has been canceled successfully. If you have any questions, contact support.</p>',
            'whatsapp_template_detail' => 'Hi [[ user_name ]], your subscription to [[ subscription_name ]] has been canceled successfully. Contact support if needed.',
            'sms_template_detail' => 'Hi [[ user_name ]], your subscription to [[ subscription_name ]] has been canceled successfully.',
            'mail_template_detail' => '<p>Hi [[ user_name ]], your subscription to [[ subscription_name ]] has been canceled successfully. If you have any questions, contact support.</p>'
        ]);
        
        
        
        
        
        
    }                                                                                                                                                                                                                                                                               
}
