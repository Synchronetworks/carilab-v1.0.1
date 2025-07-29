<?php

namespace Modules\Subscriptions\database\seeders;

use Illuminate\Database\Seeder;

class PlanlimitationMappingTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('planlimitation_mapping')->delete();
    
        \DB::table('planlimitation_mapping')->insert([
            // Plan 1
            [
                'id' => 1,
                'plan_id' => 1,
                'planlimitation_id' => 1,
                'limitation_slug' => 'number-of-laboratories',
                'limitation_value' => 1,
                'limit' => 1,
            ],
            [
                'id' => 2,
                'plan_id' => 1,
                'planlimitation_id' => 2,
                'limitation_slug' => 'number-of-collectors',
                'limitation_value' => 1,
                'limit' => 3,
            ],
            [
                'id' => 3,
                'plan_id' => 1,
                'planlimitation_id' => 3,
                'limitation_slug' => 'number-of-test-case',
                'limitation_value' => 1,
                'limit' => 20,
            ],
    
            // Plan 2
            [
                'id' => 4,
                'plan_id' => 2,
                'planlimitation_id' => 1,
                'limitation_slug' => 'number-of-laboratories',
                'limitation_value' => 1,
                'limit' => 2,
            ],
            [
                'id' => 5,
                'plan_id' => 2,
                'planlimitation_id' => 2,
                'limitation_slug' => 'number-of-collectors',
                'limitation_value' => 1,
                'limit' => 5,
            ],
            [
                'id' => 6,
                'plan_id' => 2,
                'planlimitation_id' => 3,
                'limitation_slug' => 'number-of-test-case',
                'limitation_value' => 1,
                'limit' => 40,
            ],
            [
                'id' => 7,
                'plan_id' => 2,
                'planlimitation_id' => 4,
                'limitation_slug' => 'number-of-test-package',
                'limitation_value' => 1,
                'limit' => 20,
            ],
    
            // Plan 3
            [
                'id' => 8,
                'plan_id' => 3,
                'planlimitation_id' => 1,
                'limitation_slug' => 'number-of-laboratories',
                'limitation_value' => 1,
                'limit' => 3,
            ],
            [
                'id' => 9,
                'plan_id' => 3,
                'planlimitation_id' => 2,
                'limitation_slug' => 'number-of-collectors',
                'limitation_value' => 1,
                'limit' => 10,
            ],
            [
                'id' => 10,
                'plan_id' => 3,
                'planlimitation_id' => 3,
                'limitation_slug' => 'number-of-test-case',
                'limitation_value' => 1,
                'limit' => 60,
            ],
            [
                'id' => 11,
                'plan_id' => 3,
                'planlimitation_id' => 4,
                'limitation_slug' => 'number-of-test-package',
                'limitation_value' => 1,
                'limit' => 30,
            ],
            [
                'id' => 12,
                'plan_id' => 3,
                'planlimitation_id' => 5,
                'limitation_slug' => 'enable-whatsapp-notification',
                'limitation_value' => 1,
                'limit' => null,
            ],
            [
                'id' => 13,
                'plan_id' => 3,
                'planlimitation_id' => 6,
                'limitation_slug' => 'enable-sms-notification',
                'limitation_value' => 1,
                'limit' => null,
            ],
           
        ]);
    }
    
}