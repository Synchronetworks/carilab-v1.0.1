<?php

namespace Modules\Commision\database\seeders;

use Illuminate\Database\Seeder;

class CommissionEarningsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('commission_earnings')->delete();
        
        \DB::table('commission_earnings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'employee_id' => 4,
                'commissionable_type' => 'Modules\\Appointment\\Models\\Appointment',
                'commissionable_id' => 1,
                'commission_amount' => 77.2,
                'user_type' => 'vendor',
                'commission_status' => 'unpaid',
                'commissions' => NULL,
                'payment_date' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 16:28:00',
                'updated_at' => '2025-03-19 16:28:00',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'employee_id' => 24,
                'commissionable_type' => 'Modules\\Appointment\\Models\\Appointment',
                'commissionable_id' => 1,
                'commission_amount' => 59.8,
                'user_type' => 'collector',
                'commission_status' => 'unpaid',
                'commissions' => NULL,
                'payment_date' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 16:28:00',
                'updated_at' => '2025-03-19 16:28:00',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'employee_id' => 1,
                'commissionable_type' => 'Modules\\Appointment\\Models\\Appointment',
                'commissionable_id' => 1,
                'commission_amount' => 303.0,
                'user_type' => 'admin',
                'commission_status' => 'paid',
                'commissions' => NULL,
                'payment_date' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 16:28:00',
                'updated_at' => '2025-03-19 16:28:00',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'employee_id' => 4,
                'commissionable_type' => 'Modules\\Appointment\\Models\\Appointment',
                'commissionable_id' => 4,
                'commission_amount' => 8.8,
                'user_type' => 'vendor',
                'commission_status' => 'pending',
                'commissions' => NULL,
                'payment_date' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 17:08:39',
                'updated_at' => '2025-03-19 17:08:39',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'employee_id' => 29,
                'commissionable_type' => 'Modules\\Appointment\\Models\\Appointment',
                'commissionable_id' => 4,
                'commission_amount' => 14.2,
                'user_type' => 'collector',
                'commission_status' => 'paid',
                'commissions' => NULL,
                'payment_date' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 17:08:39',
                'updated_at' => '2025-03-19 17:16:01',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'employee_id' => 1,
                'commissionable_type' => 'Modules\\Appointment\\Models\\Appointment',
                'commissionable_id' => 4,
                'commission_amount' => 37.0,
                'user_type' => 'admin',
                'commission_status' => 'pending',
                'commissions' => NULL,
                'payment_date' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 17:08:39',
                'updated_at' => '2025-03-19 17:08:39',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'employee_id' => 6,
                'commissionable_type' => 'Modules\\Appointment\\Models\\Appointment',
                'commissionable_id' => 3,
                'commission_amount' => 21.4,
                'user_type' => 'vendor',
                'commission_status' => 'unpaid',
                'commissions' => NULL,
                'payment_date' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 17:27:05',
                'updated_at' => '2025-03-19 17:27:05',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'employee_id' => 30,
                'commissionable_type' => 'Modules\\Appointment\\Models\\Appointment',
                'commissionable_id' => 3,
                'commission_amount' => 22.6,
                'user_type' => 'collector',
                'commission_status' => 'unpaid',
                'commissions' => NULL,
                'payment_date' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 17:27:05',
                'updated_at' => '2025-03-19 17:27:05',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'employee_id' => 1,
                'commissionable_type' => 'Modules\\Appointment\\Models\\Appointment',
                'commissionable_id' => 3,
                'commission_amount' => 86.0,
                'user_type' => 'admin',
                'commission_status' => 'paid',
                'commissions' => NULL,
                'payment_date' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 17:27:05',
                'updated_at' => '2025-03-19 17:27:05',
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'employee_id' => 4,
                'commissionable_type' => 'Modules\\Appointment\\Models\\Appointment',
                'commissionable_id' => 10,
                'commission_amount' => 52.0,
                'user_type' => 'vendor',
                'commission_status' => 'unpaid',
                'commissions' => NULL,
                'payment_date' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 17:55:47',
                'updated_at' => '2025-03-19 17:55:47',
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'employee_id' => 29,
                'commissionable_type' => 'Modules\\Appointment\\Models\\Appointment',
                'commissionable_id' => 10,
                'commission_amount' => 43.0,
                'user_type' => 'collector',
                'commission_status' => 'unpaid',
                'commissions' => NULL,
                'payment_date' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 17:55:47',
                'updated_at' => '2025-03-19 17:55:47',
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'employee_id' => 1,
                'commissionable_type' => 'Modules\\Appointment\\Models\\Appointment',
                'commissionable_id' => 10,
                'commission_amount' => 205.0,
                'user_type' => 'admin',
                'commission_status' => 'paid',
                'commissions' => NULL,
                'payment_date' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 17:55:47',
                'updated_at' => '2025-03-19 17:55:47',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}