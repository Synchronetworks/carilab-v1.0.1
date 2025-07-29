<?php

namespace Modules\Prescription\database\seeders;

use Illuminate\Database\Seeder;

class PrescriptionLabMappingTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('prescription_lab_mapping')->delete();
        
        \DB::table('prescription_lab_mapping')->insert(array (
            0 => 
            array (
                'id' => 1,
                'prescription_id' => 1,
                'test_id' => NULL,
                'lab_id' => 3,
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
                'prescription_id' => 2,
                'test_id' => NULL,
                'lab_id' => 1,
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
                'prescription_id' => 3,
                'test_id' => NULL,
                'lab_id' => 2,
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
                'prescription_id' => 4,
                'test_id' => NULL,
                'lab_id' => 4,
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
                'prescription_id' => 5,
                'test_id' => NULL,
                'lab_id' => 7,
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