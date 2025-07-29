<?php

namespace Modules\Prescription\database\seeders;

use Illuminate\Database\Seeder;

class PrescriptionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('prescriptions')->delete();
        
        \DB::table('prescriptions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 3,
                'uploaded_at' => '2025-03-19',
                'note' => NULL,
                'prescription_status' => 0,
                'is_notify' => 1,
                'status' => 1,
                'created_by' => 3,
                'updated_by' => 3,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:29:15',
                'updated_at' => '2025-03-19 15:29:15',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 10,
                'uploaded_at' => '2025-03-19',
                'note' => NULL,
                'prescription_status' => 0,
                'is_notify' => 1,
                'status' => 1,
                'created_by' => 3,
                'updated_by' => 3,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:29:28',
                'updated_at' => '2025-03-19 15:29:28',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'user_id' => 12,
                'uploaded_at' => '2025-03-19',
                'note' => NULL,
                'prescription_status' => 0,
                'is_notify' => 1,
                'status' => 1,
                'created_by' => 3,
                'updated_by' => 3,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:29:46',
                'updated_at' => '2025-03-19 15:29:46',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'user_id' => 14,
                'uploaded_at' => '2025-03-19',
                'note' => NULL,
                'prescription_status' => 0,
                'is_notify' => 1,
                'status' => 1,
                'created_by' => 3,
                'updated_by' => 3,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:29:58',
                'updated_at' => '2025-03-19 15:29:58',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'user_id' => 21,
                'uploaded_at' => '2025-03-19',
                'note' => NULL,
                'prescription_status' => 0,
                'is_notify' => 1,
                'status' => 1,
                'created_by' => 21,
                'updated_by' => 21,
                'deleted_by' => NULL,
                'created_at' => '2025-03-19 15:32:34',
                'updated_at' => '2025-03-19 15:32:34',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}