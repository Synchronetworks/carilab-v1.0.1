<?php

namespace Modules\Prescription\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Prescription\Models\Prescription;
use Modules\MenuBuilder\Models\MenuBuilder;

class PrescriptionDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PrescriptionsTableSeeder::class);
        $this->call(PrescriptionLabMappingTableSeeder::class);
        $this->call(PrescriptionTestMappingTableSeeder::class);
    }
}
