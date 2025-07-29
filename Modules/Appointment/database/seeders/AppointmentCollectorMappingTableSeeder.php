<?php

namespace Modules\Appointment\database\seeders;

use Illuminate\Database\Seeder;

class AppointmentCollectorMappingTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('appointment_collector_mapping')->delete();
        
        \DB::table('appointment_collector_mapping')->insert(array (
            0 => 
            array (
                'id' => 1,
                'appointment_id' => 1,
                'collector_id' => 24,
                'created_at' => '2025-03-19 16:02:16',
                'updated_at' => '2025-03-19 16:02:16',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'appointment_id' => 4,
                'collector_id' => 29,
                'created_at' => '2025-03-19 17:04:15',
                'updated_at' => '2025-03-19 17:04:15',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'appointment_id' => 3,
                'collector_id' => 30,
                'created_at' => '2025-03-19 17:20:46',
                'updated_at' => '2025-03-19 17:30:06',
                'deleted_at' => '2025-03-19 17:30:06',
            ),
            3 => 
            array (
                'id' => 4,
                'appointment_id' => 9,
                'collector_id' => 30,
                'created_at' => '2025-03-19 17:32:28',
                'updated_at' => '2025-03-19 17:32:28',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'appointment_id' => 10,
                'collector_id' => 29,
                'created_at' => '2025-03-19 17:50:02',
                'updated_at' => '2025-03-19 17:56:20',
                'deleted_at' => '2025-03-19 17:56:20',
            ),
        ));
        
        
    }
}