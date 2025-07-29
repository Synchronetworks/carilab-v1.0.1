<?php

namespace Modules\Appointment\database\seeders;

use Illuminate\Database\Seeder;

class CashPaymentHistoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cash_payment_histories')->delete();
        
        \DB::table('cash_payment_histories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'transaction_id' => 4,
                'appointment_id' => 4,
                'action' => 'customer_send_collector',
                'text' => 'John Doe successfully transfer $71.90 to Harvey Francis',
                'type' => 'cash',
                'sender_id' => 3,
                'receiver_id' => 29,
                'datetime' => '2025-03-19 17:08:39',
                'status' => 'pending_by_collector',
                'total_amount' => 71.9,
                'parent_id' => NULL,
                'created_at' => '2025-03-19 17:08:39',
                'updated_at' => '2025-03-19 17:08:39',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'transaction_id' => 4,
                'appointment_id' => 4,
                'action' => 'collector_approved_cash',
                'text' => '$71.90 successfully approved by Harvey Francis',
                'type' => 'cash',
                'sender_id' => 3,
                'receiver_id' => 29,
                'datetime' => '2025-03-19 17:09:05',
                'status' => 'approved_by_collector',
                'total_amount' => 71.9,
                'parent_id' => 1,
                'created_at' => '2025-03-19 17:09:05',
                'updated_at' => '2025-03-19 17:09:05',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'transaction_id' => 4,
                'appointment_id' => 4,
                'action' => 'collector_send_vendor',
                'text' => 'Harvey Francis successfully transfer $57.70 to Liam Long',
                'type' => 'cash',
                'sender_id' => 29,
                'receiver_id' => 4,
                'datetime' => '2025-03-19 17:16:01',
                'status' => 'send_to_vendor',
                'total_amount' => 57.7,
                'parent_id' => 1,
                'created_at' => '2025-03-19 17:16:01',
                'updated_at' => '2025-03-19 17:16:01',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}