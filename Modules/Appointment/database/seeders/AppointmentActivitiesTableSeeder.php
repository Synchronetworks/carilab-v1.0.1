<?php

namespace Modules\Appointment\database\seeders;

use Illuminate\Database\Seeder;

class AppointmentActivitiesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('appointment_activities')->delete();
        
        \DB::table('appointment_activities')->insert(array (
            0 => 
            array (
                'id' => 1,
                'appointment_id' => 1,
                'activity_date' => '2025-03-19 15:23:11',
                'activity_message' => 'New appointment created',
                'activity_type' => 'add_appointment',
                'activity_data' => '{"activity_type":"add_appointment","notification_type":"new_appointment","appointment_id":1,"customer":"John Doe","test_name":"Essential Health Screening Package"}',
                'created_at' => '2025-03-19 15:23:11',
                'updated_at' => '2025-03-19 15:23:11',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'appointment_id' => 2,
                'activity_date' => '2025-03-19 15:41:21',
                'activity_message' => 'New appointment created',
                'activity_type' => 'add_appointment',
                'activity_data' => '{"activity_type":"add_appointment","notification_type":"new_appointment","appointment_id":2,"customer":"Tracy Jones","test_name":"Essential Health & Wellness Package"}',
                'created_at' => '2025-03-19 15:41:21',
                'updated_at' => '2025-03-19 15:41:21',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'appointment_id' => 3,
                'activity_date' => '2025-03-19 15:47:27',
                'activity_message' => 'New appointment created',
                'activity_type' => 'add_appointment',
                'activity_data' => '{"activity_type":"add_appointment","notification_type":"new_appointment","appointment_id":3,"customer":"Tracy Jones","test_name":"Saliva Fertility Test"}',
                'created_at' => '2025-03-19 15:47:27',
                'updated_at' => '2025-03-19 15:47:27',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'appointment_id' => 4,
                'activity_date' => '2025-03-19 15:49:01',
                'activity_message' => 'New appointment created',
                'activity_type' => 'add_appointment',
            'activity_data' => '{"activity_type":"add_appointment","notification_type":"new_appointment","appointment_id":4,"customer":"John Doe","test_name":"Prostate-Specific Antigen (PSA)"}',
                'created_at' => '2025-03-19 15:49:01',
                'updated_at' => '2025-03-19 15:49:01',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'appointment_id' => 5,
                'activity_date' => '2025-03-19 15:49:41',
                'activity_message' => 'New appointment created',
                'activity_type' => 'add_appointment',
                'activity_data' => '{"activity_type":"add_appointment","notification_type":"new_appointment","appointment_id":5,"customer":"John Doe","test_name":"Liver & Cholesterol Health Package"}',
                'created_at' => '2025-03-19 15:49:41',
                'updated_at' => '2025-03-19 15:49:41',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'appointment_id' => 6,
                'activity_date' => '2025-03-19 15:51:04',
                'activity_message' => 'New appointment created',
                'activity_type' => 'add_appointment',
                'activity_data' => '{"activity_type":"add_appointment","notification_type":"new_appointment","appointment_id":6,"customer":"John Doe","test_name":"Advanced Metabolic & Heart Health Package"}',
                'created_at' => '2025-03-19 15:51:04',
                'updated_at' => '2025-03-19 15:51:04',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'appointment_id' => 7,
                'activity_date' => '2025-03-19 15:51:45',
                'activity_message' => 'New appointment created',
                'activity_type' => 'add_appointment',
                'activity_data' => '{"activity_type":"add_appointment","notification_type":"new_appointment","appointment_id":7,"customer":"John Doe","test_name":"Low-Dose CT Screening for Lung Cancer"}',
                'created_at' => '2025-03-19 15:51:45',
                'updated_at' => '2025-03-19 15:51:45',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'appointment_id' => 8,
                'activity_date' => '2025-03-19 15:55:21',
                'activity_message' => 'New appointment created',
                'activity_type' => 'add_appointment',
                'activity_data' => '{"activity_type":"add_appointment","notification_type":"new_appointment","appointment_id":8,"customer":"John Doe","test_name":"Urine Test for Bladder Cancer"}',
                'created_at' => '2025-03-19 15:55:21',
                'updated_at' => '2025-03-19 15:55:21',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'appointment_id' => 1,
                'activity_date' => '2025-03-19 16:02:16',
                'activity_message' => 'Collector Assigned Successfully',
                'activity_type' => 'collector_assigned',
                'activity_data' => '{"activity_type":"collector_assigned","notification_type":"collector_assigned","appointment_id":1,"activity_message":"Collector Assigned Successfully","collector":"Felix Harris","collector_id":"24"}',
                'created_at' => '2025-03-19 16:02:16',
                'updated_at' => '2025-03-19 16:02:16',
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'appointment_id' => 1,
                'activity_date' => '2025-03-19 10:35:35',
                'activity_message' => 'Appointment On Going',
                'activity_type' => 'on_going_appointment',
                'activity_data' => '{"activity_type":"on_going_appointment","activity_message":"Appointment On Going","notification_type":"on_going_appointment","appointment_id":1}',
                'created_at' => '2025-03-19 10:35:35',
                'updated_at' => '2025-03-19 10:35:35',
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'appointment_id' => 1,
                'activity_date' => '2025-03-19 16:06:06',
                'activity_message' => 'Otp Generated Successfully',
                'activity_type' => 'otp_generated',
                'activity_data' => '{"activity_type":"otp_generated","notification_type":"otp_generated","appointment_id":1,"appointment":{"id":1,"status":"on_going","customer_id":3,"other_member_id":null,"vendor_id":4,"lab_id":1,"test_id":1,"test_type":"test_package","address_id":-1,"appointment_date":"2025-03-18T18:30:00.000000Z","appointment_time":"2025-03-19T11:00:00.000000Z","amount":500,"test_discount_amount":440,"collection_type":"lab","total_amount":440,"submission_status":"pending","test_case_status":null,"rejected_id":null,"by_suggestion":0,"cancellation_reason":null,"symptoms":"Breathing issues","reschedule_reason":null,"created_by":3,"updated_by":24,"deleted_by":null,"created_at":"2025-03-19T09:53:11.000000Z","updated_at":"2025-03-19T10:35:31.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","appointment_collector_mapping":{"id":1,"appointment_id":1,"collector_id":24,"created_at":"2025-03-19T10:32:16.000000Z","updated_at":"2025-03-19T10:32:16.000000Z","deleted_at":null,"collector":{"id":24,"username":"felix_harris","first_name":"Felix","last_name":"Harris","email":"collector@gmail.com","mobile":"1-78952526","country_id":null,"state_id":null,"city_id":null,"address":"6 Heart Street, Cityville, USA","login_type":null,"gender":"male","date_of_birth":"1984-10-19","email_verified_at":null,"is_verify":0,"is_banned":0,"is_subscribe":0,"is_available":1,"last_online_time":"16:05:24","status":1,"set_as_featured":0,"last_notification_seen":null,"user_type":"collector","social_image":null,"created_at":"2025-03-19T09:42:28.000000Z","updated_at":"2025-03-19T10:35:24.000000Z","deleted_at":null,"full_name":"Felix Harris","profile_image":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/24\\/5kUgTIXZAnG7grfchScZqUf0npM4QtTPXcQnRq2T.png","total_commission_amount":0,"total_appointments":0,"total_service_amount":0,"total_tax_amount":0,"total_admin_earnings":0,"total_vendor_earnings":0,"total_collector_earnings":0,"collector_paid_earnings":0,"vendor_paid_earnings":0,"user_commission_mapping":[],"media":[{"id":24,"model_type":"App\\\\Models\\\\User","model_id":24,"uuid":"1f54ccd8-6732-4b72-921d-957044571a4c","collection_name":"profile_image","name":"felix","file_name":"5kUgTIXZAnG7grfchScZqUf0npM4QtTPXcQnRq2T.png","mime_type":"image\\/png","disk":"public","conversions_disk":"public","size":130852,"manipulations":[],"custom_properties":[],"generated_conversions":[],"responsive_images":[],"order_column":1,"created_at":"2025-03-19T09:42:29.000000Z","updated_at":"2025-03-19T09:42:29.000000Z","original_url":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/24\\/5kUgTIXZAnG7grfchScZqUf0npM4QtTPXcQnRq2T.png","preview_url":""}]}},"transactions":{"id":1,"appointment_id":1,"txn_id":null,"discount_type":"fixed","discount_value":10,"discount_amount":10,"coupon_id":3,"coupon":{"id":3,"vendor_id":null,"lab_id":1,"coupon_code":"QWE78TYL","discount_type":"fixed","discount_value":"50.00","applicability":"specific_packages","start_at":"2025-02-19","end_at":"2025-04-05","total_usage_limit":12,"per_customer_usage_limit":3,"status":true,"created_by":null,"updated_by":null,"deleted_by":null,"created_at":"2025-03-19T09:43:22.000000Z","updated_at":"2025-03-19T09:43:22.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","media":[]},"coupon_amount":50,"tax":"[]","total_tax_amount":0,"total_amount":440,"payment_type":null,"payment_status":"pending","request_token":null,"created_at":"2025-03-19T09:53:11.000000Z","updated_at":"2025-03-19T09:53:11.000000Z","deleted_at":null},"media":[{"id":299,"model_type":"Modules\\\\Appointment\\\\Models\\\\Appointment","model_id":1,"uuid":"71ab60ab-ca19-4acf-940b-8bf2641a2abe","collection_name":"medical_report","name":"Invoice","file_name":"67da9407b8025.pdf","mime_type":"application\\/pdf","disk":"public","conversions_disk":"public","size":15610,"manipulations":[],"custom_properties":{"mime_type":"application\\/pdf","original_name":"Invoice.pdf"},"generated_conversions":[],"responsive_images":[],"order_column":1,"created_at":"2025-03-19T09:53:11.000000Z","updated_at":"2025-03-19T09:53:11.000000Z","original_url":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/299\\/67da9407b8025.pdf","preview_url":""}]},"otp":"060465"}',
                'created_at' => '2025-03-19 16:06:06',
                'updated_at' => '2025-03-19 16:06:06',
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'appointment_id' => 1,
                'activity_date' => '2025-03-19 10:36:11',
                'activity_message' => 'Appointment In Progress',
                'activity_type' => 'in_progress_appointment',
                'activity_data' => '{"activity_type":"in_progress_appointment","activity_message":"Appointment In Progress","notification_type":"in_progress_appointment","appointment_id":1}',
                'created_at' => '2025-03-19 10:36:11',
                'updated_at' => '2025-03-19 10:36:11',
                'deleted_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'appointment_id' => 1,
                'activity_date' => '2025-03-19 16:07:12',
                'activity_message' => 'Otp Verified Successfully',
                'activity_type' => 'otp_verified',
                'activity_data' => '{"activity_type":"otp_verified","notification_type":"otp_verified","appointment_id":1,"collector":"-"}',
                'created_at' => '2025-03-19 16:07:12',
                'updated_at' => '2025-03-19 16:07:12',
                'deleted_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'appointment_id' => 1,
                'activity_date' => '2025-03-19 16:07:12',
                'activity_message' => 'Otp Verified Successfully',
                'activity_type' => 'otp_verified',
                'activity_data' => '{"activity_type":"otp_verified","notification_type":"otp_verified","appointment_id":1,"collector":"-"}',
                'created_at' => '2025-03-19 16:07:12',
                'updated_at' => '2025-03-19 16:07:12',
                'deleted_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'appointment_id' => 1,
                'activity_date' => '2025-03-19 10:37:15',
                'activity_message' => 'Appointment Completed Successfully',
                'activity_type' => 'completed_appointment',
                'activity_data' => '{"activity_type":"completed_appointment","activity_message":"Appointment Completed Successfully","notification_type":"completed_appointment","appointment_id":1}',
                'created_at' => '2025-03-19 10:37:15',
                'updated_at' => '2025-03-19 10:37:15',
                'deleted_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'appointment_id' => 1,
                'activity_date' => '2025-03-19 16:07:16',
                'activity_message' => 'Appointment Completed Successfully',
                'activity_type' => 'completed_appointment',
                'activity_data' => NULL,
                'created_at' => '2025-03-19 16:07:16',
                'updated_at' => '2025-03-19 16:07:16',
                'deleted_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'appointment_id' => 1,
                'activity_date' => '2025-03-19 16:44:27',
                'activity_message' => 'Test Case Accepted Successfully',
                'activity_type' => 'test_case_received',
                'activity_data' => '{"activity_type":"test_case_received","notification_type":"test_case_received","appointment_id":1,"test_name":"Essential Health Screening Package"}',
                'created_at' => '2025-03-19 16:44:27',
                'updated_at' => '2025-03-19 16:44:27',
                'deleted_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'appointment_id' => 1,
                'activity_date' => '2025-03-19 16:44:38',
                'activity_message' => 'Test Case Accepted Successfully',
                'activity_type' => 'test_case_received',
                'activity_data' => '{"activity_type":"test_case_received","notification_type":"test_case_received","appointment_id":1,"test_name":"Essential Health Screening Package"}',
                'created_at' => '2025-03-19 16:44:38',
                'updated_at' => '2025-03-19 16:44:38',
                'deleted_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'appointment_id' => 1,
                'activity_date' => '2025-03-19 16:46:12',
                'activity_message' => 'report_generated',
                'activity_type' => 'report_generated',
                'activity_data' => '{"activity_type":"report_generated","notification_type":"report_generated","appointment_id":1,"appointment":{"id":1,"status":"completed","customer_id":3,"other_member_id":null,"vendor_id":4,"lab_id":1,"test_id":1,"test_type":"test_package","address_id":-1,"appointment_date":"2025-03-18T18:30:00.000000Z","appointment_time":"2025-03-19T11:00:00.000000Z","amount":500,"test_discount_amount":440,"collection_type":"lab","total_amount":451.9,"submission_status":"accept","test_case_status":"report_generated","rejected_id":null,"by_suggestion":0,"cancellation_reason":null,"symptoms":"Breathing issues","reschedule_reason":null,"created_by":3,"updated_by":1,"deleted_by":null,"created_at":"2025-03-19T09:53:11.000000Z","updated_at":"2025-03-19T11:16:12.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","media":[{"id":299,"model_type":"Modules\\\\Appointment\\\\Models\\\\Appointment","model_id":1,"uuid":"71ab60ab-ca19-4acf-940b-8bf2641a2abe","collection_name":"medical_report","name":"Invoice","file_name":"67da9407b8025.pdf","mime_type":"application\\/pdf","disk":"public","conversions_disk":"public","size":15610,"manipulations":[],"custom_properties":{"mime_type":"application\\/pdf","original_name":"Invoice.pdf"},"generated_conversions":[],"responsive_images":[],"order_column":1,"created_at":"2025-03-19T09:53:11.000000Z","updated_at":"2025-03-19T09:53:11.000000Z","original_url":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/299\\/67da9407b8025.pdf","preview_url":""}]}}',
                'created_at' => '2025-03-19 16:46:12',
                'updated_at' => '2025-03-19 16:46:12',
                'deleted_at' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'appointment_id' => 1,
                'activity_date' => '2025-03-19 16:46:46',
                'activity_message' => 'report_generated',
                'activity_type' => 'report_generated',
                'activity_data' => '{"activity_type":"report_generated","notification_type":"report_generated","appointment_id":1,"appointment":{"id":1,"status":"completed","customer_id":3,"other_member_id":null,"vendor_id":4,"lab_id":1,"test_id":1,"test_type":"test_package","address_id":-1,"appointment_date":"2025-03-18T18:30:00.000000Z","appointment_time":"2025-03-19T11:00:00.000000Z","amount":500,"test_discount_amount":440,"collection_type":"lab","total_amount":451.9,"submission_status":"accept","test_case_status":"report_generated","rejected_id":null,"by_suggestion":0,"cancellation_reason":null,"symptoms":"Breathing issues","reschedule_reason":null,"created_by":3,"updated_by":1,"deleted_by":null,"created_at":"2025-03-19T09:53:11.000000Z","updated_at":"2025-03-19T11:16:12.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","media":[{"id":299,"model_type":"Modules\\\\Appointment\\\\Models\\\\Appointment","model_id":1,"uuid":"71ab60ab-ca19-4acf-940b-8bf2641a2abe","collection_name":"medical_report","name":"Invoice","file_name":"67da9407b8025.pdf","mime_type":"application\\/pdf","disk":"public","conversions_disk":"public","size":15610,"manipulations":[],"custom_properties":{"mime_type":"application\\/pdf","original_name":"Invoice.pdf"},"generated_conversions":[],"responsive_images":[],"order_column":1,"created_at":"2025-03-19T09:53:11.000000Z","updated_at":"2025-03-19T09:53:11.000000Z","original_url":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/299\\/67da9407b8025.pdf","preview_url":""}]}}',
                'created_at' => '2025-03-19 16:46:46',
                'updated_at' => '2025-03-19 16:46:46',
                'deleted_at' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'appointment_id' => 4,
                'activity_date' => '2025-03-19 17:04:15',
                'activity_message' => 'Collector Assigned Successfully',
                'activity_type' => 'collector_assigned',
                'activity_data' => '{"activity_type":"collector_assigned","notification_type":"collector_assigned","appointment_id":4,"activity_message":"Collector Assigned Successfully","collector":"Harvey Francis","collector_id":"29"}',
                'created_at' => '2025-03-19 17:04:15',
                'updated_at' => '2025-03-19 17:04:15',
                'deleted_at' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'appointment_id' => 4,
                'activity_date' => '2025-03-19 11:34:56',
                'activity_message' => 'Appointment On Going',
                'activity_type' => 'on_going_appointment',
                'activity_data' => '{"activity_type":"on_going_appointment","activity_message":"Appointment On Going","notification_type":"on_going_appointment","appointment_id":4}',
                'created_at' => '2025-03-19 11:34:56',
                'updated_at' => '2025-03-19 11:34:56',
                'deleted_at' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'appointment_id' => 4,
                'activity_date' => '2025-03-19 17:06:07',
                'activity_message' => 'Otp Generated Successfully',
                'activity_type' => 'otp_generated',
                'activity_data' => '{"activity_type":"otp_generated","notification_type":"otp_generated","appointment_id":4,"appointment":{"id":4,"status":"on_going","customer_id":3,"other_member_id":null,"vendor_id":4,"lab_id":2,"test_id":198,"test_type":"test_case","address_id":2,"appointment_date":"2025-03-18T18:30:00.000000Z","appointment_time":"2025-03-19T11:00:00.000000Z","amount":60,"test_discount_amount":60,"collection_type":"home","total_amount":71.9,"submission_status":"pending","test_case_status":null,"rejected_id":null,"by_suggestion":0,"cancellation_reason":null,"symptoms":"Cold & Flu","reschedule_reason":null,"created_by":3,"updated_by":29,"deleted_by":null,"created_at":"2025-03-19T10:19:01.000000Z","updated_at":"2025-03-19T11:34:53.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","appointment_collector_mapping":{"id":2,"appointment_id":4,"collector_id":29,"created_at":"2025-03-19T11:34:15.000000Z","updated_at":"2025-03-19T11:34:15.000000Z","deleted_at":null,"collector":{"id":29,"username":"harvey_francis","first_name":"Harvey","last_name":"Francis","email":"harvey@gmail.com","mobile":"1-45789655","country_id":null,"state_id":null,"city_id":null,"address":"56, Main Street, USA","login_type":null,"gender":"male","date_of_birth":"1994-07-02","email_verified_at":null,"is_verify":0,"is_banned":0,"is_subscribe":0,"is_available":1,"last_online_time":"17:05:46","status":1,"set_as_featured":0,"last_notification_seen":"2025-03-19 17:05:51","user_type":"collector","social_image":null,"created_at":"2025-03-19T09:42:28.000000Z","updated_at":"2025-03-19T11:35:51.000000Z","deleted_at":null,"full_name":"Harvey Francis","profile_image":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/29\\/rduFDx2zMC4ZSzL93Bo6DAtmVDAlPSWpocR1KB8J.png","total_commission_amount":0,"total_appointments":0,"total_service_amount":0,"total_tax_amount":0,"total_admin_earnings":0,"total_vendor_earnings":0,"total_collector_earnings":0,"collector_paid_earnings":0,"vendor_paid_earnings":0,"user_commission_mapping":[],"media":[{"id":29,"model_type":"App\\\\Models\\\\User","model_id":29,"uuid":"7008bcd3-c743-4d9d-baad-59a2c3459d95","collection_name":"profile_image","name":"harvey","file_name":"rduFDx2zMC4ZSzL93Bo6DAtmVDAlPSWpocR1KB8J.png","mime_type":"image\\/png","disk":"public","conversions_disk":"public","size":346228,"manipulations":[],"custom_properties":[],"generated_conversions":[],"responsive_images":[],"order_column":1,"created_at":"2025-03-19T09:42:29.000000Z","updated_at":"2025-03-19T09:42:29.000000Z","original_url":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/29\\/rduFDx2zMC4ZSzL93Bo6DAtmVDAlPSWpocR1KB8J.png","preview_url":""}]}},"transactions":{"id":4,"appointment_id":4,"txn_id":null,"discount_type":"","discount_value":0,"discount_amount":0,"coupon_id":null,"coupon":null,"coupon_amount":0,"tax":"[{\\"id\\":1,\\"title\\":\\"Service Tax\\",\\"value\\":5,\\"type\\":\\"Percentage\\",\\"status\\":1},{\\"id\\":2,\\"title\\":\\"Home Collection Fee\\",\\"value\\":10,\\"type\\":\\"Fixed\\",\\"status\\":1}]","total_tax_amount":11.9,"total_amount":71.9,"payment_type":null,"payment_status":"pending","request_token":null,"created_at":"2025-03-19T10:19:01.000000Z","updated_at":"2025-03-19T10:19:01.000000Z","deleted_at":null},"media":[{"id":310,"model_type":"Modules\\\\Appointment\\\\Models\\\\Appointment","model_id":4,"uuid":"4c8aea69-d6f0-481d-902e-4cbcdcfb6cfb","collection_name":"medical_report","name":"myopd-sample-rx-gujarati","file_name":"67da9a153ce53.png","mime_type":"image\\/png","disk":"public","conversions_disk":"public","size":22382,"manipulations":[],"custom_properties":{"mime_type":"image\\/png","original_name":"myopd-sample-rx-gujarati.png"},"generated_conversions":{"thumb":true,"thumb300":true},"responsive_images":[],"order_column":1,"created_at":"2025-03-19T10:19:01.000000Z","updated_at":"2025-03-19T10:19:01.000000Z","original_url":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/310\\/67da9a153ce53.png","preview_url":""}]},"otp":"814266"}',
                'created_at' => '2025-03-19 17:06:07',
                'updated_at' => '2025-03-19 17:06:07',
                'deleted_at' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'appointment_id' => 4,
                'activity_date' => '2025-03-19 11:36:12',
                'activity_message' => 'Appointment In Progress',
                'activity_type' => 'in_progress_appointment',
                'activity_data' => '{"activity_type":"in_progress_appointment","activity_message":"Appointment In Progress","notification_type":"in_progress_appointment","appointment_id":4}',
                'created_at' => '2025-03-19 11:36:12',
                'updated_at' => '2025-03-19 11:36:12',
                'deleted_at' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'appointment_id' => 4,
                'activity_date' => '2025-03-19 17:08:17',
                'activity_message' => 'Otp Verified Successfully',
                'activity_type' => 'otp_verified',
                'activity_data' => '{"activity_type":"otp_verified","notification_type":"otp_verified","appointment_id":4,"collector":"-"}',
                'created_at' => '2025-03-19 17:08:17',
                'updated_at' => '2025-03-19 17:08:17',
                'deleted_at' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
                'appointment_id' => 4,
                'activity_date' => '2025-03-19 17:08:18',
                'activity_message' => 'Otp Verified Successfully',
                'activity_type' => 'otp_verified',
                'activity_data' => '{"activity_type":"otp_verified","notification_type":"otp_verified","appointment_id":4,"collector":"-"}',
                'created_at' => '2025-03-19 17:08:18',
                'updated_at' => '2025-03-19 17:08:18',
                'deleted_at' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
                'appointment_id' => 4,
                'activity_date' => '2025-03-19 11:38:20',
                'activity_message' => 'Appointment Completed Successfully',
                'activity_type' => 'completed_appointment',
                'activity_data' => '{"activity_type":"completed_appointment","activity_message":"Appointment Completed Successfully","notification_type":"completed_appointment","appointment_id":4}',
                'created_at' => '2025-03-19 11:38:20',
                'updated_at' => '2025-03-19 11:38:20',
                'deleted_at' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
                'appointment_id' => 4,
                'activity_date' => '2025-03-19 17:08:22',
                'activity_message' => 'Appointment Completed Successfully',
                'activity_type' => 'completed_appointment',
                'activity_data' => NULL,
                'created_at' => '2025-03-19 17:08:22',
                'updated_at' => '2025-03-19 17:08:22',
                'deleted_at' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'appointment_id' => 3,
                'activity_date' => '2025-03-19 17:20:46',
                'activity_message' => 'Collector Assigned Successfully',
                'activity_type' => 'collector_assigned',
                'activity_data' => '{"activity_type":"collector_assigned","notification_type":"collector_assigned","appointment_id":3,"activity_message":"Collector Assigned Successfully","collector":"Angela Perez","collector_id":"30"}',
                'created_at' => '2025-03-19 17:20:46',
                'updated_at' => '2025-03-19 17:20:46',
                'deleted_at' => NULL,
            ),
            29 => 
            array (
                'id' => 30,
                'appointment_id' => 3,
                'activity_date' => '2025-03-19 11:51:36',
                'activity_message' => 'Appointment On Going',
                'activity_type' => 'on_going_appointment',
                'activity_data' => '{"activity_type":"on_going_appointment","activity_message":"Appointment On Going","notification_type":"on_going_appointment","appointment_id":3}',
                'created_at' => '2025-03-19 11:51:36',
                'updated_at' => '2025-03-19 11:51:36',
                'deleted_at' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
                'appointment_id' => 3,
                'activity_date' => '2025-03-19 17:22:22',
                'activity_message' => 'Otp Generated Successfully',
                'activity_type' => 'otp_generated',
                'activity_data' => '{"activity_type":"otp_generated","notification_type":"otp_generated","appointment_id":3,"appointment":{"id":3,"status":"on_going","customer_id":21,"other_member_id":null,"vendor_id":6,"lab_id":6,"test_id":162,"test_type":"test_case","address_id":1,"appointment_date":"2025-03-18T18:30:00.000000Z","appointment_time":"2025-03-19T11:00:00.000000Z","amount":130,"test_discount_amount":130,"collection_type":"home","total_amount":141.9,"submission_status":"pending","test_case_status":null,"rejected_id":null,"by_suggestion":0,"cancellation_reason":null,"symptoms":"Fever","reschedule_reason":null,"created_by":21,"updated_by":30,"deleted_by":null,"created_at":"2025-03-19T10:17:27.000000Z","updated_at":"2025-03-19T11:51:32.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","appointment_collector_mapping":{"id":3,"appointment_id":3,"collector_id":30,"created_at":"2025-03-19T11:50:46.000000Z","updated_at":"2025-03-19T11:50:46.000000Z","deleted_at":null,"collector":{"id":30,"username":"angela_perez","first_name":"Angela","last_name":"Perez","email":"angela@gmail.com","mobile":"1-56587152","country_id":null,"state_id":null,"city_id":null,"address":"12, Oak Avenue, USA","login_type":null,"gender":"female","date_of_birth":"1990-09-20","email_verified_at":null,"is_verify":0,"is_banned":0,"is_subscribe":0,"is_available":1,"last_online_time":"17:21:21","status":1,"set_as_featured":0,"last_notification_seen":null,"user_type":"collector","social_image":null,"created_at":"2025-03-19T09:42:28.000000Z","updated_at":"2025-03-19T11:51:21.000000Z","deleted_at":null,"full_name":"Angela Perez","profile_image":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/30\\/qxGMeUEVQ5RKnHahvSIIW1vGdBIfoFBYfOa6IFuW.png","total_commission_amount":0,"total_appointments":0,"total_service_amount":0,"total_tax_amount":0,"total_admin_earnings":0,"total_vendor_earnings":0,"total_collector_earnings":0,"collector_paid_earnings":0,"vendor_paid_earnings":0,"user_commission_mapping":[],"media":[{"id":30,"model_type":"App\\\\Models\\\\User","model_id":30,"uuid":"d2333aa4-443a-44ea-86be-8b0b0ed66e68","collection_name":"profile_image","name":"angela","file_name":"qxGMeUEVQ5RKnHahvSIIW1vGdBIfoFBYfOa6IFuW.png","mime_type":"image\\/png","disk":"public","conversions_disk":"public","size":370165,"manipulations":[],"custom_properties":[],"generated_conversions":[],"responsive_images":[],"order_column":1,"created_at":"2025-03-19T09:42:29.000000Z","updated_at":"2025-03-19T09:42:29.000000Z","original_url":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/30\\/qxGMeUEVQ5RKnHahvSIIW1vGdBIfoFBYfOa6IFuW.png","preview_url":""}]}},"transactions":{"id":3,"appointment_id":3,"txn_id":null,"discount_type":"","discount_value":0,"discount_amount":0,"coupon_id":null,"coupon":null,"coupon_amount":0,"tax":"[{\\"id\\":1,\\"title\\":\\"Service Tax\\",\\"value\\":5,\\"type\\":\\"Percentage\\",\\"status\\":1},{\\"id\\":2,\\"title\\":\\"Home Collection Fee\\",\\"value\\":10,\\"type\\":\\"Fixed\\",\\"status\\":1}]","total_tax_amount":11.9,"total_amount":141.9,"payment_type":null,"payment_status":"pending","request_token":null,"created_at":"2025-03-19T10:17:27.000000Z","updated_at":"2025-03-19T10:17:27.000000Z","deleted_at":null},"media":[{"id":309,"model_type":"Modules\\\\Appointment\\\\Models\\\\Appointment","model_id":3,"uuid":"e20d2a7a-0a2e-4e09-88ac-198716dcf066","collection_name":"medical_report","name":"ColorRx-English-Logo-HTWT-Generics","file_name":"67da99b712d6b.png","mime_type":"image\\/png","disk":"public","conversions_disk":"public","size":132291,"manipulations":[],"custom_properties":{"mime_type":"image\\/png","original_name":"ColorRx-English-Logo-HTWT-Generics.png"},"generated_conversions":{"thumb":true,"thumb300":true},"responsive_images":[],"order_column":1,"created_at":"2025-03-19T10:17:27.000000Z","updated_at":"2025-03-19T10:17:27.000000Z","original_url":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/309\\/67da99b712d6b.png","preview_url":""}]},"otp":"455150"}',
                'created_at' => '2025-03-19 17:22:22',
                'updated_at' => '2025-03-19 17:22:22',
                'deleted_at' => NULL,
            ),
            31 => 
            array (
                'id' => 32,
                'appointment_id' => 3,
                'activity_date' => '2025-03-19 11:52:26',
                'activity_message' => 'Appointment In Progress',
                'activity_type' => 'in_progress_appointment',
                'activity_data' => '{"activity_type":"in_progress_appointment","activity_message":"Appointment In Progress","notification_type":"in_progress_appointment","appointment_id":3}',
                'created_at' => '2025-03-19 11:52:26',
                'updated_at' => '2025-03-19 11:52:26',
                'deleted_at' => NULL,
            ),
            32 => 
            array (
                'id' => 33,
                'appointment_id' => 3,
                'activity_date' => '2025-03-19 17:22:43',
                'activity_message' => 'Otp Verified Successfully',
                'activity_type' => 'otp_verified',
                'activity_data' => '{"activity_type":"otp_verified","notification_type":"otp_verified","appointment_id":3,"collector":"-"}',
                'created_at' => '2025-03-19 17:22:43',
                'updated_at' => '2025-03-19 17:22:43',
                'deleted_at' => NULL,
            ),
            33 => 
            array (
                'id' => 34,
                'appointment_id' => 3,
                'activity_date' => '2025-03-19 11:52:47',
                'activity_message' => 'Appointment Completed Successfully',
                'activity_type' => 'completed_appointment',
                'activity_data' => '{"activity_type":"completed_appointment","activity_message":"Appointment Completed Successfully","notification_type":"completed_appointment","appointment_id":3}',
                'created_at' => '2025-03-19 11:52:47',
                'updated_at' => '2025-03-19 11:52:47',
                'deleted_at' => NULL,
            ),
            34 => 
            array (
                'id' => 35,
                'appointment_id' => 3,
                'activity_date' => '2025-03-19 17:30:06',
                'activity_message' => 'Test Case Declined Successfully',
                'activity_type' => 'test_case_not_received',
                'activity_data' => '{"activity_type":"test_case_not_received","notification_type":"test_case_not_received","appointment_id":3,"appointment":{"id":3,"status":"completed","customer_id":21,"other_member_id":null,"vendor_id":6,"lab_id":6,"test_id":162,"test_type":"test_case","address_id":1,"appointment_date":"2025-03-18T18:30:00.000000Z","appointment_time":"2025-03-19T11:00:00.000000Z","amount":130,"test_discount_amount":130,"collection_type":"home","total_amount":141.9,"submission_status":"reject","test_case_status":null,"rejected_id":null,"by_suggestion":0,"cancellation_reason":null,"symptoms":"Fever","reschedule_reason":null,"created_by":21,"updated_by":1,"deleted_by":1,"created_at":"2025-03-19T10:17:27.000000Z","updated_at":"2025-03-19T12:00:06.000000Z","deleted_at":"2025-03-19T12:00:06.000000Z","feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","appointment_collector_mapping":{"id":3,"appointment_id":3,"collector_id":30,"created_at":"2025-03-19T11:50:46.000000Z","updated_at":"2025-03-19T11:50:46.000000Z","deleted_at":null,"collector":{"id":30,"username":"angela_perez","first_name":"Angela","last_name":"Perez","email":"angela@gmail.com","mobile":"1-56587152","country_id":null,"state_id":null,"city_id":null,"address":"12, Oak Avenue, USA","login_type":null,"gender":"female","date_of_birth":"1990-09-20","email_verified_at":null,"is_verify":0,"is_banned":0,"is_subscribe":0,"is_available":1,"last_online_time":"17:21:21","status":1,"set_as_featured":0,"last_notification_seen":null,"user_type":"collector","social_image":null,"created_at":"2025-03-19T09:42:28.000000Z","updated_at":"2025-03-19T11:51:21.000000Z","deleted_at":null,"full_name":"Angela Perez","profile_image":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/30\\/qxGMeUEVQ5RKnHahvSIIW1vGdBIfoFBYfOa6IFuW.png","total_commission_amount":0,"total_appointments":0,"total_service_amount":0,"total_tax_amount":0,"total_admin_earnings":0,"total_vendor_earnings":0,"total_collector_earnings":0,"collector_paid_earnings":0,"vendor_paid_earnings":0,"user_commission_mapping":[],"media":[{"id":30,"model_type":"App\\\\Models\\\\User","model_id":30,"uuid":"d2333aa4-443a-44ea-86be-8b0b0ed66e68","collection_name":"profile_image","name":"angela","file_name":"qxGMeUEVQ5RKnHahvSIIW1vGdBIfoFBYfOa6IFuW.png","mime_type":"image\\/png","disk":"public","conversions_disk":"public","size":370165,"manipulations":[],"custom_properties":[],"generated_conversions":[],"responsive_images":[],"order_column":1,"created_at":"2025-03-19T09:42:29.000000Z","updated_at":"2025-03-19T09:42:29.000000Z","original_url":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/30\\/qxGMeUEVQ5RKnHahvSIIW1vGdBIfoFBYfOa6IFuW.png","preview_url":""}]}},"transactions":{"id":3,"appointment_id":3,"txn_id":"pi_1R4LCvFTMa5P8ht0Yd9TzYm8","discount_type":"","discount_value":0,"discount_amount":0,"coupon_id":null,"coupon":null,"coupon_amount":0,"tax":"[{\\"id\\":1,\\"title\\":\\"Service Tax\\",\\"value\\":5,\\"type\\":\\"Percentage\\",\\"status\\":1},{\\"id\\":2,\\"title\\":\\"Home Collection Fee\\",\\"value\\":10,\\"type\\":\\"Fixed\\",\\"status\\":1}]","total_tax_amount":11.9,"total_amount":141.9,"payment_type":"stripe","payment_status":"paid","request_token":null,"created_at":"2025-03-19T10:17:27.000000Z","updated_at":"2025-03-19T11:57:05.000000Z","deleted_at":null},"media":[{"id":309,"model_type":"Modules\\\\Appointment\\\\Models\\\\Appointment","model_id":3,"uuid":"e20d2a7a-0a2e-4e09-88ac-198716dcf066","collection_name":"medical_report","name":"ColorRx-English-Logo-HTWT-Generics","file_name":"67da99b712d6b.png","mime_type":"image\\/png","disk":"public","conversions_disk":"public","size":132291,"manipulations":[],"custom_properties":{"mime_type":"image\\/png","original_name":"ColorRx-English-Logo-HTWT-Generics.png"},"generated_conversions":{"thumb":true,"thumb300":true},"responsive_images":[],"order_column":1,"created_at":"2025-03-19T10:17:27.000000Z","updated_at":"2025-03-19T10:17:27.000000Z","original_url":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/309\\/67da99b712d6b.png","preview_url":""}]}}',
                'created_at' => '2025-03-19 17:30:06',
                'updated_at' => '2025-03-19 17:30:06',
                'deleted_at' => NULL,
            ),
            35 => 
            array (
                'id' => 36,
                'appointment_id' => 9,
                'activity_date' => '2025-03-19 17:32:28',
                'activity_message' => 'Collector Assigned Successfully',
                'activity_type' => 'collector_assigned',
                'activity_data' => '{"activity_type":"collector_assigned","notification_type":"collector_assigned","appointment_id":9,"activity_message":"Collector Assigned Successfully","collector":"Angela Perez","collector_id":"30"}',
                'created_at' => '2025-03-19 17:32:28',
                'updated_at' => '2025-03-19 17:32:28',
                'deleted_at' => NULL,
            ),
            36 => 
            array (
                'id' => 37,
                'appointment_id' => 10,
                'activity_date' => '2025-03-19 17:49:26',
                'activity_message' => 'New appointment created',
                'activity_type' => 'add_appointment',
                'activity_data' => '{"activity_type":"add_appointment","notification_type":"new_appointment","appointment_id":10,"customer":"John Doe","test_name":"Multiparametric MRI Test"}',
                'created_at' => '2025-03-19 17:49:26',
                'updated_at' => '2025-03-19 17:49:26',
                'deleted_at' => NULL,
            ),
            37 => 
            array (
                'id' => 38,
                'appointment_id' => 10,
                'activity_date' => '2025-03-19 17:50:02',
                'activity_message' => 'Collector Assigned Successfully',
                'activity_type' => 'collector_assigned',
                'activity_data' => '{"activity_type":"collector_assigned","notification_type":"collector_assigned","appointment_id":10,"activity_message":"Collector Assigned Successfully","collector":"Harvey Francis","collector_id":"29"}',
                'created_at' => '2025-03-19 17:50:02',
                'updated_at' => '2025-03-19 17:50:02',
                'deleted_at' => NULL,
            ),
            38 => 
            array (
                'id' => 39,
                'appointment_id' => 10,
                'activity_date' => '2025-03-19 12:20:40',
                'activity_message' => 'Appointment On Going',
                'activity_type' => 'on_going_appointment',
                'activity_data' => '{"activity_type":"on_going_appointment","activity_message":"Appointment On Going","notification_type":"on_going_appointment","appointment_id":10}',
                'created_at' => '2025-03-19 12:20:40',
                'updated_at' => '2025-03-19 12:20:40',
                'deleted_at' => NULL,
            ),
            39 => 
            array (
                'id' => 40,
                'appointment_id' => 10,
                'activity_date' => '2025-03-19 17:51:02',
                'activity_message' => 'Otp Generated Successfully',
                'activity_type' => 'otp_generated',
            'activity_data' => '{"activity_type":"otp_generated","notification_type":"otp_generated","appointment_id":10,"appointment":{"id":10,"status":"on_going","customer_id":3,"other_member_id":null,"vendor_id":4,"lab_id":2,"test_id":186,"test_type":"test_case","address_id":2,"appointment_date":"2025-03-19T18:30:00.000000Z","appointment_time":"2025-03-19T06:10:00.000000Z","amount":300,"test_discount_amount":300,"collection_type":"home","total_amount":331,"submission_status":"pending","test_case_status":null,"rejected_id":null,"by_suggestion":0,"cancellation_reason":null,"symptoms":"Fever","reschedule_reason":null,"created_by":3,"updated_by":29,"deleted_by":null,"created_at":"2025-03-19T12:19:26.000000Z","updated_at":"2025-03-19T12:20:38.000000Z","deleted_at":null,"feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","appointment_collector_mapping":{"id":5,"appointment_id":10,"collector_id":29,"created_at":"2025-03-19T12:20:02.000000Z","updated_at":"2025-03-19T12:20:02.000000Z","deleted_at":null,"collector":{"id":29,"username":"harvey_francis","first_name":"Harvey","last_name":"Francis","email":"harvey@gmail.com","mobile":"1-45789655","country_id":null,"state_id":null,"city_id":null,"address":"56, Main Street, USA","login_type":null,"gender":"male","date_of_birth":"1994-07-02","email_verified_at":null,"is_verify":0,"is_banned":0,"is_subscribe":0,"is_available":1,"last_online_time":"17:50:31","status":1,"set_as_featured":0,"last_notification_seen":"2025-03-19 17:05:51","user_type":"collector","social_image":null,"created_at":"2025-03-19T09:42:28.000000Z","updated_at":"2025-03-19T12:20:31.000000Z","deleted_at":null,"full_name":"Harvey Francis","profile_image":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/29\\/rduFDx2zMC4ZSzL93Bo6DAtmVDAlPSWpocR1KB8J.png","total_commission_amount":0,"total_appointments":1,"total_service_amount":60,"total_tax_amount":11.9,"total_admin_earnings":0,"total_vendor_earnings":0,"total_collector_earnings":14.2,"collector_paid_earnings":14.2,"vendor_paid_earnings":0,"user_commission_mapping":[],"media":[{"id":29,"model_type":"App\\\\Models\\\\User","model_id":29,"uuid":"7008bcd3-c743-4d9d-baad-59a2c3459d95","collection_name":"profile_image","name":"harvey","file_name":"rduFDx2zMC4ZSzL93Bo6DAtmVDAlPSWpocR1KB8J.png","mime_type":"image\\/png","disk":"public","conversions_disk":"public","size":346228,"manipulations":[],"custom_properties":[],"generated_conversions":[],"responsive_images":[],"order_column":1,"created_at":"2025-03-19T09:42:29.000000Z","updated_at":"2025-03-19T09:42:29.000000Z","original_url":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/29\\/rduFDx2zMC4ZSzL93Bo6DAtmVDAlPSWpocR1KB8J.png","preview_url":""}]}},"transactions":{"id":10,"appointment_id":10,"txn_id":null,"discount_type":"","discount_value":0,"discount_amount":0,"coupon_id":null,"coupon":null,"coupon_amount":0,"tax":"[{\\"id\\":1,\\"title\\":\\"Service Tax\\",\\"value\\":5,\\"type\\":\\"Percentage\\",\\"status\\":1},{\\"id\\":2,\\"title\\":\\"Home Collection Fee\\",\\"value\\":10,\\"type\\":\\"Fixed\\",\\"status\\":1},{\\"id\\":3,\\"title\\":\\"State Health Tax\\",\\"value\\":2,\\"type\\":\\"Percentage\\",\\"status\\":1}]","total_tax_amount":31,"total_amount":331,"payment_type":null,"payment_status":"pending","request_token":null,"created_at":"2025-03-19T12:19:26.000000Z","updated_at":"2025-03-19T12:19:26.000000Z","deleted_at":null},"media":[{"id":321,"model_type":"Modules\\\\Appointment\\\\Models\\\\Appointment","model_id":10,"uuid":"c1f86fde-c8f9-4d23-ab05-2c7849cf7632","collection_name":"medical_report","name":"Medical_Report_1 (3)","file_name":"67dab64e44afb.pdf","mime_type":"application\\/pdf","disk":"public","conversions_disk":"public","size":1467,"manipulations":[],"custom_properties":{"mime_type":"application\\/pdf","original_name":"Medical_Report_1 (3).pdf"},"generated_conversions":[],"responsive_images":[],"order_column":1,"created_at":"2025-03-19T12:19:26.000000Z","updated_at":"2025-03-19T12:19:26.000000Z","original_url":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/321\\/67dab64e44afb.pdf","preview_url":""}]},"otp":"668019"}',
                'created_at' => '2025-03-19 17:51:02',
                'updated_at' => '2025-03-19 17:51:02',
                'deleted_at' => NULL,
            ),
            40 => 
            array (
                'id' => 41,
                'appointment_id' => 10,
                'activity_date' => '2025-03-19 12:21:07',
                'activity_message' => 'Appointment In Progress',
                'activity_type' => 'in_progress_appointment',
                'activity_data' => '{"activity_type":"in_progress_appointment","activity_message":"Appointment In Progress","notification_type":"in_progress_appointment","appointment_id":10}',
                'created_at' => '2025-03-19 12:21:07',
                'updated_at' => '2025-03-19 12:21:07',
                'deleted_at' => NULL,
            ),
            41 => 
            array (
                'id' => 42,
                'appointment_id' => 10,
                'activity_date' => '2025-03-19 17:51:46',
                'activity_message' => 'Otp Verified Successfully',
                'activity_type' => 'otp_verified',
                'activity_data' => '{"activity_type":"otp_verified","notification_type":"otp_verified","appointment_id":10,"collector":"-"}',
                'created_at' => '2025-03-19 17:51:46',
                'updated_at' => '2025-03-19 17:51:46',
                'deleted_at' => NULL,
            ),
            42 => 
            array (
                'id' => 43,
                'appointment_id' => 10,
                'activity_date' => '2025-03-19 17:51:47',
                'activity_message' => 'Otp Verified Successfully',
                'activity_type' => 'otp_verified',
                'activity_data' => '{"activity_type":"otp_verified","notification_type":"otp_verified","appointment_id":10,"collector":"-"}',
                'created_at' => '2025-03-19 17:51:47',
                'updated_at' => '2025-03-19 17:51:47',
                'deleted_at' => NULL,
            ),
            43 => 
            array (
                'id' => 44,
                'appointment_id' => 10,
                'activity_date' => '2025-03-19 12:21:49',
                'activity_message' => 'Appointment Completed Successfully',
                'activity_type' => 'completed_appointment',
                'activity_data' => '{"activity_type":"completed_appointment","activity_message":"Appointment Completed Successfully","notification_type":"completed_appointment","appointment_id":10}',
                'created_at' => '2025-03-19 12:21:49',
                'updated_at' => '2025-03-19 12:21:49',
                'deleted_at' => NULL,
            ),
            44 => 
            array (
                'id' => 45,
                'appointment_id' => 10,
                'activity_date' => '2025-03-19 17:51:51',
                'activity_message' => 'Appointment Completed Successfully',
                'activity_type' => 'completed_appointment',
                'activity_data' => NULL,
                'created_at' => '2025-03-19 17:51:51',
                'updated_at' => '2025-03-19 17:51:51',
                'deleted_at' => NULL,
            ),
            45 => 
            array (
                'id' => 46,
                'appointment_id' => 10,
                'activity_date' => '2025-03-19 17:56:20',
                'activity_message' => 'Test Case Declined Successfully',
                'activity_type' => 'test_case_not_received',
            'activity_data' => '{"activity_type":"test_case_not_received","notification_type":"test_case_not_received","appointment_id":10,"appointment":{"id":10,"status":"completed","customer_id":3,"other_member_id":null,"vendor_id":4,"lab_id":2,"test_id":186,"test_type":"test_case","address_id":2,"appointment_date":"2025-03-19T18:30:00.000000Z","appointment_time":"2025-03-19T06:10:00.000000Z","amount":300,"test_discount_amount":300,"collection_type":"home","total_amount":331,"submission_status":"reject","test_case_status":null,"rejected_id":null,"by_suggestion":0,"cancellation_reason":null,"symptoms":"Fever","reschedule_reason":null,"created_by":3,"updated_by":1,"deleted_by":1,"created_at":"2025-03-19T12:19:26.000000Z","updated_at":"2025-03-19T12:26:20.000000Z","deleted_at":"2025-03-19T12:26:20.000000Z","feature_image":"https:\\/\\/dummyimage.com\\/600x300\\/cfcfcf\\/000000.png","appointment_collector_mapping":{"id":5,"appointment_id":10,"collector_id":29,"created_at":"2025-03-19T12:20:02.000000Z","updated_at":"2025-03-19T12:20:02.000000Z","deleted_at":null,"collector":{"id":29,"username":"harvey_francis","first_name":"Harvey","last_name":"Francis","email":"harvey@gmail.com","mobile":"1-45789655","country_id":null,"state_id":null,"city_id":null,"address":"56, Main Street, USA","login_type":null,"gender":"male","date_of_birth":"1994-07-02","email_verified_at":null,"is_verify":0,"is_banned":0,"is_subscribe":0,"is_available":1,"last_online_time":"17:50:31","status":1,"set_as_featured":0,"last_notification_seen":"2025-03-19 17:05:51","user_type":"collector","social_image":null,"created_at":"2025-03-19T09:42:28.000000Z","updated_at":"2025-03-19T12:20:31.000000Z","deleted_at":null,"full_name":"Harvey Francis","profile_image":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/29\\/rduFDx2zMC4ZSzL93Bo6DAtmVDAlPSWpocR1KB8J.png","total_commission_amount":0,"total_appointments":1,"total_service_amount":60,"total_tax_amount":11.9,"total_admin_earnings":0,"total_vendor_earnings":0,"total_collector_earnings":14.2,"collector_paid_earnings":14.2,"vendor_paid_earnings":0,"user_commission_mapping":[],"media":[{"id":29,"model_type":"App\\\\Models\\\\User","model_id":29,"uuid":"7008bcd3-c743-4d9d-baad-59a2c3459d95","collection_name":"profile_image","name":"harvey","file_name":"rduFDx2zMC4ZSzL93Bo6DAtmVDAlPSWpocR1KB8J.png","mime_type":"image\\/png","disk":"public","conversions_disk":"public","size":346228,"manipulations":[],"custom_properties":[],"generated_conversions":[],"responsive_images":[],"order_column":1,"created_at":"2025-03-19T09:42:29.000000Z","updated_at":"2025-03-19T09:42:29.000000Z","original_url":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/29\\/rduFDx2zMC4ZSzL93Bo6DAtmVDAlPSWpocR1KB8J.png","preview_url":""}]}},"transactions":{"id":10,"appointment_id":10,"txn_id":"pi_1R4LgDFTMa5P8ht0Xn7JgwBO","discount_type":"","discount_value":0,"discount_amount":0,"coupon_id":null,"coupon":null,"coupon_amount":0,"tax":"[{\\"id\\":1,\\"title\\":\\"Service Tax\\",\\"value\\":5,\\"type\\":\\"Percentage\\",\\"status\\":1},{\\"id\\":2,\\"title\\":\\"Home Collection Fee\\",\\"value\\":10,\\"type\\":\\"Fixed\\",\\"status\\":1},{\\"id\\":3,\\"title\\":\\"State Health Tax\\",\\"value\\":2,\\"type\\":\\"Percentage\\",\\"status\\":1}]","total_tax_amount":31,"total_amount":331,"payment_type":"stripe","payment_status":"paid","request_token":null,"created_at":"2025-03-19T12:19:26.000000Z","updated_at":"2025-03-19T12:25:47.000000Z","deleted_at":null},"media":[{"id":321,"model_type":"Modules\\\\Appointment\\\\Models\\\\Appointment","model_id":10,"uuid":"c1f86fde-c8f9-4d23-ab05-2c7849cf7632","collection_name":"medical_report","name":"Medical_Report_1 (3)","file_name":"67dab64e44afb.pdf","mime_type":"application\\/pdf","disk":"public","conversions_disk":"public","size":1467,"manipulations":[],"custom_properties":{"mime_type":"application\\/pdf","original_name":"Medical_Report_1 (3).pdf"},"generated_conversions":[],"responsive_images":[],"order_column":1,"created_at":"2025-03-19T12:19:26.000000Z","updated_at":"2025-03-19T12:19:26.000000Z","original_url":"https:\\/\\/apps.iqonic.design\\/kivilabs\\/storage\\/321\\/67dab64e44afb.pdf","preview_url":""}]}}',
                'created_at' => '2025-03-19 17:56:20',
                'updated_at' => '2025-03-19 17:56:20',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}