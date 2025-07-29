<?php

namespace Modules\Appointment\database\seeders;

use Illuminate\Database\Seeder;

class AppointmentStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('appointment_status')->delete();
        
        \DB::table('appointment_status')->insert(array (
            0 => 
            array (
                'created_at' => '2021-05-30 16:47:08',
                'id' => 1,
                'label' => 'Pending',
                'sequence' => 1,
                'status' => 1,
                'updated_at' => '2021-05-30 16:47:21',
                'value' => 'pending',
            ),
            1 => 
            array (
                'created_at' => '2021-05-30 16:50:40',
                'id' => 2,
                'label' => 'Accepted',
                'sequence' => 2,
                'status' => 1,
                'updated_at' => '2021-05-30 16:50:44',
                'value' => 'accept',
            ),
            2 => 
            array (
                'created_at' => '2021-05-30 16:50:46',
                'id' => 3,
                'label' => 'Ongoing',
                'sequence' => 3,
                'status' => 1,
                'updated_at' => '2021-05-30 16:50:48',
                'value' => 'on_going',
            ),
            3 => 
            array (
                'created_at' => '2021-05-30 16:50:50',
                'id' => 4,
                'label' => 'In Progress',
                'sequence' => 4,
                'status' => 1,
                'updated_at' => '2021-05-30 16:50:52',
                'value' => 'in_progress',
            ),
            4 => 
            array (
                'created_at' => '2021-05-30 16:55:03',
                'id' => 5,
                'label' => 'Cancelled',
                'value' => 'cancelled',
                'sequence' => 5,
                'status' => 1,
                'updated_at' => '2021-05-30 16:55:05',
            ),
            5 => 
            array (
                'id' => 6,
                'label' => 'Completed',
                'value' => 'completed',
                'sequence' => 6,
                'status' => 1,
                'updated_at' => '2021-05-30 16:55:12',
                'created_at' => '2021-05-30 16:55:11',
            ),
            6 => 
            array (
                'id' => 7,
                'label' => 'Reject',
                'value' => 'rejected',
                'sequence' => 7,
                'status' => 1,
                'updated_at' => '2021-05-30 16:55:12',
                'created_at' => '2021-05-30 16:55:11',
            ),
            7 => 
            array (
                'id' => 8,
                'label' => 'Reschedule',
                'value' => 'reschedule',
                'sequence' => 7,
                'status' => 1,
                'updated_at' => '2021-05-30 16:55:12',
                'created_at' => '2021-05-30 16:55:11',
            ),
           
        ));
    }
}
