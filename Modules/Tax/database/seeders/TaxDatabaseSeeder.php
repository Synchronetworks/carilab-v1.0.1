<?php

namespace Modules\Tax\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Tax\Models\Tax;
use Modules\MenuBuilder\Models\MenuBuilder;
use Carbon\Carbon;

class TaxDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('taxes')->delete();
        
        \DB::table('taxes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'Service Tax',
                'type' => 'Percentage',
                'value' => 5,
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
               
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'Home Collection Fee',
                'type' => 'Fixed',
                'value' => 10,
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
              
            ),
            2 => 
            array (
                'id' => 3,
                'title' => 'State Health Tax',
                'type' => 'Percentage',
                'value' => 2,
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
        ));
    }
}
