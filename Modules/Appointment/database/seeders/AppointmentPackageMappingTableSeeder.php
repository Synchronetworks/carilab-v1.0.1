<?php

namespace Modules\Appointment\database\seeders;

use Illuminate\Database\Seeder;

class AppointmentPackageMappingTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('appointment_package_mapping')->delete();
        
        \DB::table('appointment_package_mapping')->insert(array (
            0 => 
            array (
                'id' => 1,
                'appointment_id' => 1,
                'package_id' => 1,
                'test_id' => 4,
                'price' => 30.0,
                'status' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:23:11',
                'updated_at' => '2025-03-19 15:23:11',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'appointment_id' => 1,
                'package_id' => 1,
                'test_id' => 9,
                'price' => 60.0,
                'status' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:23:11',
                'updated_at' => '2025-03-19 15:23:11',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'appointment_id' => 1,
                'package_id' => 1,
                'test_id' => 7,
                'price' => 70.0,
                'status' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:23:11',
                'updated_at' => '2025-03-19 15:23:11',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'appointment_id' => 1,
                'package_id' => 1,
                'test_id' => 11,
                'price' => 80.0,
                'status' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:23:11',
                'updated_at' => '2025-03-19 15:23:11',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'appointment_id' => 2,
                'package_id' => 9,
                'test_id' => 4,
                'price' => 30.0,
                'status' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:41:20',
                'updated_at' => '2025-03-19 15:41:20',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'appointment_id' => 2,
                'package_id' => 9,
                'test_id' => 7,
                'price' => 70.0,
                'status' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:41:20',
                'updated_at' => '2025-03-19 15:41:20',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'appointment_id' => 2,
                'package_id' => 9,
                'test_id' => 14,
                'price' => 120.0,
                'status' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:41:20',
                'updated_at' => '2025-03-19 15:41:20',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'appointment_id' => 2,
                'package_id' => 9,
                'test_id' => 11,
                'price' => 80.0,
                'status' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:41:20',
                'updated_at' => '2025-03-19 15:41:20',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'appointment_id' => 5,
                'package_id' => 7,
                'test_id' => 60,
                'price' => 55.0,
                'status' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:49:41',
                'updated_at' => '2025-03-19 15:49:41',
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'appointment_id' => 5,
                'package_id' => 7,
                'test_id' => 40,
                'price' => 60.0,
                'status' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:49:41',
                'updated_at' => '2025-03-19 15:49:41',
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'appointment_id' => 5,
                'package_id' => 7,
                'test_id' => 50,
                'price' => 40.0,
                'status' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:49:41',
                'updated_at' => '2025-03-19 15:49:41',
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'appointment_id' => 5,
                'package_id' => 7,
                'test_id' => 35,
                'price' => 50.0,
                'status' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:49:41',
                'updated_at' => '2025-03-19 15:49:41',
                'deleted_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'appointment_id' => 6,
                'package_id' => 10,
                'test_id' => 38,
                'price' => 40.0,
                'status' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:51:04',
                'updated_at' => '2025-03-19 15:51:04',
                'deleted_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'appointment_id' => 6,
                'package_id' => 10,
                'test_id' => 43,
                'price' => 45.0,
                'status' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:51:04',
                'updated_at' => '2025-03-19 15:51:04',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}