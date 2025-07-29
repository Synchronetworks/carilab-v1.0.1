<?php

namespace Modules\Subscriptions\database\seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SubscriptionsTableSeeder extends Seeder
{

    /**
     *
     * @return void
     */
    public function run()
    {
        \DB::table('subscriptions')->delete();
        
        \DB::table('subscriptions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'plan_id' => 1,
                'user_id' => 4,
                'start_date' => '2025-03-19 15:15:19',
                'end_date' => '2025-03-26 15:15:19',
                'status' => 'active',
                'amount' => 5.0,
                'discount_percentage' => NULL,
                'tax_amount' => 10.35,
                'total_amount' => 5.0,
                'name' => 'Basic',
                'identifier' => 'basic',
                'type' => 'week',
                'duration' => 1,
                'level' => 1,
                'plan_type' => '',
                'payment_id' => NULL,
                'device_id' => '1',
                'created_by' => 4,
                'updated_by' => 4,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-03-19 15:15:19',
                'updated_at' => '2025-03-19 15:15:19',
            ),
            1 => 
            array (
                'id' => 2,
                'plan_id' => 1,
                'user_id' => 8,
                'start_date' => '2025-03-19 15:16:39',
                'end_date' => '2025-03-26 15:16:39',
                'status' => 'active',
                'amount' => 5.0,
                'discount_percentage' => NULL,
                'tax_amount' => 10.35,
                'total_amount' => 5.0,
                'name' => 'Basic',
                'identifier' => 'basic',
                'type' => 'week',
                'duration' => 1,
                'level' => 1,
                'plan_type' => '',
                'payment_id' => NULL,
                'device_id' => '1',
                'created_by' => 8,
                'updated_by' => 8,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-03-19 15:16:39',
                'updated_at' => '2025-03-19 15:16:39',
            ),
            2 => 
            array (
                'id' => 3,
                'plan_id' => 1,
                'user_id' => 6,
                'start_date' => '2025-03-19 15:17:46',
                'end_date' => '2025-03-26 15:17:46',
                'status' => 'active',
                'amount' => 5.0,
                'discount_percentage' => NULL,
                'tax_amount' => 10.35,
                'total_amount' => 5.0,
                'name' => 'Basic',
                'identifier' => 'basic',
                'type' => 'week',
                'duration' => 1,
                'level' => 1,
                'plan_type' => '',
                'payment_id' => NULL,
                'device_id' => '1',
                'created_by' => 6,
                'updated_by' => 6,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-03-19 15:17:46',
                'updated_at' => '2025-03-19 15:17:46',
            ),
        ));
    }
}

