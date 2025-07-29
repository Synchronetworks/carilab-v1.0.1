<?php

namespace Modules\FAQ\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\FAQ\Models\FAQ;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
class FAQDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $faqs = [
            [
                'question' => 'What is KiviLabs?',
                'answer' => 'KiviLabs is a laboratory appointment management system that helps users book lab tests, manage appointments, and ensure efficient sample collection.',
                'status' => 1
            ],
            [
                'question' => 'How can I book a lab test?',
                'answer' => 'You can book a lab test by logging in, selecting a lab, choosing the desired test or package, and confirming your appointment.',
                'status' => 1
            ],
            [
                'question' => 'Why am I unable to book an appointment?',
                'answer' => 'Ensure that all required fields, such as test type, date, and time, are filled correctly. Verify your internet connection. If the problem persists, contact customer support.',
                'status' => 1
            ],
            [
                'question' => 'What types of tests are available?',
                'answer' => 'KiviLabs offers various tests, including blood tests, urine tests, diagnostic tests, and more. The availability depends on the selected lab.',
                'status' => 1
            ],
            [
                'question' => 'Can I book a home sample collection?',
                'answer' => 'Yes, you can book a home sample collection. The assigned collector will visit your location to collect the required samples.',
                'status' => 1
            ],
            [
                'question' => 'What payment methods are supported?',
                'answer' => 'KiviLabs supports online payments Stripe, Paypal etc. and offline payments at the lab.',
                'status' => 1
            ],
            [
                'question' => 'Can I apply discounts or coupons?',
                'answer' => 'Yes, you can apply coupons during booking to avail discounts on tests or packages.',
                'status' => 1
            ],
            [
                'question' => 'How can I track my appointment status?',
                'answer' => 'You can track your appointment status (Scheduled, In Progress, Completed) from the "My Appointments" section in your account.',
                'status' => 1
            ],
            [
                'question' => 'What is the role of the collector?',
                'answer' => 'The collector visits your home to collect samples for lab tests. They are assigned based on location and availability.',
                'status' => 1
            ],
            [
                'question' => 'What is the cancellation policy?',
                'answer' => 'Appointments can be canceled before the scheduled date. Refund policies depend on the specific lab\'s terms.',
                'status' => 1
            ],
            [
                'question' => 'How can vendors manage their labs?',
                'answer' => 'Vendors can manage tests, packages, collectors, appointments, and promotions through their vendor login.',
                'status' => 1
            ],
            [
                'question' => 'What are the subscription plans for vendors?',
                'answer' => 'Vendors can choose subscription plans to manage their laboratories and benefit from additional features like promotions and analytics.',
                'status' => 1
            ],
            [
                'question' => 'How does KiviLabs notify users?',
                'answer' => 'KiviLabs sends real-time notifications for appointment updates, payment confirmations.',
                'status' => 1
            ],
            [
                'question' => 'What are the benefits of using packages?',
                'answer' => 'Packages bundle multiple tests at discounted rates, making them cost-effective for customers.',
                'status' => 1
            ],
            [
                'question' => 'Can I reschedule an appointment?',
                'answer' => 'Yes, you can reschedule an appointment from your account before the appointment date.',
                'status' => 1
            ],
            [
                'question' => 'How are test reports shared?',
                'answer' => 'Test reports are uploaded by the lab and can be accessed from your account once ready.',
                'status' => 1
            ],
            [
                'question' => 'What happens if the collector misses the appointment?',
                'answer' => 'If a collector misses an appointment, you can contact support or reschedule the collection.',
                'status' => 1
            ],
            [
                'question' => 'How do I reset my password?',
                'answer' => 'You can reset your password by clicking "Forgot Password" on the login page and following the instructions sent to your email.',
                'status' => 1
            ],
            [
                'question' => 'Is my data secure with KiviLabs?',
                'answer' => 'Yes, KiviLabs ensures all user data is encrypted and complies with industry-standard security protocols.',
                'status' => 1
            ]
        ];

        if (env('IS_DUMMY_DATA')) {
            foreach ($faqs as $data) {
                FAQ::create($data);
            }

            Schema::enableForeignKeyConstraints();
        }

    }
}
