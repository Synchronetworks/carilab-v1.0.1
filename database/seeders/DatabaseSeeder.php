<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('cache:clear');
        Schema::disableForeignKeyConstraints();
        $file = new Filesystem;
        $file->cleanDirectory('storage/app/public');
        $this->call(\Modules\World\database\seeders\WorldDatabaseSeeder::class);
        $this->call(AuthTableSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(UserProfilesSeeder::class);
        $this->call(\Modules\Category\database\seeders\CategoryDatabaseSeeder::class);
        $this->call(\Modules\Lab\database\seeders\LabDatabaseSeeder::class);
        $this->call(\Modules\Tax\database\seeders\TaxDatabaseSeeder::class);
        $this->call(\Modules\CatlogManagement\database\seeders\CatlogManagementDatabaseSeeder::class);
        $this->call(\Modules\PackageManagement\database\seeders\PackageManagementDatabaseSeeder::class);
        $this->call(\Modules\Constant\database\seeders\ConstantDatabaseSeeder::class);
        $this->call(\Modules\Subscriptions\database\seeders\PlanTableSeeder::class);
        $this->call(\Modules\Subscriptions\database\seeders\PlanlimitationTableSeeder::class);
        $this->call(\Modules\Subscriptions\database\seeders\SubscriptionsTableSeeder::class);
        $this->call(\Modules\Subscriptions\database\seeders\PlanlimitationMappingTableSeeder::class);
        $this->call(\Modules\Subscriptions\database\seeders\SubscriptionsTransactionsTableSeeder::class);
        $this->call(\Modules\NotificationTemplate\database\seeders\NotificationTemplateSeeder::class);
        $this->call(\Modules\FAQ\database\seeders\FAQDatabaseSeeder::class);
        $this->call(\Modules\Page\database\seeders\PageDatabaseSeeder::class);
        $this->call(\Modules\Document\database\seeders\DocumentDatabaseSeeder::class);
        $this->call(\Modules\Vendor\database\seeders\VendorDocumentSeeder::class);
        $this->call(\Modules\Collector\database\seeders\CollectorDocumentSeeder::class);
        $this->call(\Modules\Coupon\database\seeders\CouponDatabaseSeeder::class);
        $this->call(\Modules\Prescription\database\seeders\PrescriptionDatabaseSeeder::class);
        $this->call(\Modules\Appointment\database\seeders\AppointmentDatabaseSeeder::class);
        $this->call(\Modules\Commision\database\seeders\CommisionDatabaseSeeder::class);

        Schema::enableForeignKeyConstraints();
        Artisan::call('cache:clear');

        Artisan::call('cache:clear');
        Artisan::call('storage:link');

        
    }
}

