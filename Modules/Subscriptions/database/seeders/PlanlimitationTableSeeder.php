<?php

namespace Modules\Subscriptions\database\seeders;

use Illuminate\Database\Seeder;

class PlanlimitationTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('planlimitation')->delete();
    
        \DB::table('planlimitation')->insert([
            [
                'id' => 1,
                'title' => 'Number of Laboratories',
                'slug' => 'number-of-laboratories',
                'description' => 'The maximum number of laboratories that can be registered under this plan is 1.',
                'status' => 1,
                'created_by' => 2,
                'updated_by' => 2,
                'deleted_by' => NULL,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => NULL,
            ],
            [
                'id' => 2,
                'title' => 'Number of Collectors',
                'slug' => 'number-of-collectors',
                'description' => 'This plan allows up to 3 collectors to be registered for sample collection services.',
                'status' => 1,
                'created_by' => 2,
                'updated_by' => 2,
                'deleted_by' => NULL,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => NULL,
            ],
            [
                'id' => 3,
                'title' => 'Number of Test Case',
                'slug' => 'number-of-test-case',
                'description' => 'The maximum number of laboratories that can be registered under this plan is 3.',
                'status' => 1,
                'created_by' => 2,
                'updated_by' => 2,
                'deleted_by' => NULL,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => NULL,
            ],
            [
                'id' => 4,
                'title' => 'Number of Test Package',
                'slug' => 'number-of-test-package',
                'description' => 'The maximum number of laboratories that can be registered under this plan is 2.',
                'status' => 1,
                'created_by' => 2,
                'updated_by' => 2,
                'deleted_by' => NULL,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => NULL,
            ],
            [
                'id' => 5,
                'title' => 'Enable WhatsApp Notification',
                'slug' => 'enable-whatsapp-notification',
                'description' => 'This plan allows whataapp notifications to be enabled for patient visits.',
                'status' => 1,
                'created_by' => 2,
                'updated_by' => 2,
                'deleted_by' => NULL,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => NULL,
            ],
            [
                'id' => 6,
                'title' => 'Enable SMS Notification',
                'slug' => 'enable-sms-notification',
                'description' => 'This plan allows SMS notifications to be enabled for patient visits.',
                'status' => 1,
                'created_by' => 2,
                'updated_by' => 2,
                'deleted_by' => NULL,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => NULL,
            ],
        ]);
    }
    
}