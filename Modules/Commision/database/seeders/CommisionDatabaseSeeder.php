<?php

namespace Modules\Commision\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Commision\Models\Commision;
use Modules\MenuBuilder\Models\MenuBuilder;

class CommisionDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CommissionEarningsTableSeeder::class);
    }
}
