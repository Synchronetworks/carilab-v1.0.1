<?php

namespace Modules\Appointment\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Appointment\Models\Appointment;
use Modules\MenuBuilder\Models\MenuBuilder;

class AppointmentDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AppointmentStatusTableSeeder::class);
        $this->call(AppointmentsTableSeeder::class);
        $this->call(AppointmentCollectorMappingTableSeeder::class);
        $this->call(AppointmentPackageMappingTableSeeder::class);
        $this->call(AppointmentTransactionTableSeeder::class);
        $this->call(CashPaymentHistoriesTableSeeder::class);
        $this->call(AppointmentActivitiesTableSeeder::class);
    }
}
