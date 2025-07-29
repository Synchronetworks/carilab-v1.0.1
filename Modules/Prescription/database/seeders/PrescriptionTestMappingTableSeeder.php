<?php

namespace Modules\Prescription\database\seeders;

use Illuminate\Database\Seeder;

class PrescriptionTestMappingTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('prescription_test_mapping')->delete();
        
        
        
    }
}